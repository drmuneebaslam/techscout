<?php
namespace App\Core;

class DateHelper {
    /**
     * ISO 2-letter Country Code to primary IANA Timezone mapping table
     */
    public static array $countryTimezones = [
        'PK' => 'Asia/Karachi',
        'US' => 'America/New_York',
        'GB' => 'Europe/London',
        'UK' => 'Europe/London',
        'CA' => 'America/Toronto',
        'AU' => 'Australia/Sydney',
        'DE' => 'Europe/Berlin',
        'FR' => 'Europe/Paris',
        'IN' => 'Asia/Kolkata',
        'AE' => 'Asia/Dubai',
        'SA' => 'Asia/Riyadh',
        'TR' => 'Europe/Istanbul',
        'JP' => 'Asia/Tokyo',
        'CN' => 'Asia/Shanghai',
        'SG' => 'Asia/Singapore',
        'MY' => 'Asia/Kuala_Lumpur',
        'BD' => 'Asia/Dhaka',
        'ID' => 'Asia/Jakarta',
        'NL' => 'Europe/Amsterdam',
        'IT' => 'Europe/Rome',
        'ES' => 'Europe/Madrid',
        'BR' => 'America/Sao_Paulo',
        'RU' => 'Europe/Moscow',
        'KR' => 'Asia/Seoul',
        'ZA' => 'Africa/Johannesburg',
        'EG' => 'Africa/Cairo',
        'NG' => 'Africa/Lagos',
        'SE' => 'Europe/Stockholm',
        'CH' => 'Europe/Zurich',
        'PL' => 'Europe/Warsaw',
        'BE' => 'Europe/Brussels',
        'AT' => 'Europe/Vienna',
        'IE' => 'Europe/Dublin',
        'NZ' => 'Pacific/Auckland',
        'MX' => 'America/Mexico_City',
        'AR' => 'America/Argentina/Buenos_Aires',
        'CL' => 'America/Santiago',
        'CO' => 'America/Bogota',
        'PE' => 'America/Lima',
        'PH' => 'Asia/Manila',
        'VN' => 'Asia/Ho_Chi_Minh',
        'TH' => 'Asia/Bangkok',
        'QA' => 'Asia/Qatar',
        'KW' => 'Asia/Kuwait',
        'OM' => 'Asia/Muscat',
        'BH' => 'Asia/Bahrain',
        'JO' => 'Asia/Amman',
        'LB' => 'Asia/Beirut',
        'HK' => 'Asia/Hong_Kong',
        'TW' => 'Asia/Taipei',
        'IL' => 'Asia/Jerusalem',
        'NO' => 'Europe/Oslo',
        'DK' => 'Europe/Copenhagen',
        'FI' => 'Europe/Helsinki',
        'PT' => 'Europe/Lisbon',
        'GR' => 'Europe/Athens',
        'CZ' => 'Europe/Prague',
        'RO' => 'Europe/Bucharest',
        'HU' => 'Europe/Budapest',
        'UA' => 'Europe/Kyiv',
    ];

