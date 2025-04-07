@extends('layouts.app')

@section('content')
<div class="bg-white rounded-lg shadow p-6">
    <h2 class="text-xl font-bold mb-4">Queue Reports</h2>
    
    <div class="border-t border-gray-200 pt-4">
        <h3 class="text-xl font-bold mb-4">Generate Reports</h3>
        
        <a href="{{ route('reports.weekly-csv') }}" class="inline-block px-4 py-2 bg-blue-600 text-white font-medium rounded hover:bg-blue-700 mb-6">
            Generate Weekly CSV Report
        </a>
        
        <h3 class="text-xl font-bold mb-4">Queue Report Generator</h3>
        
        <form method="GET" action="{{ route('reports.weekly-csv') }}" class="space-y-6">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <select name="date_range" id="date_range" class="w-full px-3 py-2 border border-gray-300 rounded-md" onchange="updateDateRange(this.value)">
                        <option value="this_week">This Week</option>
                        <option value="last_week">Last Week</option>
                        <option value="this_month">This Month</option>
                        <option value="custom">Custom Range</option>
                    </select>
                </div>
                
                <div>
                    <label for="start_date" class="block text-sm font-medium text-gray-700 mb-1">Start Date</label>
                    <input type="date" id="start_date" name="start_date" class="w-full px-3 py-2 border border-gray-300 rounded-md" value="{{ \Carbon\Carbon::now()->startOfWeek()->format('Y-m-d') }}">
                </div>
                
                <div>
                    <label for="end_date" class="block text-sm font-medium text-gray-700 mb-1">End Date</label>
                    <input type="date" id="end_date" name="end_date" class="w-full px-3 py-2 border border-gray-300 rounded-md" value="{{ \Carbon\Carbon::now()->endOfWeek()->format('Y-m-d') }}">
                </div>
            </div>
            
            <div class="flex justify-center">
                <button type="submit" class="flex flex-col items-center justify-center w-48 h-48 bg-gray-200 border-2 border-gray-300 rounded-lg hover:bg-gray-300 transition-colors">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-16 w-16 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                    </svg>
                    <span class="mt-2 text-gray-700">Download CSV Report</span>
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function updateDateRange(range) {
    const startDateInput = document.getElementById('start_date');
    const endDateInput = document.getElementById('end_date');
    
    const today = new Date();
    let startDate = new Date();
    let endDate = new Date();
    
    switch(range) {
        case 'this_week':
            startDate = new Date(today.setDate(today.getDate() - today.getDay())); // Start of current week (Sunday)
            endDate = new Date(today.setDate(today.getDate() + 6)); // End of current week (Saturday)
            break;
        case 'last_week':
            startDate = new Date(today.setDate(today.getDate() - today.getDay() - 7)); // Start of last week
            endDate = new Date(today.setDate(today.getDate() + 6)); // End of last week
            break;
        case 'this_month':
            startDate = new Date(today.getFullYear(), today.getMonth(), 1); // Start of current month
            endDate = new Date(today.getFullYear(), today.getMonth() + 1, 0); // End of current month
            break;
        case 'custom':
            // Keep the current values
            return;
    }
    
    startDateInput.value = formatDate(startDate);
    endDateInput.value = formatDate(endDate);
}

function formatDate(date) {
    const year = date.getFullYear();
    const month = String(date.getMonth() + 1).padStart(2, '0');
    const day = String(date.getDate()).padStart(2, '0');
    return `${year}-${month}-${day}`;
}
</script>
@endsection

