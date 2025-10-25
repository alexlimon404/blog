<?php

namespace App\Traits;

use Illuminate\Cache\TaggedCache;

trait HasCache
{
    protected static $usesCache = true;

    public static function bootHasCache()
    {
        static::saved(function ($model) {
            static::flushCache();
        });

        static::deleted(function ($model) {
            static::flushCache();
        });
    }

    public static function withCache()
    {
        static::$usesCache = true;
    }

    public static function withoutCache()
    {
        static::$usesCache = false;
    }

    public static function usesCache(): bool
    {
        return static::$usesCache;
    }

    public static function cache(): TaggedCache
    {
        return app('cache')->tags(static::getCacheTags());
    }

    public static function getCacheTags(): array
    {
        return [static::class];
    }

    public static function flushCache(): bool
    {
        if (static::usesCache()) {
            return static::cache()->flush();
        }

        return false;
    }
}
