<?php

namespace App\Livewire;

use App\Models\Queue;
use Livewire\Component;
use Livewire\Attributes\Title;
use Livewire\Attributes\Layout;

#[Layout('components.layouts.client')]
#[Title('Queue Pending')]
class QueuePending extends Component
{
    public ?Queue $queue = null;
    public string $queueStatus;

    public function mount($queueId)
    {
        $this->queue = Queue::find($queueId);

        if (!$this->queue) {
            return redirect()->to("/");
        }

        $this->queueStatus = $this->queue->status;
    }

    public function render()
    {
        return view('livewire.queue-pending', [
            'queue' => $this->queue,
        ]);
    }
}
