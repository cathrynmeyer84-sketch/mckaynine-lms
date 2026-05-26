<?php

namespace App\Mail;

use App\Models\AccountHolder;
use App\Models\Handler;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class AccountLinkRequest extends Mailable
{
    use Queueable, SerializesModels;

    public string $approveUrl;
    public string $declineUrl;
    public string $requesterName;
    public string $requesterDogName;

    public function __construct(
        public Handler $requester,
        public AccountHolder $accountHolder,
    ) {
        $this->requesterName    = $requester->full_name;
        $this->requesterDogName = $requester->dogs()->latest()->first()?->name ?? 'their dog';
        $this->approveUrl       = route('billing.link.approve', $accountHolder->link_token);
        $this->declineUrl       = route('billing.link.decline', $accountHolder->link_token);
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: "{$this->requesterName} wants to link their billing to your McKaynine account",
        );
    }

    public function content(): Content
    {
        return new Content(view: 'emails.account-link-request');
    }

    public function attachments(): array { return []; }
}
