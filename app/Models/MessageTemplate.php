<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MessageTemplate extends Model
{
    protected $fillable = ['slug', 'name', 'subject', 'blocks', 'class_type_id'];

    protected $casts = ['blocks' => 'array'];

    public function classType()
    {
        return $this->belongsTo(ClassType::class);
    }
}
