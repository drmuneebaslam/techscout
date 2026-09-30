<?php
declare(strict_types=1);

namespace App\Core;

/**
 * PricingEngine: Real-Time Cryptocurrency Spot Rates & Currency Conversion Engine
 *
 * Supports CoinGecko free public API with automatic failover to Binance public tickers
 * and resilient database caching to prevent rate limiting.
 */
class PricingEngine {
    
    private const DEFAULT_TTL = 120; // 2 minutes cache window
    
    /**
     * Get live exchange rates for BTC, ETH, SOL, USDT in USD.
     * Uses database caching with configured TTL.
     *
     * @param bool $forceRefresh Bypass cache and force network request
     * @return array
     */
    public static function getRates(bool $forceRefresh = false): array {
        if (!$forceRefresh) {
            $cached = self::getCachedRates();
            if ($cached !== null) {
                return $cached;
            }
        }

        // Fetch fresh rates from primary or failover provider
        $provider = self::getConfiguredProvider();
        $rates = null;
        $source = 'CoinGecko API';

        if ($provider === 'binance') {
            $rates = self::fetchFromBinance();
            $source = 'Binance API';
        } else {
            $rates = self::fetchFromCoinGecko();
            if (!$rates) {
                // Failover to Binance
                $rates = self::fetchFromBinance();
                $source = 'Binance API (Failover)';
            }
        }

        // If network fetch failed, use stored cache even if expired, or baseline fallbacks
        if (!$rates) {
            $fallback = self::getFallbackRates();
            $fallback['source'] = 'Offline Baseline Fallback';
            $fallback['cached'] = true;
            return $fallback;
        }

        $rates['source'] = $source;
        $rates['cached'] = false;
        $rates['updated_at'] = date('Y-m-d H:i:s');

        // Cache in site_settings
        self::saveCachedRates($rates);

        return $rates;
    }

    /**
     * Test the configured live pricing API and return diagnostic latency & payload.
     *
     * @return array
     */
    public static function testApiConnection(): array {
        $startTime = microtime(true);
        $provider = self::getConfiguredProvider();

        $rates = ($provider === 'binance') ? self::fetchFromBinance() : self::fetchFromCoinGecko();
        $endTime = microtime(true);
        $latencyMs = round(($endTime - $startTime) * 1000, 2);

        if ($rates) {
            return [
                'success' => true,
                'provider' => $provider,
                'latency_ms' => $latencyMs,
                'rates' => $rates['rates'],
                'message' => "Successfully connected to {$provider} in {$latencyMs}ms. Live market rates retrieved."
            ];
        }

        return [
            'success' => false,
            'provider' => $provider,
            'latency_ms' => $latencyMs,
            'rates' => self::getFallbackRates()['rates'],
            'message' => "Could not reach {$provider} (Connection timed out or rate limit reached). Active baseline fallback engaged."
        ];
    }

    /**
     * Convert a USD amount to cryptocurrency equivalents based on live spot rates.
     *
     * @param float $amountUsd
     * @return array
     */
    public static function convertUsdToCrypto(float $amountUsd): array {
        $rates = self::getRates();
        $r = $rates['rates'];

        $btcP = !empty($r['BTC']['price']) ? (float)$r['BTC']['price'] : 64250.0;
        $ethP = !empty($r['ETH']['price']) ? (float)$r['ETH']['price'] : 3450.0;
        $solP = !empty($r['SOL']['price']) ? (float)$r['SOL']['price'] : 145.0;
        $usdtP = !empty($r['USDT']['price']) ? (float)$r['USDT']['price'] : 1.0;

        return [
            'usd' => round($amountUsd, 2),
            'usdt' => round($amountUsd / $usdtP, 2),
            'btc' => round($amountUsd / $btcP, 6),
            'eth' => round($amountUsd / $ethP, 5),
            'sol' => round($amountUsd / $solP, 3),
            'rates' => $r,
            'source' => $rates['source'] ?? 'CoinGecko API',
            'updated_at' => $rates['updated_at'] ?? date('Y-m-d H:i:s')
        ];
    }

