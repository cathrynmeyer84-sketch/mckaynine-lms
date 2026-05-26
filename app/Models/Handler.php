<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Handler extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'first_name', 'last_name', 'cell_number',
        'account_holder_name', 'occupation', 'vet_name_location',
        'whatsapp_permission', 'whatsapp_consent',
        'social_media_permission', 'photo_consent',
        'ground_rules_agreed', 'terms_agreed',
        'hear_about_us', 'hear_about_us_sources', 'status',
        'invoicesonline_client_id',
    ];

    protected $casts = [
        'whatsapp_permission' => 'boolean',
        'social_media_permission' => 'boolean',
        'ground_rules_agreed' => 'boolean',
        'terms_agreed' => 'boolean',
        'hear_about_us_sources' => 'array',
    ];

    public function user() { return $this->belongsTo(User::class); }
    public function dogs() { return $this->hasMany(Dog::class); }
    public function enrolments() { return $this->hasMany(Enrolment::class); }
    public function assessmentRequests() { return $this->hasMany(AssessmentRequest::class); }
    public function goals() { return $this->hasManyThrough(HandlerGoal::class, Enrolment::class); }
    public function accountHolder() { return $this->hasOne(AccountHolder::class); }
    public function billingPops()   { return $this->hasMany(BillingPop::class)->latest(); }
    public function surveys() { return $this->hasMany(Survey::class); }
    public function conversations()
    {
        return $this->user
            ? Conversation::forUser($this->user->id)
            : Conversation::whereRaw('0');
    }

    public function getFullNameAttribute(): string
    {
        return $this->first_name . ' ' . $this->last_name;
    }

    public function activeEnrolments()
    {
        return $this->enrolments()->whereIn('status', ['confirmed'])->with('dogClass');
    }
}