    /**
     * Resolves the real client IP address through proxies, load balancers, and Cloudflare.
     */
    public static function getClientIp(): string {
        if (!empty($_SERVER['HTTP_CF_CONNECTING_IP'])) {
            return trim($_SERVER['HTTP_CF_CONNECTING_IP']);
        }
        if (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $ips = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']);
            return trim($ips[0]);
        }
        if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
            return trim($_SERVER['HTTP_CLIENT_IP']);
        }
        return $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1';
    }

    /**
     * Automatically detects timezone by visitor IP address / IP location.
     * Uses session cache, Cloudflare headers, country mapping, and fast ip-api lookup.
     */
    public static function detectTimezoneByIp(?string $ip = null): string {
        $clientIp = $ip ?: self::getClientIp();

        // 1. Session Cache Check (0ms)
        $cacheKey = 'ip_tz_' . md5($clientIp);
        if (session_status() === PHP_SESSION_ACTIVE && !empty($_SESSION[$cacheKey])) {
            $cached = (string)$_SESSION[$cacheKey];
            if (in_array($cached, \DateTimeZone::listIdentifiers(), true)) {
                return $cached;
            }
        }

        // 2. Cloudflare Timezone Header (if provided by reverse proxy)
        if (!empty($_SERVER['HTTP_CF_TIMEZONE'])) {
            $cfTz = trim($_SERVER['HTTP_CF_TIMEZONE']);
            if (in_array($cfTz, \DateTimeZone::listIdentifiers(), true)) {
                self::cacheIpTimezone($cacheKey, $cfTz);
                return $cfTz;
            }
        }

        // 3. Localhost / Private Network Detection (Default to Pakistan Standard Time)
        if ($clientIp === '127.0.0.1' || $clientIp === '::1' || str_starts_with($clientIp, '192.168.') || str_starts_with($clientIp, '10.') || str_starts_with($clientIp, '172.16.')) {
            $localTz = 'Asia/Karachi';
            self::cacheIpTimezone($cacheKey, $localTz);
            return $localTz;
        }

        // 4. Cloudflare Country Code Header Lookup (0ms overhead)
        if (!empty($_SERVER['HTTP_CF_IPCOUNTRY'])) {
            $cc = strtoupper(trim($_SERVER['HTTP_CF_IPCOUNTRY']));
            if (isset(self::$countryTimezones[$cc])) {
                $cfCountryTz = self::$countryTimezones[$cc];
                self::cacheIpTimezone($cacheKey, $cfCountryTz);
                return $cfCountryTz;
            }
        }

        // 5. Fast IP Geolocation Lookup via ip-api.com (1.2s strict timeout)
        try {
            $url = 'http://ip-api.com/json/' . urlencode($clientIp) . '?fields=status,countryCode,timezone';
            $ctx = stream_context_create([
                'http' => [
                    'timeout' => 1.2,
                    'ignore_errors' => true,
                    'header' => "User-Agent: TechScout-Geo/1.0\r\n"
                ]
            ]);
            $raw = @file_get_contents($url, false, $ctx);
            if ($raw) {
                $data = json_decode($raw, true);
                if (!empty($data['status']) && $data['status'] === 'success' && !empty($data['timezone'])) {
                    $resolvedTz = trim((string)$data['timezone']);
                    if (in_array($resolvedTz, \DateTimeZone::listIdentifiers(), true)) {
                        self::cacheIpTimezone($cacheKey, $resolvedTz);
                        return $resolvedTz;
                    }
                }
                if (!empty($data['countryCode']) && isset(self::$countryTimezones[$data['countryCode']])) {
                    $countryTz = self::$countryTimezones[$data['countryCode']];
                    self::cacheIpTimezone($cacheKey, $countryTz);
                    return $countryTz;
                }
            }
        } catch (\Throwable $e) {}

        // 6. Platform Storage Default
        $defaultTz = 'Asia/Karachi';
        self::cacheIpTimezone($cacheKey, $defaultTz);
        return $defaultTz;
    }

    private static function cacheIpTimezone(string $cacheKey, string $tz): void {
        if (session_status() === PHP_SESSION_ACTIVE) {
            $_SESSION[$cacheKey] = $tz;
        }
        if (!headers_sent() && empty($_COOKIE['user_timezone'])) {
            @setcookie('user_timezone', $tz, time() + 31536000, '/', '', false, false);
            $_COOKIE['user_timezone'] = $tz;
        }
    }

    /**
     * Resolves the user's active timezone with robust fallbacks:
     * 1. Authenticated user profile preference (if defined and valid)
     * 2. Auto-detected 'user_timezone' cookie
     * 3. Automatic IP Location Geolocation resolution
     * 4. Default platform storage timezone 'Asia/Karachi' (UTC+5)
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

        // Auto-detect by visitor IP location
        return self::detectTimezoneByIp();
    }

    /**
     * Converts a database timestamp (stored in Asia/Karachi, UTC+5) to a DateTime in target timezone.
     * Conversion path: PKT (Asia/Karachi) -> GMT (UTC) -> Target Timezone.
     */
    public static function toDateTime(string|int|null $time, ?string $targetTimezone = null): ?\DateTime {
        if (!$time) return null;
        $targetTz = $targetTimezone ?: self::timezone();
        try {
            $tzObj = new \DateTimeZone($targetTz);
            $appTz = new \DateTimeZone('Asia/Karachi');
            $utcTz = new \DateTimeZone('UTC');

            if (is_numeric($time)) {
                $dt = new \DateTime('@' . (int)$time);
                $dt->setTimezone($utcTz);
                $dt->setTimezone($tzObj);
            } else {
                $timeStr = trim((string)$time);
                if ($timeStr === '' || $timeStr === '0000-00-00 00:00:00') return null;
                // If the string already has a timezone indicator (+05:00, Z, etc.)
                if (preg_match('/[+-]\d{2}:?\d{2}|Z$/', $timeStr)) {
                    $dt = new \DateTime($timeStr);
                } else {
                    // Raw database DATETIME strings were saved in Pakistan Standard Time (Asia/Karachi, UTC+5)
                    $dt = new \DateTime($timeStr, $appTz);
                }
                // Step 1: Explicitly convert PKT to GMT/UTC
                $dt->setTimezone($utcTz);
                // Step 2: Convert from GMT/UTC to target visitor timezone
                $dt->setTimezone($tzObj);
            }
            return $dt;
        } catch (\Throwable $e) {
            return null;
        }
    }

    /**
     * Generates a standardized UTC ISO-8601 string ending in 'Z' for universal client-side JavaScript date parsing.
     * Ensures consistent browser local time rendering worldwide without offset ambiguity.
     */
    public static function iso8601(string|int|null $time): string {
        $dt = self::toDateTime($time, 'UTC');
        return $dt ? $dt->format('Y-m-d\TH:i:s\Z') : '';
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
    public static function render(string|int|null $time, string $format = 'M j, Y • g:i A', string $formatType = 'datetime', string $extraClass = '', ?string $targetTimezone = null): string {
        $iso = self::iso8601($time);
        $tz = $targetTimezone ?: self::timezone();
        $dt = self::toDateTime($time, $tz);
        $display = $dt ? $dt->format(str_replace('•', '\•', $format)) : 'Just now';
        $escapedTitle = htmlspecialchars($display . ' (' . $tz . ')', ENT_QUOTES, 'UTF-8');
        $classes = trim('ts-time ' . $extraClass);
        return '<time class="' . htmlspecialchars($classes) . '" datetime="' . htmlspecialchars($iso) . '" data-format-type="' . htmlspecialchars($formatType) . '" title="' . $escapedTitle . '">' . htmlspecialchars($display) . '</time>';
    }

    /**
     * Renders a compact two-line timestamp (Bold Time on line 1, M j, Y Date + TZ on line 2).
     */
    public static function renderTwoLines(string|int|null $time, string $extraClass = '', ?string $targetTimezone = null): string {
        $iso = self::iso8601($time);
        $tzName = $targetTimezone ?: self::timezone();
        $dt = self::toDateTime($time, $tzName);
        $timeStr = $dt ? $dt->format('H:i:s') : '00:00:00';
        $dateStr = $dt ? $dt->format('M j, Y') : '';
        $tzAbbr = $dt ? $dt->format('T') : '';
        $classes = trim('ts-time ts-time-two-lines ' . $extraClass);
        $escapedTitle = htmlspecialchars(($dt ? $dt->format('M j, Y • H:i:s') : '') . ' (' . $tzName . ')', ENT_QUOTES, 'UTF-8');

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
