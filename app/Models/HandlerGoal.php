<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
class HandlerGoal extends Model {
    use HasFactory;
    protected $fillable = ['enrolment_id','instructor_id','goal','progress_notes','visible_to_handler','status'];
    protected $casts = ['visible_to_handler'=>'boolean'];
    public function enrolment() { return $this->belongsTo(Enrolment::class); }
    public function instructor() { return $this->belongsTo(Instructor::class); }
}
