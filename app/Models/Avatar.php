<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Avatar extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'name',
        'style',              // realistic | cartoon | 3d
        'image_path',         // final avatar image (transformed or original)
        'original_image_path', // original uploaded photo (kept for re-processing)
        'thumbnail_path',     // auto-generated thumbnail
        'gender',             // male | female | neutral
        'tags',               // JSON array  e.g. ["Professional","Lifestyle"]
        'is_public',          // visible to other users
        'status',             // active | processing | failed
    ];

    protected $casts = [
        'tags'      => 'array',
        'is_public' => 'boolean',
    ];

    /* ── Relationships ─────────────────────────── */

    public function user()
    {
        return $this->belongsTo(\App\Models\User::class);
    }

    /* ── Accessors ─────────────────────────────── */

    public function getImageUrlAttribute(): string
    {
        return $this->image_path
            ? asset('public/storage/' . $this->image_path)
            : 'https://placehold.co/600x600?text=Avatar';
    }

    public function getThumbnailUrlAttribute(): string
    {
        return $this->thumbnail_path
            ? asset('public/storage/' . $this->thumbnail_path)
            : $this->image_url;
    }

    /* ── Scopes ────────────────────────────────── */

    public function scopePublic($query)
    {
        return $query->where('is_public', true);
    }

    public function scopeOwnedBy($query, int $userId)
    {
        return $query->where('user_id', $userId);
    }
}
