<?php

namespace App\Mail;

use App\Models\BranchSetting;
use App\Models\Invitation;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class InvitationMail extends Mailable
{
    use Queueable, SerializesModels;

    public string $signUpUrl;
    public string $schoolName;
    public ?string $recipientName;

    public function __construct(public Invitation $invitation)
    {
        $branch = BranchSetting::current();
        $this->schoolName    = $branch->branch_name ?: 'McKaynine Dog School';
        $this->recipientName = $invitation->name;
        $this->signUpUrl     = url(route('invitation.register', ['token' => $invitation->token], false));
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'You\'re invited to join ' . $this->schoolName,
        );
    }

    public function content(): Content
    {
        return new Content(view: 'emails.invitation');
    }

    public function attachments(): array
    {
        return [];
    }
}
