<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Author extends Model
{
    protected $fillable = [
        'name',
        'email',
        'bio',
        'avatar',
    ];

    public function posts()
    {
        return $this->hasMany(Post::class);
    }
}
