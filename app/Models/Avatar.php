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
        'image_path',       // stored relative to storage/app/public  e.g. avatars/abc.png
        'thumbnail_path',   // auto-generated thumbnail
        'gender',           // male | female | neutral
        'tags',             // JSON array  e.g. ["Professional","Lifestyle"]
        'is_public',        // visible to other users
        'status',           // active | processing | failed
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
            ? asset('storage/' . $this->image_path)
            : 'https://placehold.co/600x600?text=Avatar';
    }

    public function getThumbnailUrlAttribute(): string
    {
        return $this->thumbnail_path
            ? asset('storage/' . $this->thumbnail_path)
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
