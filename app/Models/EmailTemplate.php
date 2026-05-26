<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EmailTemplate extends Model
{
    protected $fillable = ['key', 'name', 'subject', 'body', 'available_placeholders'];

    public static function getByKey(string $key): ?self
    {
        return static::where('key', $key)->first();
    }

    public function render(array $replacements): array
    {
        $subject = $this->subject;
        $body    = $this->body;

        foreach ($replacements as $placeholder => $value) {
            $subject = str_replace($placeholder, $value ?? '', $subject);
            $body    = str_replace($placeholder, $value ?? '', $body);
        }

        return ['subject' => $subject, 'body' => $body];
    }
}
