<?php
namespace App\Core;

class DateHelper {
    /**
     * Resolves the user's active timezone with robust fallbacks:
     * 1. Authenticated user preference (if defined and valid)
     * 2. Browser auto-detected 'user_timezone' cookie (e.g. 'America/New_York', 'Europe/London')
     * 3. Default platform storage timezone 'Asia/Karachi' (UTC+5)
     */
    public static function timezone(): string {
        try {
            if (class_exists('\\App\\Core\\Auth') && \App\Core\Auth::check()) {
                $user = \App\Core\Auth::user();
                if (!empty($user['timezone']) && in_array($user['timezone'], \DateTimeZone::listIdentifiers(), true)) {
                    return $user['timezone'];
                }
            }
        } catch (\Throwable $e) {}

        if (!empty($_COOKIE['user_timezone'])) {
            $cookieTz = trim(rawurldecode((string)$_COOKIE['user_timezone']));
            if (in_array($cookieTz, \DateTimeZone::listIdentifiers(), true)) {
                return $cookieTz;
            }
        }

        return 'Asia/Karachi';
    }

    /**
     * Converts a database timestamp (stored in Asia/Karachi, UTC+5) to a DateTime in target timezone.
     */
    public static function toDateTime(string|int|null $time, ?string $targetTimezone = null): ?\DateTime {
        if (!$time) return null;
        $targetTz = $targetTimezone ?: self::timezone();
        try {
            $tzObj = new \DateTimeZone($targetTz);
            $appTz = new \DateTimeZone('Asia/Karachi');

            if (is_numeric($time)) {
                $dt = new \DateTime('@' . (int)$time);
                $dt->setTimezone($tzObj);
            } else {
                $timeStr = trim((string)$time);
                if ($timeStr === '' || $timeStr === '0000-00-00 00:00:00') return null;
                // If the string already has a timezone indicator (+05:00, Z, etc.)
                if (preg_match('/[+-]\d{2}:?\d{2}|Z$/', $timeStr)) {
                    $dt = new \DateTime($timeStr);
                } else {
                    // Raw database DATETIME strings were saved in Asia/Karachi (UTC+5)
                    $dt = new \DateTime($timeStr, $appTz);
                }
                $dt->setTimezone($tzObj);
            }
            return $dt;
        } catch (\Throwable $e) {
            return null;
        }
    }

    /**
     * Generates a standardized ISO-8601 string (with explicit +05:00 offset) for client-side JavaScript date parsing.
     */
    public static function iso8601(string|int|null $time): string {
        $dt = self::toDateTime($time, 'Asia/Karachi');
        return $dt ? $dt->format(\DateTimeInterface::ATOM) : '';
    }

    /**
     * Formats timestamp into target user's local timezone.
     */
    public static function format(string|int|null $time, string $format = 'M j, Y • g:i A'): string {
        $dt = self::toDateTime($time);
        if (!$dt) return 'Just now';
        $escapedFormat = str_replace('•', '\•', $format);
        return $dt->format($escapedFormat);
    }

    public static function formatDateTime(string|int|null $time): string {
        return self::format($time, 'M j, Y • g:i A');
    }

    public static function formatDate(string|int|null $time): string {
        return self::format($time, 'M j, Y');
    }

    public static function formatTime(string|int|null $time, bool $withSeconds = true): string {
        return self::format($time, $withSeconds ? 'H:i:s' : 'g:i A');
    }

    /**
     * Renders a responsive, self-localizing <time> element:
     * - Pre-rendered on server in detected timezone.
     * - Client-side JS seamlessly adjusts if the browser's exact timezone differs.
     */
    public static function render(string|int|null $time, string $format = 'M j, Y • g:i A', string $formatType = 'datetime', string $extraClass = ''): string {
        $iso = self::iso8601($time);
        $display = self::format($time, $format);
        $escapedTitle = htmlspecialchars($display . ' (' . self::timezone() . ')', ENT_QUOTES, 'UTF-8');
        $classes = trim('ts-time ' . $extraClass);
        return '<time class="' . htmlspecialchars($classes) . '" datetime="' . htmlspecialchars($iso) . '" data-format-type="' . htmlspecialchars($formatType) . '" title="' . $escapedTitle . '">' . htmlspecialchars($display) . '</time>';
    }

    /**
     * Renders a compact two-line timestamp (Bold Time on line 1, M j, Y Date + TZ on line 2).
     */
    public static function renderTwoLines(string|int|null $time, string $extraClass = ''): string {
        $iso = self::iso8601($time);
        $timeStr = self::format($time, 'H:i:s');
        $dateStr = self::format($time, 'M j, Y');
        $dt = self::toDateTime($time);
        $tzAbbr = $dt ? $dt->format('T') : '';
        $tzName = self::timezone();
        $classes = trim('ts-time ts-time-two-lines ' . $extraClass);
        $escapedTitle = htmlspecialchars(self::format($time, 'M j, Y • H:i:s') . ' (' . $tzName . ')', ENT_QUOTES, 'UTF-8');

        return '<time class="' . htmlspecialchars($classes) . '" datetime="' . htmlspecialchars($iso) . '" data-format-type="compact_two_lines" title="' . $escapedTitle . '">'
             . '<div class="ts-time-part">' . htmlspecialchars($timeStr) . '</div>'
             . '<div class="ts-date-part">' . htmlspecialchars($dateStr) . ($tzAbbr ? ' <span class="ts-tz-abbr">(' . htmlspecialchars($tzAbbr) . ')</span>' : '') . '</div>'
             . '</time>';
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
