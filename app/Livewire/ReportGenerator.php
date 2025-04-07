<?php

namespace App\Livewire;

use Livewire\Component;
use Carbon\Carbon;

class ReportGenerator extends Component
{
    public $startDate;
    public $endDate;
    public $selectedRange = 'this_week';
    
    public function mount()
    {
        $this->startDate = Carbon::now()->startOfWeek()->format('Y-m-d');
        $this->endDate = Carbon::now()->endOfWeek()->format('Y-m-d');
    }
    
    public function setCurrentWeek()
    {
        $this->startDate = Carbon::now()->startOfWeek()->format('Y-m-d');
        $this->endDate = Carbon::now()->endOfWeek()->format('Y-m-d');
        $this->selectedRange = 'this_week';
    }
    
    public function setPreviousWeek()
    {
        $this->startDate = Carbon::now()->subWeek()->startOfWeek()->format('Y-m-d');
        $this->endDate = Carbon::now()->subWeek()->endOfWeek()->format('Y-m-d');
        $this->selectedRange = 'last_week';
    }
    
    public function setCurrentMonth()
    {
        $this->startDate = Carbon::now()->startOfMonth()->format('Y-m-d');
        $this->endDate = Carbon::now()->endOfMonth()->format('Y-m-d');
        $this->selectedRange = 'this_month';
    }
    
    public function downloadReport()
    {
        return redirect()->route('reports.weekly-csv', [
            'start_date' => $this->startDate,
            'end_date' => $this->endDate
        ]);
    }
    
    public function render()
    {
        return view('livewire.report-generator');
    }
}

