<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class BranchSetting extends Model
{
    protected $fillable = [
        'branch_name', 'address', 'email', 'phone', 'website',
        'hero_image_path',
        'enrolment_fee',
        'private_lesson_fee',
        'bank_name', 'bank_account_name', 'bank_account_number',
        'bank_branch_code', 'bank_reference_note',
        'legal_entity_name', 'legal_registration_number',
        'io_username', 'io_password', 'io_business_id',
        'invoicesonline_client_id',
    ];

    protected $casts = [
        'enrolment_fee' => 'decimal:2',
        'private_lesson_fee' => 'decimal:2',
    ];

    public static function current(): self
    {
        return static::firstOrCreate([]);
    }
}
