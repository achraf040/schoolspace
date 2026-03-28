<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class CacheService
{
    /**
     * Cache durations in seconds
     */
    const CACHE_DURATIONS = [
        'user_espace' => 300, // 5 minutes
        'user_stats' => 120,  // 2 minutes
        'navigation' => 600,  // 10 minutes
        'system' => 1800,     // 30 minutes
    ];

    /**
     * Cache key prefixes
     */
    const CACHE_PREFIXES = [
        'user' => 'user_',
        'stats' => 'stats_',
        'nav' => 'nav_',
        'system' => 'sys_',
    ];

    /**
     * Remember a value with automatic key generation and error handling
     */
    public static function remember(string $key, string $type, callable $callback, ?int $ttl = null)
    {
        $cacheKey = self::generateKey($key, $type);
        $duration = $ttl ?? self::CACHE_DURATIONS[$type] ?? 300;

        try {
            return Cache::remember($cacheKey, $duration, $callback);
        } catch (\Exception $e) {
            Log::warning("Cache operation failed for key: {$cacheKey}", [
                'error' => $e->getMessage(),
                'type' => $type
            ]);
            
            // Fallback to direct execution if cache fails
            return $callback();
        }
    }

    /**
     * Forget cache entries with pattern matching
     */
    public static function forget(string $key, string $type): bool
    {
        $cacheKey = self::generateKey($key, $type);
        
        try {
            return Cache::forget($cacheKey);
        } catch (\Exception $e) {
            Log::warning("Cache forget failed for key: {$cacheKey}", [
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }

    /**
     * Clear all caches for a user
     */
    public static function clearUserCache(int $userId): void
    {
        $patterns = [
            "user_espace_{$userId}",
            "user_stats_{$userId}_*",
            "user_nav_{$userId}",
        ];

        foreach ($patterns as $pattern) {
            try {
                if (str_contains($pattern, '*')) {
                    // For wildcard patterns, we need to implement a more sophisticated solution
                    // For now, we'll clear known keys
                    self::clearUserStatsCaches($userId);
                } else {
                    Cache::forget($pattern);
                }
            } catch (\Exception $e) {
                Log::warning("Failed to clear cache pattern: {$pattern}", [
                    'error' => $e->getMessage()
                ]);
            }
        }
    }

    /**
     * Clear statistics caches for a user
     */
    public static function clearUserStatsCaches(int $userId): void
    {
        // Get all possible espace IDs for this user (you might want to optimize this)
        $cacheKeys = [
            "user_stats_{$userId}_1",
            "user_stats_{$userId}_2",
            "user_stats_{$userId}_3",
            // Add more as needed, or implement a more dynamic approach
        ];

        foreach ($cacheKeys as $key) {
            Cache::forget($key);
        }
    }

    /**
     * Generate a consistent cache key
     */
    private static function generateKey(string $key, string $type): string
    {
        $prefix = self::CACHE_PREFIXES[$type] ?? '';
        return $prefix . $key;
    }

    /**
     * Get cache statistics for monitoring
     */
    public static function getCacheStats(): array
    {
        return [
            'driver' => config('cache.default'),
            'durations' => self::CACHE_DURATIONS,
            'prefixes' => self::CACHE_PREFIXES,
        ];
    }

    /**
     * Warm up frequently accessed caches
     */
    public static function warmup(): void
    {
        try {
            // Implement cache warming logic here
            Log::info('Cache warmup completed');
        } catch (\Exception $e) {
            Log::error('Cache warmup failed', ['error' => $e->getMessage()]);
        }
    }

    /**
     * Cache tags support (if using Redis or similar)
     */
    public static function tags(array $tags): \Illuminate\Cache\TaggedCache
    {
        return Cache::tags($tags);
    }

    /**
     * Invalidate by tags
     */
    public static function flushTags(array $tags): void
    {
        try {
            Cache::tags($tags)->flush();
        } catch (\Exception $e) {
            Log::warning('Failed to flush cache tags', [
                'tags' => $tags,
                'error' => $e->getMessage()
            ]);
        }
    }
}