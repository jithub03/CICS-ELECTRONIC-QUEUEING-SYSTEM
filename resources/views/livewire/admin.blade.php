<div>
    {{-- FULLSCREEN PASSCODE OVERLAY --}}
    @if (!$authenticated)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-gray-900/90 backdrop-blur-sm">
            <div class="w-[22rem] p-6 bg-base-100 dark:bg-gray-800 rounded-xl shadow-lg border border-base-300 dark:border-gray-700">
                <h2 class="text-xl font-semibold text-center text-gray-900 dark:text-white mb-4">
                    Enter Admin Passcode
                </h2>

                <form wire:submit.prevent="verifyPasscode" class="space-y-4">
                    <div>
                        <label for="passcode_input"
                            class="block text-sm font-medium text-gray-800 dark:text-gray-200 mb-1">
                            6-digit Passcode <span class="text-red-500">*</span>
                        </label>
                        <input
                            type="password"
                            id="passcode_input"
                            maxlength="6"
                            wire:model.defer="passcode_input"
                            required
                            class="input input-bordered w-full bg-white text-gray-900 dark:bg-gray-900 dark:text-white"
                        />
                    </div>

                    <x-button
                        label="Submit"
                        type="submit"
                        class="w-full btn-primary"
                        spinner
                    />
                </form>
            </div>
        </div>
    @else
        @php
            $inquiry_type = [
                ['value' => '', 'name' => 'Select Inquiry Type', 'disabled' => true],
                ['value' => 'Academic', 'name' => 'Academic (Academic Advising, Course Crediting or Concerns)'],
                ['value' => 'Complaints', 'name' => 'Complaints (Concerns regarding Department)'],
                ['value' => 'Document Handling', 'name' => 'Document Handling (Request for or Submission of Academic Documents)'],
                ['value' => 'Financial', 'name' => 'Financial (Fee Concerns)'],
                ['value' => 'Other', 'name' => 'Other (Please specify in the next section)'],
            ];

            $windows = [
                ['value' => 0, 'name' => 'Select window'],
                ['value' => 1, 'name' => 'Window 1'],
                ['value' => 2, 'name' => 'Window 2'],
            ];
        @endphp

        <!-- HEADER -->
        <x-header title="Admin Queue Management" separator>
            <x-slot:middle class="!justify-end">
                <div class="flex gap-3 items-center">
                    <x-theme-toggle class="btn btn-circle" />
                    <x-input placeholder="Search client name..." wire:model.live.debounce="search" clearable
                        icon="o-magnifying-glass" />
                </div>
            </x-slot:middle>
            <x-slot:actions>
                <x-button label="Add Queue" @click="$wire.modal = true" responsive icon="o-plus-circle" class="btn-primary" />
            </x-slot:actions>
        </x-header>

        <!-- TABLE -->
        <x-card wire:poll.5s="pollRefresh">
            <h2 class="mb-4 text-lg font-semibold">Current Queue</h2>
            <x-table :headers="$headers" :rows="$users" :sort-by="$sortBy">
                @scope('actions', $user)
                    <div class="flex items-center">
                        @if ($user['status'] === 'pending')
                            <x-button icon="o-arrow-path" class="text-blue-500 btn-ghost btn-sm"
                                @click="$wire.openEditStatusModalToProcess({{ $user['id'] }})" />
                        @elseif ($user['status'] === 'process')
                            <x-button icon="o-check" class="text-green-500 btn-ghost btn-sm"
                                @click="$wire.openEditStatusModalApprove({{ $user['id'] }})" />
                        @else
                            <x-button icon="o-x-mark" wire:click="delete({{ $user['id'] }})"
                                wire:confirm="Do you want to delete this queue?" spinner
                                class="text-red-500 btn-ghost btn-sm" />
                        @endif
                        <x-button icon="o-arrow-left-end-on-rectangle" class="text-yellow-500 btn-ghost btn-sm"
                            @click="$wire.openModalReason({{ $user['id'] }})" />
                    </div>
                @endscope
            </x-table>
        </x-card>

        <!-- MODALS -->
        <x-modal wire:model="modal" title="Add new queue" subtitle="please enter the details below">
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
                <x-select required :options="$inquiry_type" option-value="value" option-label="name"
                    wire:model="inquiry_type" />
                <hr>
                <x-textarea label="Inquiry Details" wire:model="inquiry_details"
                    placeholder="Please describe your inquiry..." hint="Max 1000 chars" rows="5" required inline />
                <x-slot:actions>
                    <x-button label="Join Queue" class="w-full btn-primary" type="submit" spinner="save" />
                </x-slot:actions>
            </x-form>
        </x-modal>

        <x-modal wire:model.defer="modalEditStatus" title="Change Queue Status">
            <div class="mb-5">Are you sure you want to change the status to "Done"?</div>
            <x-slot:actions>
                <x-button label="Cancel" @click="$wire.modalEditStatus = false" />
                <x-button label="Confirm" class="btn-primary" wire:click="updateStatusToApprove" spinner />
            </x-slot:actions>
        </x-modal>

        <x-modal wire:model.defer="modalEditStatusToProcess" title="Change Queue Status">
            <div class="mb-5">
                <p class="mb-3">Are you sure you want to change the status to "Currently Serving"?</p>
                <x-select label="Select Window" :options="$windows" option-value="value" option-label="name"
                    wire:model="window_number" required />
            </div>
            <x-slot:actions>
                <x-button label="Cancel" @click="$wire.modalEditStatusToProcess = false" />
                <x-button label="Confirm" class="btn-primary" wire:click="updateStatusToProcess" spinner />
            </x-slot:actions>
        </x-modal>

        <x-modal wire:model.defer="modalReason" title="Remove from Queue?">
            <div class="mb-5">
                <x-textarea label="Reason for queue removal" wire:model="reason_details" rows="5" required inline />
            </div>
            <x-slot:actions>
                <x-button label="Cancel" @click="$wire.modalReason = false" />
                <x-button label="Confirm" class="btn-primary" wire:click="updateReason" spinner />
            </x-slot:actions>
        </x-modal>
    @endif
</div>
