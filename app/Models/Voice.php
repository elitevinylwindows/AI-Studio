<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Voice extends Model
{
    use HasFactory;

    protected $fillable = [
        'vendor',
        'language',          // short label if you use it
        'language_full',     // "English (United States)"
        'language_code',     // "en-US"
        'voice_name',        // e.g. "en-US-Neural2-A"
        'voice_id',
        'gender',            // "MALE|FEMALE|NEUTRAL"
        'voice_engine',      // "Neural|Standard"
        'sample_url',
        'avatar_url',
        'status',
        'voice_text',        // user-entered text
        'audio_format',      // mp3|ogg|wav
    ];
}
