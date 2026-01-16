<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;

class RedisCacheService
{
    /**
     * Get an item from the cache, or store the default value.
     *
     * @param  string  $key
     * @param  int  $ttlSeconds
     * @param  \Closure  $callback
     * @return mixed
     */
    public function remember(string $key, int $ttlSeconds, \Closure $callback)
    {
        return Cache::remember($key, $ttlSeconds, $callback);
    }

    /**
     * Init a cache tag instance (if supported by driver, otherwise fallback to standard).
     *
     * @param  array|string  $tags
     * @return \Illuminate\Cache\TaggedCache|\Illuminate\Cache\Repository
     */
    public function tags($tags)
    {
        return Cache::tags($tags);
    }

    /**
     * Remove an item from the cache.
     *
     * @param  string  $key
     * @return bool
     */
    public function forget(string $key): bool
    {
        return Cache::forget($key);
    }

    /**
     * Flush the cache for specific tags.
     *
     * @param  array|string  $tags
     * @return void
     */
    public function flushTags($tags): void
    {
        Cache::tags($tags)->flush();
    }
}
