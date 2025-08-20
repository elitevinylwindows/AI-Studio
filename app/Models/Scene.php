<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Scene extends Model {
    protected $fillable = [
        'project_id','position','bg_video','avatar_image','audio','script','voice','config'
    ];
    protected $casts = ['config' => 'array'];
    public function project(){ return $this->belongsTo(Project::class); }
}
