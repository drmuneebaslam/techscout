<?php
declare(strict_types=1);

namespace App\Models;

use App\Core\Database;

/**
 * Technical Broken Link & 404 Scanner [TS-AUT-68]
 * Validates all outbound links, API endpoints, and GitHub URLs cited within author articles.
 */
class LinkScanner {
    /**
     * Scan outbound links cited across publications
     * 
     * @param int $articleId Optional specific article ID to scan (0 for all published)
     * @param int $limit Max articles to scan per execution
     * @return array Summary metrics and detailed link results
     */
    public static function scanOutboundLinks(int $articleId = 0, int $limit = 30): array {
        $articles = [];
        if ($articleId > 0) {
            $articles = Database::query("SELECT id, title, slug, body_html, summary FROM contents WHERE id = ?", [$articleId]);
        } else {
            $articles = Database::query("SELECT id, title, slug, body_html, summary FROM contents WHERE content_type = 'article' AND status = 'published' ORDER BY updated_at DESC LIMIT ?", [$limit]);
        }

        $allLinks = [];
        $brokenCount = 0;
        $activeCount = 0;
        $totalChecked = 0;

        foreach ($articles as $art) {
            $content = ($art['body_html'] ?? '') . ' ' . ($art['summary'] ?? '');
            if (empty(trim($content))) continue;

            $urls = self::extractUrls($content);
            foreach ($urls as $url) {
                $totalChecked++;
                $check = self::validateUrl($url);
                $isBroken = in_array($check['status_code'], [400, 404, 410, 500, 502, 503, 504], true) || !empty($check['error']);
                
                if ($isBroken) {
                    $brokenCount++;
                } else {
                    $activeCount++;
                }

                $allLinks[] = [
                    'article_id' => (int)$art['id'],
                    'article_title' => $art['title'],
                    'article_slug' => $art['slug'],
                    'url' => $url,
                    'status_code' => $check['status_code'],
                    'status_text' => $check['status_text'],
                    'response_time_ms' => $check['time_ms'],
                    'is_broken' => $isBroken,
                    'error' => $check['error'] ?? null,
                    'checked_at' => date('Y-m-d H:i:s')
                ];
            }
        }

        // Store scan summary in site_settings for quick dashboard access
        try {
            Database::execute(
                "INSERT INTO site_settings (key_name, key_value) VALUES ('last_link_scan_report', ?) ON DUPLICATE KEY UPDATE key_value = ?",
                [json_encode([
                    'total_checked' => $totalChecked,
                    'broken_count' => $brokenCount,
                    'active_count' => $activeCount,
                    'timestamp' => date('c')
                ]), json_encode([
                    'total_checked' => $totalChecked,
                    'broken_count' => $brokenCount,
                    'active_count' => $activeCount,
                    'timestamp' => date('c')
                ])]
            );
        } catch (\Throwable $e) {}

        return [
            'total_checked' => $totalChecked,
            'broken_count' => $brokenCount,
            'active_count' => $activeCount,
            'timestamp' => date('c'),
            'results' => $allLinks
        ];
    }

    /**
     * Extract HTTP/HTTPS links from HTML and Markdown
     */
    public static function extractUrls(string $content): array {
        $urls = [];

        // 1. Match href="..." in HTML
        if (preg_match_all('/href=["\'](https?:\/\/[^"\'>\s]+)["\']/i', $content, $m1)) {
            foreach ($m1[1] as $u) {
                $urls[] = trim($u);
            }
        }

        // 2. Match Markdown links [text](https://...)
        if (preg_match_all('/\[[^\]]*\]\((https?:\/\/[^\)\s]+)\)/i', $content, $m2)) {
            foreach ($m2[1] as $u) {
                $urls[] = trim($u);
            }
        }

        // 3. Raw URLs in code blocks or plain text
        if (preg_match_all('/(?<=^|[\s<])(https?:\/\/[^\s<>"\'\)]+)/i', $content, $m3)) {
            foreach ($m3[1] as $u) {
                $urls[] = trim($u);
            }
        }

        $unique = [];
        foreach ($urls as $u) {
            $u = rtrim($u, '.,;:!?');
            if (filter_var($u, FILTER_VALIDATE_URL) && !isset($unique[$u])) {
                // Ignore localhost or current site internal anchors
                $host = parse_url($u, PHP_URL_HOST);
                if ($host && $host !== 'localhost' && $host !== '127.0.0.1') {
                    $unique[$u] = true;
                }
            }
        }

        return array_keys($unique);
    }

