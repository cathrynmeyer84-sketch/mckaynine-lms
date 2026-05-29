<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Invitation extends Model
{
    protected $fillable = [
        'email', 'name', 'type', 'token', 'expires_at', 'used_at', 'created_by',
    ];

    protected $casts = [
        'expires_at' => 'datetime',
        'used_at'    => 'datetime',
    ];

    // ── Relationships ─────────────────────────────────────────────

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    // ── Helpers ───────────────────────────────────────────────────

    public static function generate(string $email, ?string $name, int $createdBy, string $type = 'handler'): self
    {
        return self::create([
            'email'      => $email,
            'name'       => $name,
            'type'       => $type,
            'token'      => Str::random(48),
            'expires_at' => now()->addDays(14),
            'created_by' => $createdBy,
        ]);
    }

    public function isInstructor(): bool
    {
        return $this->type === 'instructor';
    }

    public function isValid(): bool
    {
        return is_null($this->used_at) && $this->expires_at->isFuture();
    }

    public function getStatusAttribute(): string
    {
        if ($this->used_at) return 'used';
        if ($this->expires_at->isPast()) return 'expired';
        return 'pending';
    }

    public function markUsed(): void
    {
        $this->update(['used_at' => now()]);
    }
}
