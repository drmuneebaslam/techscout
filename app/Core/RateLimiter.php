<?php
declare(strict_types=1);

namespace App\Core;

/**
 * TechScout Enterprise Sliding-Window Rate Limiter Engine [TS-SEC-010]
 * Protects public API endpoints, authentication flows, and search routes
 * against brute force attacks, scrapers, and volumetric abuse.
 */
class RateLimiter {
    private static string $storageDir = '';

    private static function getStorageDir(): string {
        if (self::$storageDir === '') {
            self::$storageDir = dirname(__DIR__, 2) . '/storage/cache/ratelimit';
            if (!is_dir(self::$storageDir)) {
                @mkdir(self::$storageDir, 0777, true);
            }
        }
        return self::$storageDir;
    }

    private static function getFilePath(string $key): string {
        return self::getStorageDir() . '/' . md5($key) . '.json';
    }

    /**
     * Increment attempts for a given rate limit key
     */
    public static function hit(string $key, int $decaySeconds = 60): int {
        $file = self::getFilePath($key);
        $now = time();
        $data = ['reset_at' => $now + $decaySeconds, 'attempts' => 0];

        if (file_exists($file)) {
            $content = @file_get_contents($file);
            $parsed = $content ? json_decode($content, true) : null;
            if ($parsed && isset($parsed['reset_at']) && $parsed['reset_at'] > $now) {
                $data = $parsed;
            }
        }

        $data['attempts']++;
        @file_put_contents($file, json_encode($data), LOCK_EX);
        return (int)$data['attempts'];
    }

    /**
     * Check if attempts exceed maximum allowed
     */
    public static function tooManyAttempts(string $key, int $maxAttempts): bool {
        $file = self::getFilePath($key);
        $now = time();

        if (!file_exists($file)) {
            return false;
        }

        $content = @file_get_contents($file);
        $parsed = $content ? json_decode($content, true) : null;
        if (!$parsed || !isset($parsed['reset_at']) || $parsed['reset_at'] <= $now) {
            return false;
        }

        return (int)($parsed['attempts'] ?? 0) >= $maxAttempts;
    }

    /**
     * Get remaining available attempts
     */
    public static function remaining(string $key, int $maxAttempts): int {
        $file = self::getFilePath($key);
        $now = time();

        if (!file_exists($file)) {
            return $maxAttempts;
        }

        $content = @file_get_contents($file);
        $parsed = $content ? json_decode($content, true) : null;
        if (!$parsed || !isset($parsed['reset_at']) || $parsed['reset_at'] <= $now) {
            return $maxAttempts;
        }

        return max(0, $maxAttempts - (int)($parsed['attempts'] ?? 0));
    }

    /**
     * Seconds until rate limit bucket resets
     */
    public static function availableIn(string $key): int {
        $file = self::getFilePath($key);
        $now = time();

        if (!file_exists($file)) {
            return 0;
        }

        $content = @file_get_contents($file);
        $parsed = $content ? json_decode($content, true) : null;
        if (!$parsed || !isset($parsed['reset_at']) || $parsed['reset_at'] <= $now) {
            return 0;
        }

        return max(0, (int)$parsed['reset_at'] - $now);
    }

    /**
     * Reset/clear key
     */
    public static function clear(string $key): void {
        $file = self::getFilePath($key);
        if (file_exists($file)) {
            @unlink($file);
        }
    }

    /**
     * Guard a request: increments hit, checks limit, sets standard headers, and aborts with 429 if exceeded.
     */
    public static function guard(string $actionKey, int $maxAttempts = 60, int $decaySeconds = 60): void {
        $ip = Request::ip();
        $rateKey = "rl:{$actionKey}:{$ip}";

        if (self::tooManyAttempts($rateKey, $maxAttempts)) {
            $retryAfter = self::availableIn($rateKey);
            header('HTTP/1.1 429 Too Many Requests');
            header('Retry-After: ' . $retryAfter);
            header('X-RateLimit-Limit: ' . $maxAttempts);
            header('X-RateLimit-Remaining: 0');
            header('X-RateLimit-Reset: ' . (time() + $retryAfter));

            if (str_starts_with($_SERVER['REQUEST_URI'] ?? '', '/api/')) {
                header('Content-Type: application/json');
                echo json_encode([
                    'error' => 'Rate limit exceeded. Too many requests.',
                    'retry_after_seconds' => $retryAfter
                ]);
            } else {
                Response::abort(429, "Rate limit exceeded. Please wait {$retryAfter} seconds before trying again.");
            }
            exit;
        }

        $hits = self::hit($rateKey, $decaySeconds);
        $remaining = max(0, $maxAttempts - $hits);
        header('X-RateLimit-Limit: ' . $maxAttempts);
        header('X-RateLimit-Remaining: ' . $remaining);
    }
}
