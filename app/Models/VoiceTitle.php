<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VoiceTitle extends Model
{
    protected $fillable = [
        'voice_name',
        'display_title',
        'language_code'
    ];
}
