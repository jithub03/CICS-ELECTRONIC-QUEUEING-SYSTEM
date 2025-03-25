<?php

namespace App\Livewire;

use App\Models\Queue;
use Livewire\Component;
use Livewire\Attributes\Title;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Validate;

#[Layout('components.layouts.client')]
#[Title('Queue Form')]
class QueueForm extends Component
{
    public bool $modal = false;
    public string $pin = '';

    public $full_name = '';
    public $contact_number = '';
    public $email = '';
    public $inquiry_type = '';
    public $inquiry_details = '';
    public $notify_sms = false;
    public $notify_email = false;

    public $windowQueues = [];

    public ?Queue $currentServing = null;

    protected $rules = [
        'full_name' => 'required|string|max:255',
        'contact_number' => 'required|numeric',
        'email' => 'required|email|max:255',
        'inquiry_type' => 'required|string',
        'inquiry_details' => 'string|max:1000',
        'notify_sms' => 'boolean',
        'notify_email' => 'boolean',
    ];

    public function fetchCurrentServingPerWindow()
    {
        // Fetch current serving queue for each window (1, 2, 3)
        $this->windowQueues = Queue::whereIn('status', ['process', 'approve'])
            ->whereNotNull('window_number')
            ->orderBy('window_number')
            ->get()
            ->groupBy('window_number')
            ->map(fn($group) => $group->toArray())
            ->toArray();
    }

    public function mount()
    {
        $this->pollQueueStatus();
    }

    public function fetchCurrentServing()
    {
        $queueId = session('queue_id'); // Retrieve queue_id from session

        if ($queueId) {
            $this->currentServing = Queue::where('id', $queueId)
                ->whereIn('status', ['process', 'approve']) // Only fetch if status is 'process' or 'approve'
                ->first();
        }
    }

    public function pollQueueStatus()
    {
        $this->fetchCurrentServing();
        $this->fetchCurrentServingPerWindow();

        // Only redirect if the queue exists AND its status is 'approve'
        if ($this->currentServing && $this->currentServing->status === 'approve') {
            return redirect()->to("/user-feedback");
        }
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

        session(['queue_id' => $queue->id]);

        return redirect()->to("/queue-pending/{$queue->id}");
    }

    public function checkPin()
    {
        $this->validate([
            'pin' => 'required|digits:4'
        ]);

        if ($this->pin === '2025') {
            return redirect()->to('/admin');
        } else {
            $this->addError('pin', 'The PIN is incorrect.');
        }
    }

    public function render()
    {
        return view('livewire.queue-form');
    }
}