    // ------------------------------------------------------------------------
    // PROVIDER INTEGRATIONS
    // ------------------------------------------------------------------------

    private static function fetchFromCoinGecko(): ?array {
        $url = 'https://api.coingecko.com/api/v3/simple/price?ids=bitcoin,ethereum,solana,tether&vs_currencies=usd&include_24hr_change=true';
        $apiKey = trim((string)(Database::fetch("SELECT key_value FROM site_settings WHERE key_name = 'pricing_api_key'")['key_value'] ?? ''));

        $headers = [
            'Accept: application/json',
            'User-Agent: TechScout-Pricing-Engine/2.0'
        ];
        if (!empty($apiKey)) {
            $headers[] = 'x-cg-demo-api-key: ' . $apiKey;
        }

        $json = self::httpGet($url, $headers, 5);
        if (!$json) return null;

        $data = json_decode($json, true);
        if (empty($data['bitcoin']['usd'])) return null;

        return [
            'rates' => [
                'BTC' => [
                    'price' => (float)$data['bitcoin']['usd'],
                    'change_24h' => round((float)($data['bitcoin']['usd_24h_change'] ?? 0), 2)
                ],
                'ETH' => [
                    'price' => (float)$data['ethereum']['usd'],
                    'change_24h' => round((float)($data['ethereum']['usd_24h_change'] ?? 0), 2)
                ],
                'SOL' => [
                    'price' => (float)$data['solana']['usd'],
                    'change_24h' => round((float)($data['solana']['usd_24h_change'] ?? 0), 2)
                ],
                'USDT' => [
                    'price' => (float)($data['tether']['usd'] ?? 1.0),
                    'change_24h' => round((float)($data['tether']['usd_24h_change'] ?? 0), 2)
                ]
            ]
        ];
    }

    private static function fetchFromBinance(): ?array {
        $url = 'https://api.binance.com/api/v3/ticker/24hr?symbols=%5B%22BTCUSDT%22,%22ETHUSDT%22,%22SOLUSDT%22%5D';
        $json = self::httpGet($url, ['Accept: application/json'], 5);
        if (!$json) return null;

        $data = json_decode($json, true);
        if (!is_array($data) || empty($data)) return null;

        $map = [];
        foreach ($data as $item) {
            if (!empty($item['symbol'])) {
                $map[$item['symbol']] = [
                    'price' => (float)($item['lastPrice'] ?? 0),
                    'change' => round((float)($item['priceChangePercent'] ?? 0), 2)
                ];
            }
        }

        if (empty($map['BTCUSDT']['price'])) return null;

        return [
            'rates' => [
                'BTC' => [
                    'price' => $map['BTCUSDT']['price'],
                    'change_24h' => $map['BTCUSDT']['change']
                ],
                'ETH' => [
                    'price' => $map['ETHUSDT']['price'] ?? 3450.0,
                    'change_24h' => $map['ETHUSDT']['change'] ?? 0
                ],
                'SOL' => [
                    'price' => $map['SOLUSDT']['price'] ?? 145.0,
                    'change_24h' => $map['SOLUSDT']['change'] ?? 0
                ],
                'USDT' => [
                    'price' => 1.0,
                    'change_24h' => 0.0
                ]
            ]
        ];
    }

    // ------------------------------------------------------------------------
    // CACHING & PERSISTENCE
    // ------------------------------------------------------------------------

    private static function getCachedRates(): ?array {
        try {
            $row = Database::fetch("SELECT key_value, updated_at FROM site_settings WHERE key_name = 'pricing_rates_cache'");
            if (!$row || empty($row['key_value'])) return null;

            $ttl = (int)(Database::fetch("SELECT key_value FROM site_settings WHERE key_name = 'pricing_cache_ttl'")['key_value'] ?? self::DEFAULT_TTL);
            if ($ttl < 10) $ttl = self::DEFAULT_TTL;

            $cacheTime = strtotime($row['updated_at'] ?? '2000-01-01');
            if ((time() - $cacheTime) > $ttl) {
                return null; // Cache expired
            }

            $data = json_decode($row['key_value'], true);
            if (!is_array($data) || empty($data['rates']['BTC']['price'])) {
                return null;
            }

            $data['cached'] = true;
            return $data;
        } catch (\Throwable $e) {
            return null;
        }
    }

