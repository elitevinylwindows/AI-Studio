<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class TalkingHead extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'avatar_id',
        'title',
        'script',              // the text that was spoken
        'voice_name',          // Google TTS voice used
        'audio_path',          // path to the TTS audio file
        'video_path',          // path to the generated video
        'video_url',           // public URL to the video
        'replicate_id',        // Replicate prediction ID
        'status',              // pending | processing | completed | failed
        'error_message',       // error details if failed
    ];

    /* ── Relationships ─────────────────────────── */

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function avatar()
    {
        return $this->belongsTo(Avatar::class);
    }

    /* ── Accessors ─────────────────────────────── */

    public function getVideoPublicUrlAttribute(): ?string
    {
        if ($this->video_path) {
            return asset('public/storage/' . $this->video_path);
        }
        return $this->video_url; // external URL from Replicate
    }

    public function getAudioPublicUrlAttribute(): ?string
    {
        return $this->audio_path
            ? asset('public/storage/' . $this->audio_path)
            : null;
    }
}
