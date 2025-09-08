<?php

namespace App\Traits;

use Illuminate\Database\Eloquent\Builder;

/**
 * @property bool $active
 * @method static Builder active(bool $active = true)
 */
trait HasActive
{
    public function isActive(): bool
    {
        return $this->active;
    }

    public function isNotActive(): bool
    {
        return ! $this->isActive();
    }

    public function scopeActive(Builder $query, bool $active = true)
    {
        return $query->where(compact('active'));
    }

    public function updateActive(bool $active): bool
    {
        return $this->update(compact('active'));
    }
}
