<div class="p-6 bg-white dark:bg-gray-800 rounded-lg shadow-md">
    <h2 class="text-2xl font-bold mb-6 text-gray-900 dark:text-white">Queue Report Generator</h2>
    
    <div class="mb-6">
        <div class="flex flex-wrap gap-2 mb-4">
            <button wire:click="setCurrentWeek" class="px-4 py-2 bg-blue-500 text-white rounded hover:bg-blue-600 {{ $selectedRange === 'this_week' ? 'bg-blue-700' : '' }}">
                Current Week
            </button>
            <button wire:click="setPreviousWeek" class="px-4 py-2 bg-blue-500 text-white rounded hover:bg-blue-600 {{ $selectedRange === 'last_week' ? 'bg-blue-700' : '' }}">
                Previous Week
            </button>
            <button wire:click="setCurrentMonth" class="px-4 py-2 bg-blue-500 text-white rounded hover:bg-blue-600 {{ $selectedRange === 'this_month' ? 'bg-blue-700' : '' }}">
                Current Month
            </button>
        </div>
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
            <div>
                <label for="start_date" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Start Date</label>
                <input 
                    wire:model="startDate" 
                    type="date" 
                    id="start_date" 
                    class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                >
            </div>
            <div>
                <label for="end_date" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">End Date</label>
                <input 
                    wire:model="endDate" 
                    type="date" 
                    id="end_date" 
                    class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                >
            </div>
        </div>
    </div>
    
    <div class="flex justify-end">
        <button 
            wire:click="downloadReport" 
            class="px-6 py-3 bg-green-600 text-white font-medium rounded-md hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500"
        >
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 inline-block mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
            </svg>
            Download CSV Report
        </button>
    </div>
</div>

