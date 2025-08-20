<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Voice extends Model
{
    use HasFactory;

    protected $fillable = [
        'vendor',
        'language',
        'language_code',
        'voice_name',
        'voice_id',
        'gender',
        'voice_engine',
        'sample_url',
        'avatar_url',
        'status',
    ];
}
