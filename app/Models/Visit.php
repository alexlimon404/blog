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
        'created_at',
        'url',
        'page_title',
        'referrer',
        'user_agent',
        'ip_address',
        'session_id',
        'post_id',
        'metadata',
        'bot_name',
    ];

    protected $casts = [
        'metadata' => 'array',
        'created_at' => 'datetime',
    ];

    protected static function booted(): void
    {
        static::creating(function (Visit $visit) {
            $botInfo = static::detectBot($visit->user_agent ?? '');
            $visit->bot_name = $botInfo['bot_name'];
        });
    }

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

    public static function botNames(): array
    {
        return [
            'Googlebot' => 'Google',
            'Google-InspectionTool' => 'Google',
            'Googlebot-Image' => 'Google Images',
            'Googlebot-News' => 'Google News',
            'Googlebot-Video' => 'Google Video',
            'AdsBot-Google' => 'Google Ads',
            'Mediapartners-Google' => 'Google AdSense',
            'APIs-Google' => 'Google APIs',
            'bingbot' => 'Bing',
            'BingPreview' => 'Bing Preview',
            'msnbot' => 'MSN',
            'Slurp' => 'Yahoo',
            'DuckDuckBot' => 'DuckDuckGo',
            'Baiduspider' => 'Baidu',
            'YandexBot' => 'Yandex',
            'YandexImages' => 'Yandex Images',
            'facebot' => 'Facebook',
            'facebookexternalhit' => 'Facebook',
            'Twitterbot' => 'Twitter',
            'LinkedInBot' => 'LinkedIn',
            'Slackbot' => 'Slack',
            'Discordbot' => 'Discord',
            'TelegramBot' => 'Telegram',
            'WhatsApp' => 'WhatsApp',
            'ia_archiver' => 'Alexa',
            'archive.org_bot' => 'Archive.org',
            'SemrushBot' => 'Semrush',
            'AhrefsBot' => 'Ahrefs',
            'MJ12bot' => 'Majestic',
            'DotBot' => 'Moz',
            'PetalBot' => 'Huawei',
            'curl' => 'cURL',
            'wget' => 'Wget',
            'python-requests' => 'Python Requests',
            'axios' => 'Axios',
            'node-fetch' => 'Node Fetch',
            'okhttp' => 'OkHttp',
            'Postman' => 'Postman',
        ];
    }

    public static function detectBot(string $userAgent): array
    {
        $bots = static::botNames();

        $userAgentLower = strtolower($userAgent);

        foreach ($bots as $pattern => $name) {
            if (str_contains($userAgentLower, strtolower($pattern))) {
                return [
                    'bot_name' => $name,
                ];
            }
        }

        $generalPatterns = ['bot', 'crawl', 'spider', 'scraper', 'http'];
        foreach ($generalPatterns as $pattern) {
            if (str_contains($userAgentLower, $pattern)) {
                return [
                    'bot_name' => 'Unknown Bot',
                ];
            }
        }

        return [
            'bot_name' => null,
        ];
    }
}
