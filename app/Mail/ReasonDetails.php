<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ReasonDetails extends Mailable
{
    use Queueable, SerializesModels;

    public $queue;
    public $reasonDetails;

    public function __construct($queue, $reasonDetails)
    {
        $this->queue = $queue;
        $this->reasonDetails = $reasonDetails;
    }

    public function build()
    {
        return $this->subject('Queue Status Updated')
            ->view('emails.reason_details')
            ->with([
                'queue' => $this->queue,
                'reasonDetails' => $this->reasonDetails, // Pass to view
            ]);
    }
}
