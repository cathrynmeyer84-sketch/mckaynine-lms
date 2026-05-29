<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class InfoDocument extends Model
{
    protected $fillable = ['name', 'path', 'mime_type', 'size'];

    public function getUrlAttribute(): string
    {
        return url(Storage::url($this->path));
    }

    public function getExtensionAttribute(): string
    {
        return strtolower(pathinfo($this->path, PATHINFO_EXTENSION));
    }

    public function getFormattedSizeAttribute(): string
    {
        if (!$this->size) return '';
        if ($this->size < 1024) return $this->size . ' B';
        if ($this->size < 1048576) return round($this->size / 1024, 1) . ' KB';
        return round($this->size / 1048576, 1) . ' MB';
    }
}
