<?php

namespace App\Utils;

use Illuminate\Support\Facades\Redis;

class RedisUtil
{
    /**
     * Store a value in Redis with an optional expiration (in seconds).
     */
    public static function set(string $key, mixed $value, int $seconds = null): bool
    {
        $serializedValue = is_scalar($value) ? $value : serialize($value);
        
        if ($seconds) {
            return Redis::setex($key, $seconds, $serializedValue);
        }
        
        return Redis::set($key, $serializedValue);
    }

    /**
     * Get a value from Redis.
     */
    public static function get(string $key, mixed $default = null): mixed
    {
        $value = Redis::get($key);

        if (is_null($value)) {
            return $default;
        }

        // Try to unserialize if it's a serialized string
        $unserialized = @unserialize($value);
        if ($unserialized !== false || $value === 'b:0;') {
            return $unserialized;
        }

        return $value;
    }

    /**
     * Delete a key from Redis.
     */
    public static function forget(string $key): int
    {
        return Redis::del($key);
    }

    /**
     * Check if a key exists.
     */
    public static function exists(string $key): bool
    {
        return (bool) Redis::exists($key);
    }

    /**
     * Increment a value.
     */
    public static function increment(string $key, int $amount = 1): int
    {
        return Redis::incrby($key, $amount);
    }

    /**
     * Run a command directly on the Redis facade.
     */
    public static function command(string $method, array $parameters = []): mixed
    {
        return Redis::command($method, $parameters);
    }
}
