<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class FileUpload extends Model
{
    use HasFactory;

    protected $fillable = [
        'filename',
        'original_name',
        'category',
    ];

    /**
     * Get the public URL to the stored file.
     */
    public function getUrlAttribute()
    {
        return asset('storage/' . $this->filename);
    }

    /**
     * Scope a query to only include files of a specific category.
     */
    public function scopeCategory($query, $type)
    {
        return $query->where('category', $type);
    }
}
