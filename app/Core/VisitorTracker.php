<?php
declare(strict_types=1);

namespace App\Core;

class VisitorTracker {
    /**
     * Common bots and crawler signatures
     */
    private static array $botSignatures = [
        'googlebot' => 'Googlebot',
        'bingbot' => 'Bingbot',
        'slurp' => 'Yahoo! Slurp',
        'duckduckbot' => 'DuckDuckBot',
        'baiduspider' => 'Baiduspider',
        'yandexbot' => 'YandexBot',
        'sogou' => 'Sogou Spider',
        'exabot' => 'Exabot',
        'facebot' => 'Facebook Bot',
        'facebookexternalhit' => 'Facebook External Hit',
        'ia_archiver' => 'Alexa/Archive',
        'twitterbot' => 'Twitterbot',
        'linkedinbot' => 'LinkedInBot',
        'discordbot' => 'Discordbot',
        'slackbot' => 'Slackbot',
        'applebot' => 'Applebot',
        'ahrefsbot' => 'AhrefsBot',
        'semrushbot' => 'SemrushBot',
        'dotbot' => 'DotBot',
        'mj12bot' => 'MJ12bot',
        'petalbot' => 'PetalBot',
        'curl' => 'cURL Client',
        'wget' => 'Wget Client',
        'python-requests' => 'Python Requests',
        'postmanruntime' => 'Postman Client',
        'httpclient' => 'HTTP Client',
        'go-http-client' => 'Go HTTP Client',
        'node-fetch' => 'Node Fetch',
        'axios' => 'Axios Client',
        'headlesschrome' => 'Headless Chrome'
    ];

    /**
     * File extensions and routes to bypass tracking
     */
    private static array $ignoredExtensions = [
        'css', 'js', 'map', 'jpg', 'jpeg', 'png', 'gif', 'svg', 'webp', 'ico',
        'woff', 'woff2', 'ttf', 'eot', 'otf', 'mp4', 'webm', 'mp3', 'wav',
        'pdf', 'zip', 'tar', 'gz', 'xml', 'txt'
    ];

