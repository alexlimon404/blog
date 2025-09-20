<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class StatusHistory extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'resource_type', 'resource_id',
        'status',
    ];

    protected $casts = [
        'resource_id' => 'integer',
    ];

    public static function bootHasStatus(): void
    {
        static::creating(function ($model) {
            $model->forceFill(['created_at' => now()]);
        });
    }

    public function resource(): MorphTo
    {
        return $this->morphTo();
    }
}
