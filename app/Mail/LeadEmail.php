<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Symfony\Component\Mime\Email as SymfonyEmail;

class LeadEmail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     */
    public $data;
    public function __construct($data)
    {
    $this->data=$data;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'SOLAR ASPEKT',
        );
    }
    public function build()
    {
        return $this->view('mail.lead')
        ->from($this->data['from'])
        ->to($this->data['to'])
        ->subject($this->data['subject'])
        ->withSwiftMessage(function ($message) {
            $message->getHeaders()->addTextHeader('Return-Path', $this->data['from']);
        });
    }


public function toSymfonyEmail()
{
    return (new SymfonyEmail())
        ->from($this->data['from'])
        ->to($this->data['to'])
        ->subject($this->data['subject'])
        ->html($this->data['body']);
}

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            markdown: 'admin.email.lead',
        );

    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}
