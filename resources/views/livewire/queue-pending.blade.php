<div>
    <!-- Queue Pending -->
    @if ($queueStatus !== 'process')
        <!-- Only show if the status is not 'process' -->
        <x-card title="You're in the Queue!" subtitle="We'll notify you when it's your turn." shadow separator>
            <p class="text-xl font-bold mt-2 text-center">Your Queue Number: {{ $queue->id }}</p>
            <x-button label="Ok" link="/" no-wire-navigate class="btn-primary w-full mt-4" />
        </x-card>
    @endif
</div>
