<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Project extends Model {
    protected $fillable = ['title','state','status'];
    protected $casts = ['state' => 'array'];
    public function scenes(){ return $this->hasMany(Scene::class)->orderBy('position'); }
}
