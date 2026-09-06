<?php
namespace App\Core;

class DateHelper {
    public static function timezone(): string {
        $user = Auth::user();
        if (!empty($user['timezone']) && in_array($user['timezone'], \DateTimeZone::listIdentifiers(), true)) {
            return $user['timezone'];
        }
        if (!empty($_COOKIE['user_timezone'])) {
            $cookieTz = trim($_COOKIE['user_timezone']);
            if (in_array($cookieTz, \DateTimeZone::listIdentifiers(), true)) {
                return $cookieTz;
            }
        }
        return 'Asia/Karachi';
    }

    public static function format(string|int|null $time, string $format = 'M j, Y • g:i A'): string {
        if (!$time) return 'Just now';
        
        $targetTz = self::timezone();
        try {
            $tzObj = new \DateTimeZone($targetTz);
            $appTz = new \DateTimeZone('Asia/Karachi');

            if (is_numeric($time)) {
                $dt = new \DateTime('@' . (int)$time);
                $dt->setTimezone($tzObj);
            } else {
                $timeStr = trim((string)$time);
                // If the string already has a timezone indicator (+05:00, Z, etc.)
                if (preg_match('/[+-]\d{2}:?\d{2}|Z$/', $timeStr)) {
                    $dt = new \DateTime($timeStr);
                } else {
                    // Raw database DATETIME strings were stored in Asia/Karachi (UTC+5)
                    $dt = new \DateTime($timeStr, $appTz);
                }
                $dt->setTimezone($tzObj);
            }
            
            // Format with literal bullet separator escaped
            $escapedFormat = str_replace('•', '\•', $format);
            return $dt->format($escapedFormat);
        } catch (\Throwable $e) {
            $ts = is_numeric($time) ? (int)$time : strtotime((string)$time);
            return $ts ? date('M j, Y • g:i A', $ts) : 'Just now';
        }
    }

    public static function formatDateTime(string|int|null $time): string {
        return self::format($time, 'M j, Y • g:i A');
    }

    public static function formatDate(string|int|null $time): string {
        return self::format($time, 'M j, Y');
    }

    public static function formatTime(string|int|null $time): string {
        return self::format($time, 'g:i A');
    }

    public static function relative(string|int|null $time): string {
        if (!$time) return 'Just now';
        $timestamp = is_numeric($time) ? (int)$time : strtotime((string)$time);
        if (!$timestamp) return 'Just now';

        $diff = time() - $timestamp;
        if ($diff < 60) return 'Just now';
        if ($diff < 3600) return floor($diff / 60) . 'm ago';
        if ($diff < 86400) return floor($diff / 3600) . 'h ago';
        if ($diff < 604800) return floor($diff / 86400) . 'd ago';
        return self::format($time, 'M j, Y');
    }
}
