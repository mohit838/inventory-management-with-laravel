<?php

namespace App\Interfaces;

use Closure;

interface CacheServiceInterface
{
    /**
     * Get an item from the cache, or store the default value.
     *
     * @param string $key
     * @param int $ttlSeconds
     * @param Closure $callback
     * @param array|string $tags
     * @return mixed
     */
    public function remember(string $key, int $ttlSeconds, Closure $callback, $tags = []);

    /**
     * Remove an item from the cache.
     *
     * @param string $key
     * @return bool
     */
    public function forget(string $key): bool;

    /**
     * Flush the cache for specific tags.
     *
     * @param array|string $tags
     * @return void
     */
    public function invalidateTags($tags): void;

    /**
     * Clear all cache.
     *
     * @return void
     */
    public function flush(): void;
}