    /**
     * Core tracking entrypoint called in shutdown or post-dispatch
     */
    public static function track(Request $request, float $startTime = 0.0): void {
        try {
            $uri = $request->getUri();
            $cleanPath = parse_url($uri, PHP_URL_PATH) ?? '';

            // 1. Skip static assets
            $ext = strtolower(pathinfo($cleanPath, PATHINFO_EXTENSION));
            if (!empty($ext) && in_array($ext, self::$ignoredExtensions, true)) {
                return;
            }

            // 2. Skip internal asset paths, service workers, and stream listeners to avoid self-referential loops
            if (
                str_starts_with($cleanPath, '/assets/') ||
                str_starts_with($cleanPath, '/public/assets/') ||
                str_starts_with($cleanPath, '/admin/visitors/stream') ||
                str_starts_with($cleanPath, '/api/v1/deploy/sync') ||
                str_starts_with($cleanPath, '/api/v1/diagnostic-ping') ||
                $cleanPath === '/favicon.ico' ||
                $cleanPath === '/sw.js' ||
                $cleanPath === '/manifest.json'
            ) {
                return;
            }

            // 3. Resolve client IP and User Agent
            $ip = self::resolveClientIp();
            $userAgent = trim((string)($_SERVER['HTTP_USER_AGENT'] ?? 'Unknown'));

            // 4. Detect Bot vs Human
            [$isBot, $botName] = self::detectBot($userAgent);

            // 5. Parse Browser, OS, and Device Type
            $deviceType = self::detectDeviceType($userAgent, $isBot);
            $os = self::detectOS($userAgent);
            [$browser, $browserVersion] = self::detectBrowser($userAgent);

            // 6. Persistent Visitor Cookie & Hash
            $visitorHash = hash('sha256', $ip . '|' . $userAgent);
            $sessionId = session_id() ?: 'sess_' . substr($visitorHash, 0, 16);

            if (empty($_COOKIE['ts_vid']) && !headers_sent()) {
                $vidCookie = bin2hex(random_bytes(16));
                @setcookie('ts_vid', $vidCookie, time() + (86400 * 365), '/', '', false, true);
            }

            // 7. Resolve Geolocation (Cloudflare or DateHelper IP lookup)
            [$countryCode, $countryName, $city, $region] = self::resolveGeoLocation($ip);

            // 8. Capture Authenticated User Details if present
            $userId = null;
            $username = null;
            if (class_exists('\\App\\Core\\Auth') && Auth::check()) {
                $authUser = Auth::user();
                $userId = !empty($authUser['id']) ? (int)$authUser['id'] : null;
                $username = !empty($authUser['username']) ? (string)$authUser['username'] : null;
            }

            // 9. Referrer and Domain
            $referrer = $request->referrer() ?: ($_SERVER['HTTP_REFERER'] ?? null);
            $referrerDomain = null;
            if ($referrer) {
                $refHost = parse_url($referrer, PHP_URL_HOST);
                if ($refHost && $refHost !== ($_SERVER['HTTP_HOST'] ?? '')) {
                    $referrerDomain = strtolower($refHost);
                }
            }

            // 10. Timing and Status Code
            $httpStatus = http_response_code() ?: 200;
            $durationMs = 0.0;
            if ($startTime > 0) {
                $durationMs = round((microtime(true) - $startTime) * 1000, 2);
            }

            // 11. Client Timezone & Language
            $clientLang = substr(trim((string)($_SERVER['HTTP_ACCEPT_LANGUAGE'] ?? '')), 0, 40) ?: null;
            $clientTz = $_COOKIE['user_timezone'] ?? DateHelper::timezone();

            // 12. Persist to Database with UTC Timestamp
            // Save explicitly in UTC format (gmdate)
            $nowUtc = gmdate('Y-m-d H:i:s');

            Database::execute(
                "INSERT INTO visitor_logs 
                 (session_id, visitor_hash, ip_address, country_code, country_name, city, region, 
                  user_id, username, request_method, request_uri, route_name, http_referer, referrer_domain, 
                  user_agent, browser, browser_version, os, device_type, is_bot, bot_name, 
                  http_status, response_time_ms, client_language, client_timezone, created_at)
                 VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)",
                [
                    $sessionId,
                    $visitorHash,
                    $ip,
                    $countryCode,
                    $countryName,
                    $city,
                    $region,
                    $userId,
                    $username,
                    strtoupper($request->method() ?: 'GET'),
                    mb_substr($uri, 0, 490),
                    mb_substr($cleanPath, 0, 95),
                    $referrer ? mb_substr($referrer, 0, 490) : null,
                    $referrerDomain ? mb_substr($referrerDomain, 0, 140) : null,
                    mb_substr($userAgent, 0, 490),
                    $browser,
                    $browserVersion,
                    $os,
                    $deviceType,
                    $isBot ? 1 : 0,
                    $botName,
                    $httpStatus,
                    $durationMs,
                    $clientLang,
                    mb_substr($clientTz, 0, 60),
                    $nowUtc
                ]
            );
        } catch (\Throwable $e) {
            // Failsafe: Never throw or disrupt response delivery
        }
    }

    /**
     * Resolves the real client IP address
     */
    public static function resolveClientIp(): string {
        if (!empty($_SERVER['HTTP_CF_CONNECTING_IP'])) {
            return trim($_SERVER['HTTP_CF_CONNECTING_IP']);
        }
        if (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $parts = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']);
            return trim($parts[0]);
        }
        if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
            return trim($_SERVER['HTTP_CLIENT_IP']);
        }
        return $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1';
    }

    /**
     * Detects bot crawlers and spiders
     */
    private static function detectBot(string $ua): array {
        $lowerUa = strtolower($ua);
        foreach (self::$botSignatures as $needle => $name) {
            if (str_contains($lowerUa, $needle)) {
                return [true, $name];
            }
        }
        if (preg_match('/(bot|crawl|spider|slurp|crawler|mediapartners)/i', $ua)) {
            return [true, 'Generic Web Crawler'];
        }
        return [false, null];
    }

    /**
     * Determines device classification
     */
    private static function detectDeviceType(string $ua, bool $isBot): string {
        if ($isBot) {
            return 'bot';
        }
        if (preg_match('/(tablet|ipad|playbook|silk)|(android(?!.*mobi))/i', $ua)) {
            return 'tablet';
        }
        if (preg_match('/(mobi|ipod|iphone|android|blackberry|opera mini|iemobile)/i', $ua)) {
            return 'mobile';
        }
        return 'desktop';
    }

    /**
     * Detects client Operating System
     */
    private static function detectOS(string $ua): string {
        if (preg_match('/windows nt 10/i', $ua)) return 'Windows 10/11';
        if (preg_match('/windows nt 6\.3/i', $ua)) return 'Windows 8.1';
        if (preg_match('/windows nt 6\.2/i', $ua)) return 'Windows 8';
        if (preg_match('/windows nt 6\.1/i', $ua)) return 'Windows 7';
        if (preg_match('/windows/i', $ua)) return 'Windows';
        if (preg_match('/macintosh|mac os x/i', $ua)) return 'macOS';
        if (preg_match('/iphone/i', $ua)) return 'iOS (iPhone)';
        if (preg_match('/ipad/i', $ua)) return 'iPadOS';
        if (preg_match('/android/i', $ua)) return 'Android';
        if (preg_match('/cros/i', $ua)) return 'ChromeOS';
        if (preg_match('/linux/i', $ua)) return 'Linux';
        return 'Unknown OS';
    }

    /**
     * Detects client Web Browser and major version
     */
    private static function detectBrowser(string $ua): array {
        $browser = 'Unknown Browser';
        $version = null;

        if (preg_match('/edg[ea]?\/([0-9.]+)/i', $ua, $matches)) {
            $browser = 'Microsoft Edge';
            $version = $matches[1] ?? null;
        } elseif (preg_match('/opr\/([0-9.]+)/i', $ua, $matches)) {
            $browser = 'Opera';
            $version = $matches[1] ?? null;
        } elseif (preg_match('/chrome\/([0-9.]+)/i', $ua, $matches)) {
            $browser = 'Google Chrome';
            $version = $matches[1] ?? null;
        } elseif (preg_match('/firefox\/([0-9.]+)/i', $ua, $matches)) {
            $browser = 'Mozilla Firefox';
            $version = $matches[1] ?? null;
        } elseif (preg_match('/version\/([0-9.]+).*safari/i', $ua, $matches)) {
            $browser = 'Apple Safari';
            $version = $matches[1] ?? null;
        } elseif (preg_match('/brave\/([0-9.]+)/i', $ua, $matches)) {
            $browser = 'Brave';
            $version = $matches[1] ?? null;
        }

        return [$browser, $version ? substr($version, 0, 20) : null];
    }

    /**
     * Resolves country and city with Cloudflare & IP cache
     */
    private static function resolveGeoLocation(string $ip): array {
        $countryCode = null;
        $countryName = null;
        $city = null;
        $region = null;

        // 1. Cloudflare Country Header (0ms)
        if (!empty($_SERVER['HTTP_CF_IPCOUNTRY'])) {
            $countryCode = strtoupper(trim($_SERVER['HTTP_CF_IPCOUNTRY']));
            $countryName = self::countryCodeToName($countryCode);
        }

        // 2. Localhost fallback
        if ($ip === '127.0.0.1' || $ip === '::1' || str_starts_with($ip, '192.168.')) {
            return ['PK', 'Pakistan (Localhost)', 'Karachi', 'Sindh'];
        }

        // 3. Session / Cache check
        $geoKey = 'geo_' . md5($ip);
        if (session_status() === PHP_SESSION_ACTIVE && !empty($_SESSION[$geoKey])) {
            return $_SESSION[$geoKey];
        }

        $result = [$countryCode ?: 'XX', $countryName ?: 'Global / Unknown', $city, $region];
        if (session_status() === PHP_SESSION_ACTIVE) {
            $_SESSION[$geoKey] = $result;
        }

        return $result;
    }

    /**
     * Maps ISO country code to Country name
     */
    private static function countryCodeToName(string $code): string {
        $names = [
            'PK' => 'Pakistan',
            'US' => 'United States',
            'GB' => 'United Kingdom',
            'CA' => 'Canada',
            'AU' => 'Australia',
            'DE' => 'Germany',
            'FR' => 'France',
            'IN' => 'India',
            'AE' => 'United Arab Emirates',
            'SA' => 'Saudi Arabia',
            'TR' => 'Turkey',
            'JP' => 'Japan',
            'CN' => 'China',
            'SG' => 'Singapore',
            'MY' => 'Malaysia',
            'BD' => 'Bangladesh',
            'ID' => 'Indonesia',
            'NL' => 'Netherlands',
            'IT' => 'Italy',
            'ES' => 'Spain',
            'BR' => 'Brazil',
            'RU' => 'Russia',
            'KR' => 'South Korea',
            'ZA' => 'South Africa',
            'EG' => 'Egypt',
            'NG' => 'Nigeria',
            'SE' => 'Sweden',
            'CH' => 'Switzerland',
            'PL' => 'Poland',
            'BE' => 'Belgium',
            'AT' => 'Austria',
            'IE' => 'Ireland',
            'NZ' => 'New Zealand',
            'MX' => 'Mexico',
            'AR' => 'Argentina',
            'CL' => 'Chile',
            'CO' => 'Colombia',
            'PH' => 'Philippines',
            'VN' => 'Vietnam',
            'TH' => 'Thailand',
            'QA' => 'Qatar',
            'KW' => 'Kuwait',
            'OM' => 'Oman',
            'BH' => 'Bahrain',
            'NO' => 'Norway',
            'DK' => 'Denmark',
            'FI' => 'Finland',
            'PT' => 'Portugal',
            'GR' => 'Greece',
            'CZ' => 'Czech Republic',
            'RO' => 'Romania',
            'HU' => 'Hungary',
            'UA' => 'Ukraine'
        ];
        return $names[strtoupper($code)] ?? $code;
    }
}
