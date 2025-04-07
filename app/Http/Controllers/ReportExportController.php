<?php

namespace App\Http\Controllers;

use App\Services\ReportService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ReportExportController extends Controller
{
    protected $reportService;
    
    public function __construct(ReportService $reportService)
    {
        $this->reportService = $reportService;
    }
    
    public function exportCsv(Request $request)
    {
        try {
            $inquiryType = $request->input('inquiry_type', 'all');
            $windowNumber = $request->input('window_number', 'all');
            $startDate = $request->input('start_date') 
                ? Carbon::parse($request->input('start_date'))->startOfDay() 
                : Carbon::today()->startOfDay();
            $endDate = $request->input('end_date') 
                ? Carbon::parse($request->input('end_date'))->endOfDay() 
                : Carbon::today()->endOfDay();
            
            $report = $this->reportService->generateReport($inquiryType, $windowNumber, $startDate, $endDate);
            
            $filename = 'queue_report_' . date('Y-m-d') . '.csv';
            
            $headers = [
                "Content-type" => "text/csv",
                "Content-Disposition" => "attachment; filename=$filename",
                "Pragma" => "no-cache",
                "Cache-Control" => "must-revalidate, post-check=0, pre-check=0",
                "Expires" => "0"
            ];
            
            $callback = function() use ($report) {
                $file = fopen('php://output', 'w');
                
                // Report header
                fputcsv($file, ['Queue System Report']);
                fputcsv($file, ['Generated on', date('Y-m-d H:i:s')]);
                fputcsv($file, ['Period', $report['period']['start_date'] . ' to ' . $report['period']['end_date']]);
                fputcsv($file, ['Inquiry Type', $report['filters']['inquiry_type']]);
                fputcsv($file, ['Window Number', $report['filters']['window_number']]);
                fputcsv($file, []);
                
                // Queue Counts
                fputcsv($file, ['Queue Counts']);
                fputcsv($file, ['Period', 'Count']);
                fputcsv($file, ['Daily', $report['counts']['daily']]);
                fputcsv($file, ['Weekly', $report['counts']['weekly']]);
                fputcsv($file, ['Monthly', $report['counts']['monthly']]);
                fputcsv($file, []);
                
                // Key Metrics
                fputcsv($file, ['Key Metrics']);
                fputcsv($file, ['Metric', 'Value']);
                fputcsv($file, ['Served Clients', $report['metrics']['served']]);
                fputcsv($file, ['Unserved Clients', $report['metrics']['unserved']]);
                fputcsv($file, ['Service Rate (%)', $report['metrics']['service_rate']]);
                fputcsv($file, []);
                
                // Feedback Data
                if ($report['feedback']['hasData']) {
                    fputcsv($file, ['Feedback Reactions']);
                    fputcsv($file, ['Reaction', 'Count']);
                    foreach ($report['feedback']['data'] as $item) {
                        fputcsv($file, [$item[0], $item[1]]);
                    }
                }
                
                fclose($file);
            };
            
            return new StreamedResponse($callback, 200, $headers);
        } catch (\Exception $e) {
            Log::error('Error exporting CSV: ' . $e->getMessage(), [
                'exception' => $e,
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'error' => 'Failed to export CSV: ' . $e->getMessage()
            ], 500);
        }
    }
    
    public function exportJson(Request $request)
    {
        try {
            $inquiryType = $request->input('inquiry_type', 'all');
            $windowNumber = $request->input('window_number', 'all');
            $startDate = $request->input('start_date') 
                ? Carbon::parse($request->input('start_date'))->startOfDay() 
                : Carbon::today()->startOfDay();
            $endDate = $request->input('end_date') 
                ? Carbon::parse($request->input('end_date'))->endOfDay() 
                : Carbon::today()->endOfDay();
            
            $report = $this->reportService->generateReport($inquiryType, $windowNumber, $startDate, $endDate);
            
            return response()->json($report);
        } catch (\Exception $e) {
            Log::error('Error exporting JSON: ' . $e->getMessage(), [
                'exception' => $e,
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'error' => 'Failed to export JSON: ' . $e->getMessage()
            ], 500);
        }
    }
    
    public function exportWeeklyCsv(Request $request)
    {
        try {
            // Get date range parameters or use default (current week)
            $startDate = $request->input('start_date') 
                ? Carbon::parse($request->input('start_date'))->startOfDay() 
                : Carbon::now()->startOfWeek()->startOfDay();
                
            $endDate = $request->input('end_date') 
                ? Carbon::parse($request->input('end_date'))->endOfDay() 
                : Carbon::now()->endOfWeek()->endOfDay();
            
            // Format dates for the report title
            $startDateFormatted = $startDate->format('d/m/y');
            $endDateFormatted = $endDate->format('d/m/y');
            
            $filename = 'CICS_ELECTRONIC_QUEUE_WEEKLY_REPORT_' . $startDate->format('Y-m-d') . '_to_' . $endDate->format('Y-m-d') . '.csv';
            
            $headers = [
                "Content-type" => "text/csv",
                "Content-Disposition" => "attachment; filename=$filename",
                "Pragma" => "no-cache",
                "Cache-Control" => "must-revalidate, post-check=0, pre-check=0",
                "Expires" => "0"
            ];
            
            $callback = function() use ($startDate, $endDate, $startDateFormatted, $endDateFormatted) {
                $file = fopen('php://output', 'w');
                
                // Report title
                fputcsv($file, ["CICS ELECTRONIC QUEUE WEEKLY REPORT ($startDateFormatted - $endDateFormatted)"]);
                fputcsv($file, []); // Empty row
                
                // Column headers - updated to match the request
                fputcsv($file, ['ID', 'Name', 'Inquiry Type', 'Joined Queue', 'Feedback Created', 'Feedback_Rating', 'Feedback Comments']);
                
                // Get report data directly with a query to ensure we use the correct columns
                $reportData = DB::table('queues')
                    ->leftJoin('feedback', 'queues.id', '=', 'feedback.id')
                    ->select(
                        'queues.id',
                        'queues.full_name',
                        'queues.inquiry_type',
                        'queues.created_at as joined_queue', // Using created_at from queues table
                        'feedback.created_at as feedback_created', // Using created_at from feedback table
                        'feedback.reaction as feedback_rating',
                        'feedback.comments as feedback_comments',
                        'queues.status'
                    )
                    ->whereBetween('queues.created_at', [$startDate, $endDate])
                    ->orderBy('queues.created_at')
                    ->get();
                
                // Add data rows
                $counter = 1;
                foreach ($reportData as $row) {
                    // Format the timestamps
                    $joinedQueue = Carbon::parse($row->joined_queue);
                    $joinedQueueFormatted = $joinedQueue->format('g:ia');
                    
                    // Handle feedback data based on status
                    if ($row->status !== 'approve') {
                        $feedbackCreatedFormatted = 'NULL';
                        $feedbackRating = 'NULL';
                        $feedbackComments = 'NULL';
                    } else {
                        $feedbackCreated = $row->feedback_created ? Carbon::parse($row->feedback_created) : null;
                        $feedbackCreatedFormatted = $feedbackCreated ? $feedbackCreated->format('g:ia') : 'NULL';
                        $feedbackRating = $row->feedback_rating ?? 'NULL';
                        $feedbackComments = $row->feedback_comments ?? 'NULL';
                    }
                    
                    $csvRow = [
                        $counter++,
                        $row->full_name ?: 'Anonymous',
                        $row->inquiry_type,
                        $joinedQueueFormatted,
                        $feedbackCreatedFormatted,
                        $feedbackRating,
                        $feedbackComments
                    ];
                    fputcsv($file, $csvRow);
                }
                
                fclose($file);
            };
            
            return new StreamedResponse($callback, 200, $headers);
        } catch (\Exception $e) {
            Log::error('Error exporting weekly CSV: ' . $e->getMessage(), [
                'exception' => $e,
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'error' => 'Failed to export CSV: ' . $e->getMessage()
            ], 500);
        }
    }
}

