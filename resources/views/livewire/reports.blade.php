<div class="dark">
    <div class="container mx-auto px-4 py-8 dark:bg-gray-900">
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
            <div class="flex justify-between items-center mb-6">
                <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Queue Reports</h1>
                <div class="flex items-center space-x-4">
                    <x-theme-toggle class="btn btn-circle" />
                    <select wire:change="updateCounts" wire:model="inquiryTypeFilter"
                            class="block appearance-none bg-white dark:bg-gray-700 text-gray-900 dark:text-white border border-gray-400 px-4 py-2 pr-8 rounded shadow leading-tight focus:outline-none focus:shadow-outline">
                        <option value="all">All Inquiry Types</option>
                        @foreach($inquiryTypes as $type)
                            <option value="{{ $type }}" {{ $inquiryTypeFilter === $type ? 'selected' : '' }}>{{ $type }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
    
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <div class="bg-white dark:bg-blue-900 p-6 rounded-lg shadow">
                    <h2 class="text-xl font-semibold text-blue-800 dark:text-white mb-2">Daily Report</h2>
                    <p class="text-gray-600 dark:text-gray-300">Total queues processed today</p>
                    <div class="text-3xl font-bold text-blue-600 dark:text-white mt-2">{{ $dailyCount }}</div>
                </div>
    
                <div class="bg-white dark:bg-green-900 p-6 rounded-lg shadow">
                    <h2 class="text-xl font-semibold text-green-800 dark:text-white mb-2">Weekly Report</h2>
                    <p class="text-gray-600 dark:text-gray-300">Total queues processed this week</p>
                    <div class="text-3xl font-bold text-green-600 dark:text-white mt-2">{{ $weeklyCount }}</div>
                </div>
    
                <div class="bg-white dark:bg-purple-900 p-6 rounded-lg shadow">
                    <h2 class="text-xl font-semibold text-purple-800 dark:text-white mb-2">Monthly Report</h2>
                    <p class="text-gray-600 dark:text-gray-300">Total queues processed this month</p>
                    <div class="text-3xl font-bold text-purple-600 dark:text-white mt-2">{{ $monthlyCount }}</div>
                </div>
            </div>
        </div>
    
        <div class="mt-8 bg-white dark:bg-gray-800 rounded-lg shadow p-6">
            <h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-4">User Feedback Reactions</h2>
    
            @if(!$hasFeedback)
                <div class="text-center py-8">
                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                    </svg>
                    <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-white">No Feedback Data</h3>
                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-300">There are no feedback records in the system yet.</p>
                </div>
            @else
                <div class="p-4 bg-white dark:bg-gray-700 rounded-lg">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Reaction Distribution</h3>
                    <div class="space-y-4">
                        @php
                            $colors = ['#FF6B6B', '#4ECDC4', '#45B7D1', '#96CEB4', '#FFEEAD'];
                            $reactions = [
                                'angry' => '😡',
                                'sad' => '😟',
                                'neutral' => '😐',
                                'happy' => '🙂',
                                'very_happy' => '😀'
                            ];
                        @endphp
                        @foreach($feedbackReactions['series'][0]['data'] as $index => $item)
                            @php
                                $total = array_sum(array_column($feedbackReactions['series'][0]['data'], 1));
                                $percentage = $total ? round($item[1] / $total * 100) : 0;
                            @endphp
                            <div class="flex items-center justify-between p-3 bg-white dark:bg-gray-700 rounded-lg">
                                <div class="flex items-center space-x-3">
                                    <div class="w-8 h-8 rounded-full flex items-center justify-center" style="background-color: {{ $colors[$index] }}">
                                        <span class="text-white text-lg">{{ $reactions[$item[0]] }}</span>
                                    </div>
                                    <span class="font-medium text-gray-900 dark:text-white">{{ ucwords(str_replace('_', ' ', $item[0])) }}</span>
                                </div>
                                <div class="text-right">
                                    <span class="font-bold text-blue-600 dark:text-blue-400">{{ $item[1] }}</span>
                                    <span class="text-sm text-gray-600 dark:text-gray-300">({{ $percentage }}%)</span>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>
    </div>
    </div>
    