<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReportsController extends Controller
{
    /**
     * Show the report export form
     */
    public function exportForm()
    {
        // Get all unique inquiry types
        $inquiryTypes = DB::table('queues')
            ->select('inquiry_type')
            ->distinct()
            ->get()
            ->pluck('inquiry_type')
            ->map(function($type) {
                return trim($type);
            })
            ->toArray();
        
        return view('reports.export', compact('inquiryTypes'));
    }
}

