<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PushSubscription extends Model
{
    protected $fillable = [
        'endpoint',
        'keys',
        'user_agent',
        'ip_address',
    ];

    protected $casts = [
        'keys' => 'array',
    ];
}
