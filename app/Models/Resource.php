<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
class Resource extends Model {
    use HasFactory;
    protected $fillable = ['title','content','image_path','file_path','external_url','category','class_categories','is_published','sort_order','created_by'];
    protected $casts = ['is_published'=>'boolean','class_categories'=>'array'];
    public function createdBy() { return $this->belongsTo(User::class,'created_by'); }
}
