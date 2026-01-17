<?php

namespace App\Services;

use App\Interfaces\CacheServiceInterface;
use Closure;
use Illuminate\Support\Facades\Cache;

class RedisCacheService implements CacheServiceInterface
{
    /**
     * {@inheritDoc}
     */
    public function remember(string $key, int $ttlSeconds, Closure $callback, $tags = [])
    {
        if (! empty($tags) && config('cache.default') !== 'file' && config('cache.default') !== 'database') {
            return Cache::tags($tags)->remember($key, $ttlSeconds, $callback);
        }

        return Cache::remember($key, $ttlSeconds, $callback);
    }

    /**
     * {@inheritDoc}
     */
    public function forget(string $key): bool
    {
        return Cache::forget($key);
    }

    /**
     * {@inheritDoc}
     */
    public function invalidateTags($tags): void
    {
        if (! empty($tags) && config('cache.default') !== 'file' && config('cache.default') !== 'database') {
            Cache::tags($tags)->flush();
        }
    }

    /**
     * {@inheritDoc}
     */
    public function flush(): void
    {
        Cache::flush();
    }
}
