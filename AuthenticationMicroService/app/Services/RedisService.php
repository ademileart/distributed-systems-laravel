<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;

class RedisService
{
    private static self|null $instance = null;

    public static function getInstance(): self
    {
        if (self::$instance === null)
            self::$instance = new self();
        return self::$instance;
    }

    public function putInCache(string $key, $value, int $minutes)
    {
        Cache::put($key, $value, $minutes);
        return true;
    }

    public function getFromCache(string $key)
    {
        return Cache::get($key);
    }
    public function deleteFromCache(string $key): bool
    {
        return Cache::delete($key);
    }
}
