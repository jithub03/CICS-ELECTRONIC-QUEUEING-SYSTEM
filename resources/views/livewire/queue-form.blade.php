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

<div class="flex flex-col gap-3" wire:poll.5s="pollQueueStatus">
    {{-- <div class="flex flex-col gap-3"> --}}
    <x-card title="Current Serving" shadow separator>
        {{-- @if ($currentServing)
        <p class="mt-2 text-xl font-bold text-center">
            Queue Number: {{ $currentServing->id }}
        </p>
        @else
        <p class="mt-2 text-xl font-bold text-center text-gray-500">
            No one is being served currently.
        </p>
        @endif --}}

        <table class="table-auto border-collapse border border-gray-400 w-full text-center">
            <thead>
                <tr>
                    <td class="border border-gray-400 px-4 py-2">Window No:</td>
                    <td class="border border-gray-400 px-4 py-2">1</td>
                    <td class="border border-gray-400 px-4 py-2">2</td>
                    {{-- <td class="border border-gray-400 px-4 py-2">3</td> --}}
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td class="border border-gray-400 px-4 py-2">Queue No:</td>
                    @for ($i = 1; $i <= 2; $i++)
                        <td class="border border-gray-400 px-4 py-2">
                            @if (isset($windowQueues[$i]) && count($windowQueues[$i]) > 0)
                                {{ $windowQueues[$i][0]['id'] }}
                            @else
                                Available
                            @endif
                        </td>
                    @endfor
                </tr>
            </tbody>
        </table>


    </x-card>

    <x-card title="Welcome to UST CICS Support Queue" subtitle="Get assistance quickly and easily" shadow separator>
        <x-form wire:submit.prevent="save">
            <x-input label="Full Name" wire:model="full_name" required />

            <x-input type="number" label="Contact Number" wire:model="contact_number" required />

            <x-input label="Email Address" wire:model="email" required />

            <hr />

            <h3 class="font-bold">Notify Via</h3>
            <x-toggle label="SMS" wire:model="notify_sms" />
            <x-toggle label="Email" wire:model="notify_email" />

            <hr>

            <h3 class="font-bold">Inquiry Type</h3>
            <x-select required :options="$inquiry_type" option-value="value" option-label="name" wire:model="inquiry_type" />

            <hr>

            <x-textarea label="Inquiry Details" wire:model="inquiry_details"
                placeholder="Please describe your inquiry..." hint="Max 1000 chars" rows="5" required inline />

            <x-slot:actions>
                <x-theme-toggle class="btn btn-circle" />
                {{-- <x-button label="Admin" @click="$wire.modal = true" /> --}}
                <x-button label="Join Queue" class="btn-primary" type="submit" spinner="save" />
            </x-slot:actions>
        </x-form>

        <x-modal wire:model="modal" title="Enter PIN to Access Admin Panel">
            <div class="flex items-center justify-center mb-5">
                <x-pin wire:model="pin" size="4" numeric />
            </div>

            @error('pin')
                <p class="text-sm text-center text-red-500">{{ $message }}</p>
            @enderror

            <x-slot:actions>
                <x-button label="Cancel" @click="$wire.modal = false" />
                <x-button label="Enter PIN" class="btn-primary" type="button" wire:click="checkPin"
                    spinner="checkPin" />
            </x-slot:actions>
        </x-modal>
    </x-card>
</div>
