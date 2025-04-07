<?php

namespace App\Livewire;

use App\Models\Feedback;
use Livewire\Component;
use Livewire\Attributes\Title;
use Livewire\Attributes\Layout;
use Mary\Traits\Toast;

#[Layout('components.layouts.client')]
#[Title('Feedback Form')]
class UserFeedback extends Component
{
    use Toast;

    public $full_name = '';
    public $comments = '';
    public $inquiry_type = '';
    public $anonymous = false;
    public $reaction = null; // Ensure reaction starts as null

    public function submitFeedback()
{
    $this->validate([
        'full_name' => $this->anonymous ? 'nullable' : 'required|string|max:255',
        'comments' => 'nullable|string',
        'inquiry_type' => 'string',
        'reaction' => 'required|in:angry,sad,neutral,happy,very_happy',
    ]);

    Feedback::create([
        'full_name' => $this->anonymous ? 'Anonymous' : $this->full_name,
        'comments' => $this->comments,
        'inquiry_type' => $this->inquiry_type,
        'anonymous' => $this->anonymous,
        'reaction' => $this->reaction,
    ]);

    // Show success toast
    $this->success("Thank you for your feedback!", position: 'toast-bottom');

    // Reset input fields
    $this->reset(['full_name', 'comments', 'anonymous', 'reaction']);

    // Forget the session queue_id to prevent redirection loop
    session()->forget('queue_id');
}


    public function render()
    {
        return view('livewire.user-feedback');
    }
}
