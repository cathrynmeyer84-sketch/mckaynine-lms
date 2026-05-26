<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
class Survey extends Model {
    use HasFactory;
    protected $fillable = ['enrolment_id','handler_id','overall_rating','instructor_rating','most_valuable','suggestions','likelihood_to_recommend','comments','submitted_at'];
    protected $casts = ['submitted_at'=>'datetime'];
    public function enrolment() { return $this->belongsTo(Enrolment::class); }
    public function handler() { return $this->belongsTo(Handler::class); }
}
