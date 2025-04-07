<div>
    <!-- HEADER -->
    <x-header title="Queue Reports" separator>
        <x-slot:middle class="!justify-end">
            <div class="flex gap-3 items-center">
                <x-theme-toggle class="btn btn-circle" />
                <select wire:change="updateCounts" wire:model="inquiryTypeFilter"
                        class="select select-bordered w-full max-w-xs">
                    <option value="all">All Inquiry Types</option>
                    @foreach($inquiryTypes as $type)
                        <option value="{{ $type }}" {{ $inquiryTypeFilter === $type ? 'selected' : '' }}>
                            {{ $type }}
                        </option>
                    @endforeach
                </select>
            </div>
        </x-slot:middle>
    </x-header>

    <!-- Report Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        <x-card class="bg-blue-100 dark:bg-blue-900">
            <h2 class="text-xl font-semibold text-blue-800 dark:text-white mb-2">Daily Report</h2>
            <p class="text-white/80 dark:text-white/80">Total queues processed today</p>
            <div class="text-3xl font-bold text-blue-600 dark:text-white mt-2">{{ $dailyCount }}</div>
        </x-card>

        <x-card class="bg-green-100 dark:bg-green-900">
            <h2 class="text-xl font-semibold text-green-800 dark:text-white mb-2">Weekly Report</h2>
            <p class="text-white/80 dark:text-white/80">Total queues processed this week</p>
            <div class="text-3xl font-bold text-green-600 dark:text-white mt-2">{{ $weeklyCount }}</div>
        </x-card>

        <x-card class="bg-purple-100 dark:bg-purple-900">
            <h2 class="text-xl font-semibold text-purple-800 dark:text-white mb-2">Monthly Report</h2>
            <p class="text-white/80 dark:text-white/80">Total queues processed this month</p>
            <div class="text-3xl font-bold text-purple-600 dark:text-white mt-2">{{ $monthlyCount }}</div>
        </x-card>
    </div>

    <!-- Feedback Section -->
    <x-card class="mt-8">
        <h2 class="text-xl font-semibold mb-4">User Feedback Reactions</h2>

        @if(!$hasFeedback)
            <div class="text-center py-8">
                <svg class="mx-auto h-12 w-12 text-base-content/40" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                </svg>
                <h3 class="mt-2 text-sm font-medium">No Feedback Data</h3>
                <p class="mt-1 text-sm text-base-content/60">There are no feedback records in the system yet.</p>
            </div>
        @else
            <div class="p-4 bg-base-100 rounded-lg shadow">
                <h3 class="text-lg font-semibold mb-4">Reaction Distribution</h3>
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
                        <div class="flex items-center justify-between p-3 bg-base-200 rounded-lg">
                            <div class="flex items-center space-x-3">
                                <div class="w-8 h-8 rounded-full flex items-center justify-center"
                                     style="background-color: {{ $colors[$index] }}">
                                    <span class="text-white text-lg">{{ $reactions[$item[0]] }}</span>
                                </div>
                                <span class="font-medium">{{ ucwords(str_replace('_', ' ', $item[0])) }}</span>
                            </div>
                            <div class="text-right">
                                <span class="font-bold text-primary">{{ $item[1] }}</span>
                                <span class="text-sm text-base-content/70">({{ $percentage }}%)</span>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif
    </x-card>
</div>
