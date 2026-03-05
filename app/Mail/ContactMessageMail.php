<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ContactMessageMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * @param array{name:string,email:string,subject:string,message:string} $payload
     */
    public function __construct(public array $payload)
    {
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Contact Form: ' . $this->payload['subject'],
            replyTo: [
                new \Illuminate\Mail\Mailables\Address(
                    $this->payload['email'],
                    $this->payload['name']
                ),
            ],
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.contact-message',
            with: [
                'payload' => $this->payload,
            ],
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
