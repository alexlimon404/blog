<?php

namespace App\Models;

use App\Traits\HasActive;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property string $name
 * @property string $prompt
 * @property bool $active
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 */
class BasePrompt extends Model
{
    use HasFactory, HasActive;

    protected $fillable = [
        'name', 'prompt',
        'active'
    ];

    protected $casts = [
        'active' => 'boolean',
    ];
}
