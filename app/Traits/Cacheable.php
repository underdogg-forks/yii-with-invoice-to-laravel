<?php

namespace App\Traits;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

trait Cacheable
{
    /**
     * Cache TTL in seconds.
     */
    protected int $cacheTtl = 3600;

    /**
     * Boot the trait.
     */
    protected static function bootCacheable(): void
    {
        static::saved(function (Model $model) {
            $model->clearCache();
        });

        static::deleted(function (Model $model) {
            $model->clearCache();
        });
    }

    /**
     * Get cached model.
     */
    public static function cached(mixed $id): ?static
    {
        $cacheKey = static::getCacheKey($id);
        
        return Cache::remember($cacheKey, (new static())->getCacheTtl(), function () use ($id) {
            return static::find($id);
        });
    }

    /**
     * Get cache key for model.
     */
    public static function getCacheKey(mixed $id): string
    {
        return static::class . ':' . $id;
    }

    /**
     * Clear model cache.
     */
    public function clearCache(): void
    {
        Cache::forget(static::getCacheKey($this->getKey()));
    }

    /**
     * Get cache TTL.
     */
    protected function getCacheTtl(): int
    {
        return $this->cacheTtl;
    }

    /**
     * Set cache TTL.
     */
    public function setCacheTtl(int $ttl): static
    {
        $this->cacheTtl = $ttl;
        
        return $this;
    }
}
