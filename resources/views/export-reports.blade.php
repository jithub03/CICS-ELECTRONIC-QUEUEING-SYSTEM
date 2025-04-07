<x-layouts.app>
    <div class="container mx-auto px-4 py-8 dark:bg-gray-900">
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
            <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6 gap-4">
                <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Export Reports</h1>
            </div>

            <!-- Export Reports Section -->
            <div class="mt-4">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="bg-white dark:bg-gray-700 p-6 rounded-lg shadow">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Standard Report</h3>
                        <p class="text-gray-600 dark:text-gray-300 mb-4">Export a summary report with queue counts, metrics, and feedback data.</p>
                        
                        <form action="{{ route('reports.export.csv') }}" method="get" class="space-y-4">
                            <div>
                                <label for="inquiry_type" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Inquiry Type</label>
                                <select id="inquiry_type" name="inquiry_type" class="block w-full appearance-none bg-white dark:bg-gray-700 text-gray-900 dark:text-white border border-gray-400 px-4 py-2 pr-8 rounded shadow leading-tight focus:outline-none focus:shadow-outline">
                                    <option value="all">All Inquiry Types</option>
                                    @foreach($inquiryTypes as $type)
                                        <option value="{{ $type }}">{{ $type }}</option>
                                    @endforeach
                                </select>
                            </div>
                            
                            <div>
                                <label for="window_number" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Window Number</label>
                                <select id="window_number" name="window_number" class="block w-full appearance-none bg-white dark:bg-gray-700 text-gray-900 dark:text-white border border-gray-400 px-4 py-2 pr-8 rounded shadow leading-tight focus:outline-none focus:shadow-outline">
                                    <option value="all">All Windows</option>
                                    @foreach($officeWindows as $window)
                                        <option value="{{ $window }}">Window {{ $window }}</option>
                                    @endforeach
                                </select>
                            </div>
                            
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label for="start_date" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Start Date</label>
                                    <input type="date" id="start_date" name="start_date" value="{{ $startDate }}" class="block w-full appearance-none bg-white dark:bg-gray-700 text-gray-900 dark:text-white border border-gray-400 px-4 py-2 rounded shadow leading-tight focus:outline-none focus:shadow-outline">
                                </div>
                                <div>
                                    <label for="end_date" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">End Date</label>
                                    <input type="date" id="end_date" name="end_date" value="{{ $endDate }}" class="block w-full appearance-none bg-white dark:bg-gray-700 text-gray-900 dark:text-white border border-gray-400 px-4 py-2 rounded shadow leading-tight focus:outline-none focus:shadow-outline">
                                </div>
                            </div>
                            
                            <div class="flex gap-4">
                                <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700 transition-colors">
                                    Export as CSV
                                </button>
                                <a href="{{ route('reports.export.json') }}" onclick="updateQueryParams(this); return false;" class="px-4 py-2 bg-gray-600 text-white rounded hover:bg-gray-700 transition-colors">
                                    Export as JSON
                                </a>
                            </div>
                        </form>
                    </div>
                    
                    <div class="bg-white dark:bg-gray-700 p-6 rounded-lg shadow">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Weekly Detailed Report</h3>
                        <p class="text-gray-600 dark:text-gray-300 mb-4">Export a detailed report with individual queue entries and feedback.</p>
                        
                        <form action="{{ route('reports.export.weekly.csv') }}" method="get" class="space-y-4">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label for="weekly_start_date" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Start Date</label>
                                    <input type="date" id="weekly_start_date" name="start_date" value="{{ $startDate }}" class="block w-full appearance-none bg-white dark:bg-gray-700 text-gray-900 dark:text-white border border-gray-400 px-4 py-2 rounded shadow leading-tight focus:outline-none focus:shadow-outline">
                                </div>
                                <div>
                                    <label for="weekly_end_date" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">End Date</label>
                                    <input type="date" id="weekly_end_date" name="end_date" value="{{ $endDate }}" class="block w-full appearance-none bg-white dark:bg-gray-700 text-gray-900 dark:text-white border border-gray-400 px-4 py-2 rounded shadow leading-tight focus:outline-none focus:shadow-outline">
                                </div>
                            </div>
                            
                            <div>
                                <button type="submit" class="px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700 transition-colors">
                                    Export Weekly Report
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        function updateQueryParams(link) {
            const form = link.closest('form');
            const url = new URL(link.href);
            
            // Add form fields to URL
            const formData = new FormData(form);
            for (const [key, value] of formData.entries()) {
                url.searchParams.set(key, value);
            }
            
            // Navigate to the updated URL
            window.location.href = url.toString();
        }
    </script>
</x-layouts.app>

