<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ContactMessage extends Mailable
{
    use Queueable, SerializesModels;

    public $messageData;

    public function __construct($message)
    {
        $this->messageData = $message;
    }

    public function build()
    {
        return $this->subject('New Contact Form Message')
                    ->view('emails.contact-message');
    }
}