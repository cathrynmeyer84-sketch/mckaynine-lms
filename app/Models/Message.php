<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Message extends Model
{
    protected $fillable = ['conversation_id', 'sender_user_id', 'blocks'];

    protected $casts = ['blocks' => 'array'];

    public function conversation()
    {
        return $this->belongsTo(Conversation::class);
    }

    public function sender()
    {
        return $this->belongsTo(User::class, 'sender_user_id');
    }

    public function isSystem(): bool
    {
        return is_null($this->sender_user_id);
    }
}
