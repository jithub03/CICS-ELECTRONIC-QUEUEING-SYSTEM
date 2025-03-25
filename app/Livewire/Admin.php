<?php

namespace App\Livewire;

use Livewire\Component;
use App\Mail\QueueStatusUpdated;
use App\Mail\ReasonDetails;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Collection;
use Livewire\Attributes\Title;
use Mary\Traits\Toast;
use App\Models\Queue;
use Carbon\Carbon;

#[Title('Admin Dashboard')]
class Admin extends Component
{
    use Toast;

    public string $search = '';
    public bool $drawer = false;
    public bool $modal = false;

    public bool $modalEditStatus = false;
    public bool $modalEditStatusToProcess = false;
    public bool $modalReason = false;

    public array $sortBy = ['column' => 'id', 'direction' => 'asc'];
    public Collection $users;
    public string $lastUpdatedAt = '';
    public $userIdToUpdate;

    public $full_name = '';
    public $contact_number = '';
    public $email = '';
    public $inquiry_type = '';
    public $inquiry_details = '';
    public $notify_sms = false;
    public $notify_email = false;
    public $window_number = null; // Initialize window_number

    public $reason_details = '';

    protected $rules = [
        'full_name' => 'required|string|max:255',
        'contact_number' => 'required|numeric',
        'email' => 'required|email|max:255',
        'inquiry_type' => 'required|string',
        'inquiry_details' => 'string|max:1000',
        'notify_sms' => 'boolean',
        'notify_email' => 'boolean',
        'window_number' => 'nullable|integer', // Add validation rule for window_number
    ];

    public function openEditStatusModalToProcess($userId)
    {
        $this->userIdToUpdate = $userId;
        $this->window_number = null; // Ensure window_number is reset
        $this->modalEditStatusToProcess = true;
    }

    public function openEditStatusModalApprove($userId)
    {
        $this->userIdToUpdate = $userId;
        $this->modalEditStatus = true;
    }

    public function openModalReason($userId)
    {
        $this->userIdToUpdate = $userId;
        $this->modalReason = true;
    }

    public function mount()
    {
        $this->refreshUsers();
        // $this->lastUpdatedAt = Carbon::now()->toIso8601String(); // Store current timestamp when component is mounted
    }

    public function save()
    {
        $this->validate();

        $queue = Queue::create([
            'full_name' => $this->full_name,
            'contact_number' => $this->contact_number,
            'email' => $this->email,
            'inquiry_type' => $this->inquiry_type,
            'inquiry_details' => $this->inquiry_details,
            'notify_sms' => (bool) $this->notify_sms,
            'notify_email' => (bool) $this->notify_email,
        ]);

        $this->refreshUsers();
        $this->redirect('/admin');
    }

    public function updateStatusToApprove()
    {
        $queue = Queue::find($this->userIdToUpdate);

        if ($queue) {
            $queue->status = 'approve'; // Update the status to 'approve'
            $queue->window_number = null;
            $queue->save();
            $this->success("Queue #{$this->userIdToUpdate} status updated to 'Process'.", position: 'toast-bottom');
        } else {
            $this->error("Queue #{$this->userIdToUpdate} not found.", position: 'toast-bottom');
        }

        $this->resetModals();
        $this->refreshUsers(); // Refresh the users list
    }

    public function updateStatusToProcess()
    {
        $this->validate([
            'window_number' => 'required|integer', // Ensure window_number is validated
        ]);

        $queue = Queue::find($this->userIdToUpdate);

        if ($queue) {
            $queue->status = 'process';
            $queue->window_number = $this->window_number; // Ensure window_number is set
            $queue->save();

            // Only send email if notify_email is true
            if ($queue->notify_email) {
                try {
                    Mail::to($queue->email)->send(new QueueStatusUpdated($queue));
                } catch (\Exception $e) {
                    $this->error("Failed to send email notification.", position: 'toast-bottom');
                }
            }

            if ($queue->notify_sms) {
                try {
                    Http::asForm()->post(env('SMS_API_URL'), [
                        'api_token' => env('SMS_API_TOKEN'),
                        'message' => "You are next in line for the CICS Office Queue. Please come up to the office and wait for your turn.",
                        'phone_number' => $queue->contact_number,
                    ]);
                } catch (\Exception $e) {
                    $this->error("Failed to send SMS notification.", position: 'toast-bottom');
                }
            }

            $this->success("Queue #{$this->userIdToUpdate} status updated to 'Process'.", position: 'toast-bottom');
        } else {
            $this->error("Queue #{$this->userIdToUpdate} not found.", position: 'toast-bottom');
        }

        $this->resetModals();
        $this->refreshUsers();
    }

