<?php

namespace App\Mail;

use App\Models\VideoCall;
use App\Models\VideoCallInvite as InviteModel;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

/**
 * Phase 3b — Einladung zu einem Videocall an einen Gast (Kunde oder freier Empfänger).
 * Der Gast-Link ist signiert + zeitlich begrenzt (JitsiService::guestUrl).
 */
class VideoCallInvite extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public VideoCall $videoCall,
        public InviteModel $invite,
        public string $guestUrl,
    ) {
    }

    public function envelope(): Envelope
    {
        return new Envelope(subject: 'Einladung zum Video-Call');
    }

    public function content(): Content
    {
        return new Content(view: 'emails.video-call-invite');
    }
}
