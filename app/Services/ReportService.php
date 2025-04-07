<?php

namespace App\Services;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

class ReportService
{
    /**
     * Get all unique inquiry types
     *
     * @return array
     */
    public function getInquiryTypes()
    {
        return Cache::remember('inquiry_types', 3600, function() {
            return DB::table('queues')
                ->select('inquiry_type')
                ->distinct()
                ->get()
                ->pluck('inquiry_type')
                ->map(fn($type) => trim($type))
                ->toArray();
        });
    }
    
    /**
     * Get all unique window numbers
     *
     * @return array
     */
    public function getWindowNumbers()
{
    return Cache::remember('window_numbers', 3600, function() {
        return DB::table('queues')
            ->select('window_number')
            ->whereIn('window_number', [1, 2]) // Only include these
            ->distinct()
            ->whereNotNull('window_number')
            ->get()
            ->pluck('window_number')
            ->toArray();
    });
}

    
    /**
     * Get all staff members
     *
     * @return array
     */
    public function getStaffMembers()
    {
        return Cache::remember('staff_members', 3600, function() {
            return DB::table('queues')
                ->select('staff_name')
                ->whereNotNull('staff_name')
                ->distinct()
                ->get()
                ->pluck('staff_name')
                ->toArray();
        });
    }
    
    /**
     * Get queue counts for different time periods
     *
     * @param string $inquiryType
     * @param string $windowNumber
     * @return array
     */
    public function getQueueCounts($inquiryType = 'all', $windowNumber = 'all')
    {
        $timezone = config('app.timezone', 'Asia/Shanghai');
        $today = Carbon::today()->setTimezone($timezone);
        $now = Carbon::now()->setTimezone($timezone);
        $startOfWeek = $now->copy()->startOfWeek();
        $startOfMonth = $now->copy()->startOfMonth();
        
        // Base query builder
        $baseQuery = function() use ($inquiryType, $windowNumber) {
            $query = DB::table('queues');
            if ($inquiryType !== 'all') {
                $query->where('inquiry_type', trim($inquiryType));
            }
            if ($windowNumber !== 'all') {
                $query->where('window_number', $windowNumber);
            }
            return $query;
        };
        
        // Get counts
        $dailyCount = $baseQuery()->whereDate('created_at', $today)->count();
        $weeklyCount = $baseQuery()->whereBetween('created_at', [
            $startOfWeek->format('Y-m-d 00:00:00'),
            $now->format('Y-m-d 23:59:59')
        ])->count();
        $monthlyCount = $baseQuery()->whereBetween('created_at', [
            $startOfMonth->format('Y-m-d 00:00:00'),
            $now->format('Y-m-d 23:59:59')
        ])->count();
        
        return [
            'daily' => $dailyCount,
            'weekly' => $weeklyCount,
            'monthly' => $monthlyCount
        ];
    }
    
    /**
     * Get feedback data
     *
     * @param string $inquiryType
     * @param string $windowNumber
     * @param Carbon $startDate
     * @param Carbon $endDate
     * @return array
     */
    public function getFeedbackData($inquiryType = 'all', $windowNumber = 'all', $startDate = null, $endDate = null)
    {
        try {
            // Define all possible reactions
            $reactions = [
                'angry' => 0,
                'sad' => 0,
                'neutral' => 0,
                'happy' => 0,
                'very_happy' => 0
            ];
            
            // Build query
            $query = DB::table('feedback');
            
            if ($inquiryType !== 'all') {
                $query->where('inquiry_type', trim($inquiryType));
            }
            
            if ($windowNumber !== 'all') {
                $query->where('window_number', $windowNumber);
            }
            
            if ($startDate && $endDate) {
                $query->whereBetween('created_at', [$startDate, $endDate]);
            }
            
            // Get reaction counts
            $reactionCounts = $query
                ->select('reaction', DB::raw('count(*) as count'))
                ->groupBy('reaction')
                ->get()
                ->mapWithKeys(fn($item) => [$item->reaction => $item->count]);
                
            // Update counts
            foreach ($reactions as $reaction => $count) {
                if (isset($reactionCounts[$reaction])) {
                    $reactions[$reaction] = $reactionCounts[$reaction];
                }
            }
            
            return [
                'hasData' => $reactionCounts->isNotEmpty(),
                'data' => array_map(function($reaction, $count) {
                    return [$reaction, $count];
                }, array_keys($reactions), array_values($reactions))
            ];
            
        } catch (\Exception $e) {
            Log::error('Error getting feedback data:', ['error' => $e->getMessage()]);
            return [
                'hasData' => false,
                'data' => []
            ];
        }
    }
    
