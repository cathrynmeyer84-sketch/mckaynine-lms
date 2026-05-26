<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
class Register extends Model {
    use HasFactory;
    protected $fillable = ['class_date_id','enrolment_id','attendance','notes','marked_by','marked_at'];
    protected $casts = ['marked_at'=>'datetime'];
    public function classDate() { return $this->belongsTo(ClassDate::class); }
    public function enrolment() { return $this->belongsTo(Enrolment::class); }
    public function markedBy() { return $this->belongsTo(User::class,'marked_by'); }
}