    /**
     * Validate an external URL with HEAD/GET request
     */
    public static function validateUrl(string $url): array {
        $start = microtime(true);
        $statusCode = 0;
        $statusText = 'Unknown';
        $error = null;

        if (function_exists('curl_init')) {
            $ch = curl_init($url);
            curl_setopt_array($ch, [
                CURLOPT_NOBODY => true, // HEAD request
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_MAXREDIRS => 3,
                CURLOPT_TIMEOUT => 4,
                CURLOPT_CONNECTTIMEOUT => 3,
                CURLOPT_USERAGENT => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) TechScout-LinkScanner/2.5 (+https://techscout.online/bot)',
                CURLOPT_SSL_VERIFYPEER => false,
                CURLOPT_SSL_VERIFYHOST => false
            ]);

            curl_exec($ch);
            $statusCode = (int)curl_getinfo($ch, CURLINFO_HTTP_CODE);
            $curlErr = curl_error($ch);
            curl_close($ch);

            // If HEAD was rejected with 403 or 405 (some servers reject HEAD), retry with lightweight GET
            if (in_array($statusCode, [403, 405], true)) {
                $ch2 = curl_init($url);
                curl_setopt_array($ch2, [
                    CURLOPT_RETURNTRANSFER => true,
                    CURLOPT_FOLLOWLOCATION => true,
                    CURLOPT_MAXREDIRS => 2,
                    CURLOPT_TIMEOUT => 3,
                    CURLOPT_RANGE => '0-512', // Only download first 512 bytes
                    CURLOPT_USERAGENT => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) TechScout-LinkScanner/2.5',
                    CURLOPT_SSL_VERIFYPEER => false,
                    CURLOPT_SSL_VERIFYHOST => false
                ]);
                curl_exec($ch2);
                $statusCode = (int)curl_getinfo($ch2, CURLINFO_HTTP_CODE);
                curl_close($ch2);
            }

            if ($statusCode === 0) {
                $error = !empty($curlErr) ? $curlErr : 'Connection failed or timed out';
                $statusText = 'Unreachable / Timeout';
            } elseif ($statusCode >= 200 && $statusCode < 300) {
                $statusText = '200 OK';
            } elseif ($statusCode >= 300 && $statusCode < 400) {
                $statusText = "{$statusCode} Redirect";
            } elseif ($statusCode === 404) {
                $statusText = '404 Not Found';
            } elseif ($statusCode === 410) {
                $statusText = '410 Gone';
            } elseif ($statusCode >= 500) {
                $statusText = "{$statusCode} Server Error";
            } else {
                $statusText = "HTTP {$statusCode}";
            }
        } else {
            // Fallback without cURL
            $ctx = stream_context_create([
                'http' => [
                    'method' => 'HEAD',
                    'timeout' => 3,
                    'user_agent' => 'TechScout-LinkScanner/2.5'
                ]
            ]);
            $headers = @get_headers($url, false, $ctx);
            if ($headers && isset($headers[0])) {
                preg_match('/HTTP\/\S*\s+(\d{3})/', $headers[0], $m);
                $statusCode = isset($m[1]) ? (int)$m[1] : 200;
                $statusText = $headers[0];
            } else {
                $statusCode = 0;
                $error = 'Stream timeout / failed to resolve';
                $statusText = 'Unreachable';
            }
        }

        $timeMs = (int)round((microtime(true) - $start) * 1000);

        return [
            'status_code' => $statusCode,
            'status_text' => $statusText,
            'time_ms' => $timeMs,
            'error' => $error
        ];
    }
}
