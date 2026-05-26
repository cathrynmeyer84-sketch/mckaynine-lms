<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
class WeeklyContent extends Model {
    use HasFactory;
    protected $fillable = ['class_date_id','title','description','youtube_url','practice_checklist','what_to_bring_next_week','extra_notes','is_published','publish_at'];
    protected $casts = ['is_published'=>'boolean','publish_at'=>'datetime'];
    public function classDate() { return $this->belongsTo(ClassDate::class); }
    public function isVisible(): bool { return $this->is_published && ($this->publish_at === null || $this->publish_at->isPast()); }
}
