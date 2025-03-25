<?php

namespace App\Livewire;

use App\Models\Feedback as ModelsFeedback;
use Livewire\Component;
use Livewire\Attributes\Title;
use Mary\Traits\Toast;

#[Title('Feedback')]
class Feedback extends Component
{
    use Toast;
    public $feedbacks;

    public function mount()
    {
        $this->feedbacks = ModelsFeedback::latest()->get(); // Fetch latest feedback
    }

    public function render()
    {
        return view('livewire.feedback', [
            'feedbacks' => $this->feedbacks,
        ]);
    }
}