    public function updateReason()
    {
        $this->validate([
            'reason_details' => 'required|string', // Ensure window_number is validated
        ]);

        $queue = Queue::find($this->userIdToUpdate);

        if ($queue) {
            // Only send email if notify_email is true
            if ($queue->notify_email) {
                try {
                    Mail::to($queue->email)->send(new ReasonDetails($queue, $this->reason_details));
                } catch (\Exception $e) {
                    $this->error("Failed to send email notification.", position: 'toast-bottom');
                }
            }

            if ($queue->notify_sms) {
                try {
                    Http::asForm()->post(env('SMS_API_URL'), [
                        'api_token' => env('SMS_API_TOKEN'),
                        'message' => "You have been removed from the queue for the following reason/s: " . $this->reason_details,
                        'phone_number' => $queue->contact_number,
                    ]);
                } catch (\Exception $e) {
                    $this->error("Failed to send SMS notification.", position: 'toast-bottom');
                }
            }

            $this->success("Succesfully removed.", position: 'toast-bottom');
            $queue->delete();
        } else {
            $this->error("Queue #{$this->userIdToUpdate} not found.", position: 'toast-bottom');
        }

        $this->resetModals();
        $this->refreshUsers();
    }

    // Delete action
    public function delete($id): void
    {
        $queue = Queue::find($id);

        if ($queue) {
            $queue->delete();
            $this->success("Queue #$id deleted successfully.", position: 'toast-bottom');
        } else {
            $this->error("Queue #$id not found.", position: 'toast-bottom');
        }

        $this->refreshUsers();
    }

    // Table headers
    public function headers(): array
    {
        return [
            ['key' => 'id', 'label' => 'Ticket #', 'class' => 'w-1'],
            ['key' => 'full_name', 'label' => 'Client Name', 'class' => 'w-64', 'sortable' => false],
            ['key' => 'contact_number', 'label' => 'Contact Number', 'class' => 'w-32', 'sortable' => false],
            ['key' => 'email', 'label' => 'Email', 'class' => 'w-64', 'sortable' => false],
            ['key' => 'inquiry_details', 'label' => 'Inquiry Details', 'class' => 'w-42', 'sortable' => false],
            ['key' => 'inquiry_type', 'label' => 'Inquiry Type', 'class' => 'w-20', 'sortable' => false],
            ['key' => 'created_at', 'label' => 'Date', 'format' => ['date', 'd/m/Y'], 'class' => 'w-20', 'sortable' => false],
            [
                'key' => 'status',
                'label' => 'Status',
                'class' => 'w-20',
                'sortable' => false,
                'format' => fn($row, $field) => match (strtolower($field)) {
                    'pending'  => 'In queue',
                    'process'  => 'Currently Serving',
                    'approve'  => 'Done',
                },
            ],
        ];
    }

    public function refreshUsers()
    {
        $this->users = Queue::query()
            ->when($this->search, function ($query) {
                $query->where('full_name', 'like', '%' . $this->search . '%');
            })
            ->orderBy($this->sortBy['column'], $this->sortBy['direction'])
            ->get();
    }

    // Method to check if new queue has been added or updated
    // public function checkForNewQueue()
    // {
    //     $latestQueue = Queue::latest()->first(); // Get the latest queue

    //     if ($latestQueue && $latestQueue->created_at->toIso8601String() !== $this->lastUpdatedAt) {
    //         $this->lastUpdatedAt = $latestQueue->created_at->toIso8601String(); // Update timestamp
    //         $this->refreshUsers(); // Refresh only when a new queue is added
    //     }
    // }

    public function pollRefresh()
    {
        $this->refreshUsers();
    }

    public function resetModals()
    {
        $this->modal = false;
        $this->modalEditStatus = false;
        $this->modalEditStatusToProcess = false;
        $this->modalReason = false;
        $this->userIdToUpdate = null;
        $this->window_number = null;
    }

    public function render()
    {
        return view('livewire.admin', [
            'users' => $this->users,
            'headers' => $this->headers()
        ]);
    }
}
