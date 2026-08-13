<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ContactMessageMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    /**
     * Redeclared so the subject is exposed to the view.
     */
    public $subject;

    /**
     * Create a new message instance.
     */
    public function __construct(
        public string $name,
        public string $email,
        string $subject,
        public string $message
    ) {
        $this->subject = $subject;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            to: [config('blog.contact_recipient') ?: config('mail.from.address')],
            replyTo: [$this->email, $this->name],
            subject: 'Contact form: '.$this->subject,
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            markdown: 'mail.contact-message',
        );
    }
}
