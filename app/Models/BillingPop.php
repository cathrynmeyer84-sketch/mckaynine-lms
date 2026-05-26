<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BillingPop extends Model
{
    use HasFactory;

    protected $fillable = [
        'handler_id', 'invoice_reference', 'amount',
        'file_path', 'is_reviewed', 'reviewed_by', 'reviewed_at', 'notes',
    ];

    protected $casts = [
        'is_reviewed' => 'boolean',
        'reviewed_at' => 'datetime',
        'amount'      => 'decimal:2',
    ];

    public function handler()    { return $this->belongsTo(Handler::class); }
    public function reviewedBy() { return $this->belongsTo(User::class, 'reviewed_by'); }
}
