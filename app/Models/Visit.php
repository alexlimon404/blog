<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Visit extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $fillable = [
        'url',
        'page_title',
        'referrer',
        'user_agent',
        'ip_address',
        'session_id',
        'post_id',
        'metadata',
    ];

    protected $casts = [
        'metadata' => 'array',
        'created_at' => 'datetime',
    ];

    public static function getTotalVisits(): int
    {
        return static::count();
    }

    public static function getTodayVisits(): int
    {
        return static::whereDate('created_at', today())->count();
    }

    public static function getTodayUniqueVisits(): int
    {
        return static::distinct('ip_address')->whereDate('created_at', today())->count('ip_address');
    }

    public static function getUniqueVisitors(): int
    {
        return static::distinct('ip_address')->count('ip_address');
    }

    public static function getPopularPages(int $limit = 10): array
    {
        return static::selectRaw('url, page_title, COUNT(*) as visits_count')
            ->groupBy('url', 'page_title')
            ->orderByDesc('visits_count')
            ->limit($limit)
            ->get()
            ->toArray();
    }

    public static function getVisitsByDate(int $days = 30): array
    {
        return static::selectRaw('DATE(created_at) as date, COUNT(*) as visits')
            ->where('created_at', '>=', now()->subDays($days))
            ->groupBy('date')
            ->orderBy('date')
            ->get()
            ->toArray();
    }

    public function scopeToday(Builder $query): Builder
    {
        return $query->whereDate('created_at', today());
    }

    public function scopeThisWeek(Builder $query): Builder
    {
        return $query->whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()]);
    }

    public function scopeThisMonth(Builder $query): Builder
    {
        return $query->whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year);
    }

    public function post(): BelongsTo
    {
        return $this->belongsTo(Post::class);
    }
}