    /**
     * Get average waiting time
     * 
     * @param string $inquiryType
     * @param string $windowNumber
     * @param Carbon $startDate
     * @param Carbon $endDate
     * @return int
     */
    public function getAverageWaitingTime($inquiryType = 'all', $windowNumber = 'all', $startDate = null, $endDate = null)
    {
        // Replace this with a calculation that doesn't use served_at
        // For example, we can use a fixed value or calculate based on status changes
        return 0; // Return a default value of 0 minutes
    }
    
    /**
     * Generate a report for export
     *
     * @param string $inquiryType
     * @param string $windowNumber
     * @param Carbon $startDate
     * @param Carbon $endDate
     * @return array
     */
    public function generateReport($inquiryType = 'all', $windowNumber = 'all', $startDate = null, $endDate = null)
    {
        if (!$startDate) $startDate = Carbon::today()->startOfDay();
        if (!$endDate) $endDate = Carbon::today()->endOfDay();
        
        $counts = $this->getQueueCounts($inquiryType, $windowNumber);
        $feedback = $this->getFeedbackData($inquiryType, $windowNumber, $startDate, $endDate);
        
        // Get served vs unserved (using status instead of served_at)
        $servedUnserved = DB::table('queues')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->when($inquiryType !== 'all', function($query) use ($inquiryType) {
                return $query->where('inquiry_type', trim($inquiryType));
            })
            ->when($windowNumber !== 'all', function($query) use ($windowNumber) {
                return $query->where('window_number', $windowNumber);
            })
            ->selectRaw('
                SUM(CASE WHEN status = "approve" THEN 1 ELSE 0 END) as served,
                SUM(CASE WHEN status != "approve" THEN 1 ELSE 0 END) as unserved
            ')
            ->first();
            
        return [
            'period' => [
                'start_date' => $startDate->format('Y-m-d'),
                'end_date' => $endDate->format('Y-m-d'),
            ],
            'filters' => [
                'inquiry_type' => $inquiryType,
                'window_number' => $windowNumber,
            ],
            'counts' => $counts,
            'metrics' => [
                'served' => $servedUnserved->served ?? 0,
                'unserved' => $servedUnserved->unserved ?? 0,
                'service_rate' => $servedUnserved && ($servedUnserved->served + $servedUnserved->unserved) > 0 
                    ? round(($servedUnserved->served / ($servedUnserved->served + $servedUnserved->unserved)) * 100, 1)
                    : 0,
                // Remove avg_wait_time from here
            ],
            'feedback' => $feedback,
        ];
    }
    
    /**
     * Get data for the weekly report
     *
     * @param Carbon $startDate
     * @param Carbon $endDate
     * @return \Illuminate\Support\Collection
     */
    public function getWeeklyReportData(Carbon $startDate, Carbon $endDate)
    {
        try {
            // Query to get queue data with feedback information
            // LEFT JOIN ensures we get all queue entries even if they don't have feedback
            $data = DB::table('queues')
                ->leftJoin('feedback', 'queues.id', '=', 'feedback.id')
                ->select(
                    'queues.id',
                    'queues.full_name',
                    'queues.inquiry_type',
                    'queues.created_at as joined_queue',
                    'feedback.created_at as feedback_created',
                    'feedback.reaction as feedback_rating',
                    'feedback.comments as feedback_comments',
                    'queues.status'
                )
                ->whereBetween('queues.created_at', [$startDate, $endDate])
                ->orderBy('queues.created_at')
                ->get();
            
            // Format the data
            return $data->map(function($item) {
                // Format the timestamps
                $joinedQueue = Carbon::parse($item->joined_queue);
                
                // IMPORTANT: If the queue was removed before being served (status is not 'approve'),
                // set feedback fields to NULL as per requirement
                if ($item->status !== 'approve') {
                    $item->feedback_created = null;
                    $item->feedback_rating = null;
                    $item->feedback_comments = null;
                    $item->feedback_created_formatted = 'NULL';
                } else {
                    $feedbackCreated = $item->feedback_created ? Carbon::parse($item->feedback_created) : null;
                    $item->feedback_created_formatted = $feedbackCreated ? $feedbackCreated->format('g:ia') : 'NULL';
                }
                
                // Add formatted time fields
                $item->joined_queue_formatted = $joinedQueue->format('g:ia');
                
                // Ensure feedback fields are NULL when appropriate
                if ($item->feedback_rating === null) {
                    $item->feedback_rating = 'NULL';
                }
                
                if ($item->feedback_comments === null) {
                    $item->feedback_comments = 'NULL';
                }
                
                return $item;
            });
        } catch (\Exception $e) {
            Log::error('Error generating weekly report: ' . $e->getMessage(), [
                'exception' => $e,
                'trace' => $e->getTraceAsString()
            ]);
            return collect([]);
        }
    }
}

