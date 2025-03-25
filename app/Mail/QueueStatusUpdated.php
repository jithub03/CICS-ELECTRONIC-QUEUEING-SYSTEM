<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class QueueStatusUpdated extends Mailable
{
    use Queueable, SerializesModels;

    public $queue;

    public function __construct($queue)
    {
        $this->queue = $queue;
    }

    public function build()
    {
        return $this->subject('Queue Status Updated')
            ->view('emails.queue_status_updated')
            ->with(['queue' => $this->queue]);
    }
}
