<?php

namespace App\Http\Controllers;

use App\Services\ReportService;
use Carbon\Carbon;
use Illuminate\Http\Request;

class ExportReportsController extends Controller
{
    protected $reportService;
    
    public function __construct(ReportService $reportService)
    {
        $this->reportService = $reportService;
    }
    
    public function index()
    {
        // Get all unique inquiry types
        $inquiryTypes = $this->reportService->getInquiryTypes();
        
        // Get all window numbers
        $officeWindows = $this->reportService->getWindowNumbers();
        
        // Default date range
        $startDate = Carbon::today()->format('Y-m-d');
        $endDate = Carbon::today()->format('Y-m-d');
        
        return view('export-reports', [
            'inquiryTypes' => $inquiryTypes,
            'officeWindows' => $officeWindows,
            'startDate' => $startDate,
            'endDate' => $endDate
        ]);
    }
}