    private static function saveCachedRates(array $rates): void {
        try {
            $encoded = json_encode($rates);
            Database::execute(
                "INSERT INTO site_settings (key_name, key_value, updated_at) 
                 VALUES ('pricing_rates_cache', ?, NOW()) 
                 ON DUPLICATE KEY UPDATE key_value = VALUES(key_value), updated_at = NOW()",
                [$encoded]
            );
        } catch (\Throwable $e) {}
    }

    private static function getFallbackRates(): array {
        // 1. Try to retrieve last known cached rates from database even if expired
        try {
            $row = Database::fetch("SELECT key_value, updated_at FROM site_settings WHERE key_name = 'pricing_rates_cache'");
            if ($row && !empty($row['key_value'])) {
                $cached = json_decode($row['key_value'], true);
                if (is_array($cached) && !empty($cached['rates']['BTC']['price'])) {
                    $cached['cached'] = true;
                    $cached['source'] = 'Latest Stored Market Cache';
                    return $cached;
                }
            }
        } catch (\Throwable $e) {}

        // 2. Baseline fallbacks with current realistic spot prices
        $btcFb = (float)(Database::fetch("SELECT key_value FROM site_settings WHERE key_name = 'pricing_fallback_btc'")['key_value'] ?? 84292.39);
        $ethFb = (float)(Database::fetch("SELECT key_value FROM site_settings WHERE key_name = 'pricing_fallback_eth'")['key_value'] ?? 2687.66);
        $solFb = (float)(Database::fetch("SELECT key_value FROM site_settings WHERE key_name = 'pricing_fallback_sol'")['key_value'] ?? 119.92);
        $usdtFb = (float)(Database::fetch("SELECT key_value FROM site_settings WHERE key_name = 'pricing_fallback_usdt'")['key_value'] ?? 1.0);

        return [
            'rates' => [
                'BTC' => ['price' => $btcFb, 'change_24h' => 1.39],
                'ETH' => ['price' => $ethFb, 'change_24h' => 0.43],
                'SOL' => ['price' => $solFb, 'change_24h' => 1.39],
                'USDT' => ['price' => $usdtFb, 'change_24h' => 0.0],
            ],
            'source' => 'Platform Baseline Fallback',
            'cached' => true,
            'updated_at' => date('Y-m-d H:i:s')
        ];
    }

    private static function getConfiguredProvider(): string {
        try {
            $row = Database::fetch("SELECT key_value FROM site_settings WHERE key_name = 'pricing_api_provider'");
            $p = strtolower(trim((string)($row['key_value'] ?? 'coingecko')));
            return in_array($p, ['coingecko', 'binance', 'static'], true) ? $p : 'coingecko';
        } catch (\Throwable $e) {
            return 'coingecko';
        }
    }

    private static function httpGet(string $url, array $headers = [], int $timeout = 5): ?string {
        if (function_exists('curl_init')) {
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);
            curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 3);
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            if (!empty($headers)) {
                curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
            }
            $response = curl_exec($ch);
            $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);

            if ($code >= 200 && $code < 300 && is_string($response) && strlen($response) > 0) {
                return $response;
            }
        }

        // Fallback to file_get_contents if stream allows
        $ctx = stream_context_create([
            'http' => [
                'method' => 'GET',
                'timeout' => $timeout,
                'header' => implode("\r\n", $headers)
            ],
            'ssl' => [
                'verify_peer' => false,
                'verify_peer_name' => false
            ]
        ]);

        $res = @file_get_contents($url, false, $ctx);
        return is_string($res) && strlen($res) > 0 ? $res : null;
    }
}
