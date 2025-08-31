<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Visit extends Model
{
    use HasFactory;

    protected $fillable = [
        'url',
        'page_title',
        'referrer',
        'user_agent',
        'ip_address',
        'session_id',
        'metadata',
        'visited_at',
    ];

    protected $casts = [
        'metadata' => 'array',
        'visited_at' => 'datetime',
    ];
}
