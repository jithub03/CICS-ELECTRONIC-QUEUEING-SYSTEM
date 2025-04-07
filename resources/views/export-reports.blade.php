<x-layouts.app>
    <!-- HEADER -->
    <x-header title="Export Reports" separator>
        <x-slot:middle class="!justify-end">
            <x-theme-toggle class="btn btn-circle" />
        </x-slot:middle>
    </x-header>

    <!-- CONTENT -->
    <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
        <!-- Standard Report -->
        <x-card class="p-6">
            <h3 class="text-lg font-semibold">Standard Report</h3>
            <p class="text-base-content/70 mb-4">Export a summary report with queue counts, metrics, and feedback data.</p>

            <form action="{{ route('reports.export.csv') }}" method="get" class="space-y-4">
                <!-- Inquiry Type -->
                <div>
                    <label for="inquiry_type" class="block text-sm font-medium mb-1">Inquiry Type</label>
                    <select id="inquiry_type" name="inquiry_type" class="select select-bordered w-full">
                        <option value="all">All Inquiry Types</option>
                        @foreach($inquiryTypes as $type)
                            <option value="{{ $type }}">{{ $type }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Window Number -->
                <div>
                    <label for="window_number" class="block text-sm font-medium mb-1">Window Number</label>
                    <select id="window_number" name="window_number" class="select select-bordered w-full">
                        <option value="all">All Windows</option>
                        @foreach($officeWindows as $window)
                            <option value="{{ $window }}">Window {{ $window }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Dates -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label for="start_date" class="block text-sm font-medium mb-1">Start Date</label>
                        <input type="date" id="start_date" name="start_date" value="{{ $startDate }}" class="input input-bordered w-full" />
                    </div>
                    <div>
                        <label for="end_date" class="block text-sm font-medium mb-1">End Date</label>
                        <input type="date" id="end_date" name="end_date" value="{{ $endDate }}" class="input input-bordered w-full" />
                    </div>
                </div>

                <!-- Buttons -->
                <div class="flex gap-4">
                    <button type="submit" class="btn btn-primary">
                        Export as CSV
                    </button>
                    <a href="{{ route('reports.export.json') }}" onclick="updateQueryParams(this); return false;" class="btn btn-secondary">
                        Export as JSON
                    </a>
                </div>
            </form>
        </x-card>

        <!-- Weekly Report -->
        <x-card class="p-6">
            <h3 class="text-lg font-semibold">Weekly Detailed Report</h3>
            <p class="text-base-content/70 mb-4">Export a detailed report with individual queue entries and feedback.</p>

            <form action="{{ route('reports.export.weekly.csv') }}" method="get" class="space-y-4">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label for="weekly_start_date" class="block text-sm font-medium mb-1">Start Date</label>
                        <input type="date" id="weekly_start_date" name="start_date" value="{{ $startDate }}" class="input input-bordered w-full" />
                    </div>
                    <div>
                        <label for="weekly_end_date" class="block text-sm font-medium mb-1">End Date</label>
                        <input type="date" id="weekly_end_date" name="end_date" value="{{ $endDate }}" class="input input-bordered w-full" />
                    </div>
                </div>

                <button type="submit" class="btn btn-success">
                    Export Weekly Report
                </button>
            </form>
        </x-card>
    </div>

    <script>
        function updateQueryParams(link) {
            const form = link.closest('form');
            const url = new URL(link.href);
            const formData = new FormData(form);
            for (const [key, value] of formData.entries()) {
                url.searchParams.set(key, value);
            }
            window.location.href = url.toString();
        }
    </script>
</x-layouts.app>
