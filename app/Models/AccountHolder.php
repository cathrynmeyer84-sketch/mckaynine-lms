<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class AccountHolder extends Model {
    use HasFactory;

    protected $fillable = [
        'handler_id', 'name', 'email', 'phone', 'billing_address', 'invoicing_notes',
        'linked_handler_id', 'link_status', 'link_token', 'link_expires_at',
    ];

    protected $casts = [
        'link_expires_at' => 'datetime',
    ];

    public function handler() { return $this->belongsTo(Handler::class); }
    public function linkedHandler() { return $this->belongsTo(Handler::class, 'linked_handler_id'); }

    /** Is this a pending or approved link to another McKaynine handler? */
    public function isLinked(): bool
    {
        return $this->linked_handler_id !== null;
    }

    /** Has the linked handler approved the request? */
    public function isApproved(): bool
    {
        return $this->link_status === 'approved';
    }

    /** Is this link request still waiting for a response? */
    public function isPending(): bool
    {
        return $this->link_status === 'pending_approval'
            && $this->link_expires_at
            && $this->link_expires_at->isFuture();
    }

    /** Generate a fresh token and expiry for the approval email */
    public function generateLinkToken(): void
    {
        $this->update([
            'link_token'      => Str::random(48),
            'link_expires_at' => now()->addDays(7),
        ]);
    }
}
