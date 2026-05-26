<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Conversation extends Model
{
    protected $fillable = [
        'type', 'subject', 'class_id', 'created_by_user_id', 'is_read_only', 'template_slug',
    ];

    protected $casts = [
        'is_read_only' => 'boolean',
    ];

    public function messages()
    {
        return $this->hasMany(Message::class)->orderBy('created_at');
    }

    public function latestMessage()
    {
        return $this->hasOne(Message::class)->latestOfMany();
    }

    public function participants()
    {
        return $this->hasMany(ConversationParticipant::class);
    }

    public function dogClass()
    {
        return $this->belongsTo(DogClass::class, 'class_id');
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by_user_id');
    }

    public function scopeForUser($query, int $userId)
    {
        return $query->whereHas('participants', fn($q) => $q->where('user_id', $userId));
    }

    public function isUnreadFor(int $userId): bool
    {
        $participant = $this->participants->firstWhere('user_id', $userId);
        if (!$participant) return false;
        $latest = $this->latestMessage;
        if (!$latest) return false;
        return is_null($participant->last_read_at) || $participant->last_read_at < $latest->created_at;
    }

    public function markReadFor(int $userId): void
    {
        $this->participants()->where('user_id', $userId)->update(['last_read_at' => now()]);
    }
}
