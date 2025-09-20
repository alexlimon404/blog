<?php

namespace App\Traits;

use Carbon\Carbon;
use App\Models\StatusHistory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphOne;

/**
 * @property string $status
 * @property Carbon $status_at
 * @property string $status_comment
 * @method static Builder forStatus(string $status)
 */
trait HasStatus
{
    public static function bootHasStatus()
    {
        static::creating(function ($model) {
            $model->forceFill(['status_at' => now()]);
        });
    }

    public function isStatus(string $status): bool
    {
        return $this->status === $status;
    }

    public function isNotStatus(string $status): bool
    {
        return ! $this->isStatus($status);
    }

    public function updateStatus(string $status, array $attributes = []): bool
    {
        if ($updated = $this->fillStatus($status, $attributes)->save()) {
            $this->createStatusHistory();
        }

        return $updated;
    }

    public function fillStatus(string $status, array $attributes = []): Model
    {
        if ($this->isStatus($status)) {
            return $this;
        }

        $attributes['status_at'] ??= now();

        return $this->fill(compact('status') + $attributes);
    }

    public function createStatusHistory(): StatusHistory
    {
        return $this->statusHistory()->create([
            'status' => $this->status,
        ]);
    }

    public function statusHistory(): MorphOne
    {
        return $this->morphOne(StatusHistory::class, 'resource');
    }

    public function scopeForStatus(Builder $query, string $status)
    {
        return $query->where(compact('status'));
    }
}
