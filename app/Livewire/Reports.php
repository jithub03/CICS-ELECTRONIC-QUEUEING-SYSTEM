<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Queue;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class Reports extends Component
{
    public $dailyCount = 0;
    public $weeklyCount = 0;
    public $monthlyCount = 0;
    public $inquiryTypeFilter = 'all';
    public $dateRangeFilter = 'today';
    public $windowNumberFilter = 'all';
    public $startDate;
    public $endDate;
    public $inquiryTypes = [];
    public $officeWindows = [];
    public $hasFeedback = false;
    public $feedbackReactions = [
        'series' => [[
            'name' => 'Reactions',
            'data' => []
        ]]
    ];

    public function mount()
    {
        // Set default date range
        $this->startDate = Carbon::today()->format('Y-m-d');
        $this->endDate = Carbon::today()->format('Y-m-d');

        // Get feedback reactions data
        $feedbackData = DB::table('feedback')
            ->select('reaction', DB::raw('COUNT(*) as count'))
            ->groupBy('reaction')
            ->get();

        $this->hasFeedback = $feedbackData->isNotEmpty();

        if ($this->hasFeedback) {
            $this->feedbackReactions = [
                'series' => [[
                    'name' => 'Reactions',
                    'data' => $feedbackData->map(function($item) {
                        return [$item->reaction, $item->count];
                    })->toArray()
                ]]
            ];
        }

        // Get all unique inquiry types and ensure consistent formatting
        $this->inquiryTypes = DB::table('queues')
            ->select('inquiry_type')
            ->distinct()
            ->get()
            ->pluck('inquiry_type')
            ->map(function($type) {
                return trim($type);
            })
            ->toArray();
        
        // Get all window numbers
        $this->officeWindows = DB::table('queues')
            ->select('window_number')
            ->distinct()
            ->whereNotNull('window_number')
            ->get()
            ->pluck('window_number')
            ->toArray();
        
        $this->updateCounts();
    }

    public function updatedInquiryTypeFilter()
    {
        $this->updateCounts();
    }

    public function updatedOfficeWindowFilter()
    {
        $this->updateCounts();
    }

    public function updatedDateRangeFilter($value)
    {
        switch ($value) {
            case 'today':
                $this->startDate = Carbon::today()->format('Y-m-d');
                $this->endDate = Carbon::today()->format('Y-m-d');
                break;
            case 'yesterday':
                $this->startDate = Carbon::yesterday()->format('Y-m-d');
                $this->endDate = Carbon::yesterday()->format('Y-m-d');
                break;
            case 'this_week':
                $this->startDate = Carbon::now()->startOfWeek()->format('Y-m-d');
                $this->endDate = Carbon::now()->format('Y-m-d');
                break;
            case 'this_month':
                $this->startDate = Carbon::now()->startOfMonth()->format('Y-m-d');
                $this->endDate = Carbon::now()->format('Y-m-d');
                break;
            case 'last_month':
                $this->startDate = Carbon::now()->subMonth()->startOfMonth()->format('Y-m-d');
                $this->endDate = Carbon::now()->subMonth()->endOfMonth()->format('Y-m-d');
                break;
            case 'custom':
                // Keep existing dates
                break;
        }
        
        $this->updateCounts();
    }

    public function updatedStartDate()
    {
        $this->dateRangeFilter = 'custom';
        $this->updateCounts();
    }

    public function updatedEndDate()
    {
        $this->dateRangeFilter = 'custom';
        $this->updateCounts();
    }

    public function updateCounts()
    {
        $timezone = config('app.timezone', 'Asia/Shanghai');
        $startDate = Carbon::parse($this->startDate)->setTimezone($timezone)->startOfDay();
        $endDate = Carbon::parse($this->endDate)->setTimezone($timezone)->endOfDay();
        $today = Carbon::today()->setTimezone($timezone);
        $now = Carbon::now()->setTimezone($timezone);
        $startOfWeek = $now->copy()->startOfWeek();
        $startOfMonth = $now->copy()->startOfMonth();
    
        // Base query builder with filters
        $baseQuery = DB::table('queues');
        if ($this->inquiryTypeFilter !== 'all') {
            $baseQuery->where('inquiry_type', trim($this->inquiryTypeFilter));
        }
        if ($this->windowNumberFilter !== 'all') {
            $baseQuery->where('window_number', $this->windowNumberFilter);
        }
    
        // Daily count (today)
        $this->dailyCount = (clone $baseQuery)
            ->whereDate('created_at', $today)
            ->count();
    
        // Weekly count
        $this->weeklyCount = (clone $baseQuery)
            ->whereBetween('created_at', [
                $startOfWeek->format('Y-m-d 00:00:00'),
                $now->format('Y-m-d 23:59:59')
            ])
            ->count();
    
        // Monthly count
        $this->monthlyCount = (clone $baseQuery)
            ->whereBetween('created_at', [
                $startOfMonth->format('Y-m-d 00:00:00'),
                $now->format('Y-m-d 23:59:59')
            ])
            ->count();
    
        $this->updateFeedbackReactions();
    }

    public function updateFeedbackReactions()
    {
        try {
            // Get all possible reactions
            $reactions = [
                'angry' => 0,
                'sad' => 0,
                'neutral' => 0,
                'happy' => 0,
                'very_happy' => 0
            ];

            // Build query with filters
            $query = DB::table('feedback');
            
            if ($this->inquiryTypeFilter !== 'all') {
                $query->where('inquiry_type', trim($this->inquiryTypeFilter));
            }
            
            if ($this->windowNumberFilter !== 'all') {
                $query->where('window_number', $this->windowNumberFilter);
            }
            
            $startDate = Carbon::parse($this->startDate)->startOfDay();
            $endDate = Carbon::parse($this->endDate)->endOfDay();
            $query->whereBetween('created_at', [$startDate, $endDate]);

            // Get counts for each reaction
            $reactionCounts = $query
                ->select('reaction', DB::raw('count(*) as count'))
                ->groupBy('reaction')
                ->get()
                ->mapWithKeys(function($item) {
                    return [$item->reaction => $item->count];
                });

            // Update counts in our reactions array
            foreach ($reactions as $reaction => $count) {
                if (isset($reactionCounts[$reaction])) {
                    $reactions[$reaction] = $reactionCounts[$reaction];
                }
            }

            // Convert to format expected by the view
            $this->feedbackReactions = [
                'series' => [[
                    'name' => 'Reactions',
                    'data' => array_map(function($reaction, $count) {
                        return [$reaction, $count];
                    }, array_keys($reactions), array_values($reactions))
                ]]
            ];

            $this->hasFeedback = !empty(array_filter(array_values($reactions)));
        } catch (\Exception $e) {
            Log::error('Error updating feedback reactions:', ['error' => $e->getMessage()]);
            $this->feedbackReactions = [
                'series' => [[
                    'name' => 'Reactions',
                    'data' => []
                ]]
            ];
            $this->hasFeedback = false;
        }
    }

    public function render()
    {
        // Remove any references to served_at or avg_wait_time
        return view('livewire.reports', [
            'feedbackReactions' => $this->feedbackReactions,
            'hasFeedback' => $this->hasFeedback,
            'inquiryTypes' => $this->inquiryTypes,
            'officeWindows' => $this->officeWindows,
            'inquiryTypeFilter' => $this->inquiryTypeFilter,
            'officeWindowFilter' => $this->windowNumberFilter,
            'dateRangeFilter' => $this->dateRangeFilter,
            'startDate' => $this->startDate,
            'endDate' => $this->endDate,
            'dailyCount' => $this->dailyCount,
            'weeklyCount' => $this->weeklyCount,
            'monthlyCount' => $this->monthlyCount,
        ]);
    }
}

