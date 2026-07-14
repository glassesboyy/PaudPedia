<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use App\Models\SchoolTransferRequest;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class HeadmasterTransferMail extends Mailable
{
    use Queueable, SerializesModels;

    public $transferRequest;
    public $frontEndUrl;

    /**
     * Create a new message instance.
     */
    public function __construct(SchoolTransferRequest $transferRequest)
    {
        $this->transferRequest = $transferRequest;
        // In real app, this would be env('FRONTEND_URL') or similar
        $this->frontEndUrl = rtrim(env('FRONTEND_SIAKAD_URL', 'http://localhost:5173'), '/');
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Undangan Transfer Kepemilikan Sekolah: ' . $this->transferRequest->school->name,
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            markdown: 'emails.headmaster-transfer',
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
