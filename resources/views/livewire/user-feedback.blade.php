@php
    $inquiry_type = [
        ['value' => '', 'name' => 'Select Inquiry Type', 'disabled' => true],
        ['value' => 'Academic', 'name' => 'Academic (Academic Advising, Course Crediting or Concerns)'],
        ['value' => 'Complaints', 'name' => 'Complaints (Concerns regarding Department)'],
        [
            'value' => 'Document Handling',
            'name' => 'Document Handling (Request for or Submission of Academic Documents)',
        ],
        ['value' => 'Financial', 'name' => 'Financial (Fee Concerns)'],
        ['value' => 'Other', 'name' => 'Other (Please specify in the next section)'],
    ];
@endphp

<x-card title="How was your experience?" subtitle="Your feedback helps us improve our service" shadow separator>
    <form wire:submit.prevent="submitFeedback" class="space-y-4">
        <label class="block font-semibold">Reaction</label>
        <div class="flex gap-3">
            <button type="button" wire:click="$set('reaction', 'angry')"
                class="p-3 rounded text-3xl leading-none {{ $reaction === 'angry' ? 'bg-red-500' : 'bg-gray-200' }}">😡
                <span class="text-base">Angry</span></button>
            <button type="button" wire:click="$set('reaction', 'sad')"
                class="p-3 rounded text-3xl leading-none {{ $reaction === 'sad' ? 'bg-blue-500' : 'bg-gray-200' }}">😟
                <span class="text-base">Sad</span></button>
            <button type="button" wire:click="$set('reaction', 'neutral')"
                class="p-3 rounded text-3xl leading-none {{ $reaction === 'neutral' ? 'bg-gray-500' : 'bg-gray-200' }}">😐
                <span class="text-base">Neutral</span></button>
                
            <button type="button" wire:click="$set('reaction', 'happy')"
                class="p-3 rounded text-3xl leading-none {{ $reaction === 'happy' ? 'bg-green-500' : 'bg-gray-200' }}">🙂
                <span class="text-base">Happy</span></button>

                <button type="button" wire:click="$set('reaction', 'very_happy')"
    class="p-3 rounded text-3xl leading-none flex flex-col items-center justify-center w-24 h-24 text-center {{ $reaction === 'very_happy' ? 'bg-yellow-500' : 'bg-gray-200' }}">
    😀
    <span class="text-sm mt-1 break-words">Very Happy</span></button>

        </div>

        <x-input label="Full Name" wire:model="full_name" :disabled="$anonymous" />

        <label class="block font-semibold text-sm">Inquiry Type</label>
        <x-select required :options="$inquiry_type" option-value="value" option-label="name" wire:model="inquiry_type" />

        <x-textarea label="Additional comments (optional)" wire:model="comments"
            placeholder="Tell us more about your experience..." hint="Max 1000 chars" rows="5" inline />

        <x-checkbox label="Submit as Anonymous" wire:model="anonymous" />

        <x-button label="Submit Feedback" class="btn-primary" type="submit" spinner="submitFeedback" />

        @if (session()->has('message'))
            <p class="mt-2 text-green-500">{{ session('message') }}</p>
        @endif
    </form>
</x-card>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        Livewire.on('redirectAfterDelay', function() {
            setTimeout(() => {
                window.location.href = '/'; // Redirect to homepage
            }, 5000); // 5-second delay
        });
    });
</script>
