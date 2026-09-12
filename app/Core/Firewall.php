<?php
namespace App\Core;

use Throwable;

class Firewall {
    public static function check(): void {
        if (!FeatureFlag::isEnabled('enable_security_firewall', true)) return;

        $ip = $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1';
        $ua = $_SERVER['HTTP_USER_AGENT'] ?? '';
        $path = $_SERVER['REQUEST_URI'] ?? '/';

        try {
            // Check IP or CIDR block
            $blockedRule = Database::fetch(
                "SELECT * FROM firewall_rules WHERE is_active = 1 AND rule_type = 'ip' AND target_value = ?",
                [$ip]
            );

            if ($blockedRule) {
                self::blockRequest($blockedRule['id'], $ip, $ua, $path, 'Blocked IP Address');
            }

            // Check Malicious User Agent scan
            if (!empty($ua)) {
                $badUARule = Database::fetch(
                    "SELECT * FROM firewall_rules WHERE is_active = 1 AND rule_type = 'user_agent' AND ? LIKE CONCAT('%', target_value, '%')",
                    [$ua]
                );
                if ($badUARule) {
                    self::blockRequest($badUARule['id'], $ip, $ua, $path, 'Malicious User Agent Signature');
                }
            }

            // Known Automated Exploit Scanning Detection
            $suspiciousPatterns = ['/etc/passwd', '..%2f', 'eval-stdin.php', 'wp-login.php', '.env', '/vendor/phpunit', '/.git/config'];
            foreach ($suspiciousPatterns as $pattern) {
                if (stripos($path, $pattern) !== false) {
                    self::logThreat($ip, $ua, $path, 'Automated Exploit Scanner', 'blocked');
                    Response::abort(403, 'Automated exploit scanner blocked by TechScout Security Perimeter.');
                }
            }
        } catch (Throwable $e) {}
    }

    private static function blockRequest(int $ruleId, string $ip, string $ua, string $path, string $threatType): void {
        try {
            Database::execute("UPDATE firewall_rules SET hits_count = hits_count + 1 WHERE id = ?", [$ruleId]);
            self::logThreat($ip, $ua, $path, $threatType, 'blocked');
        } catch (Throwable $e) {}

        Response::abort(403, 'Access denied by TechScout Enterprise Firewall Rule.');
    }

    public static function logThreat(string $ip, string $ua, string $path, string $threatType, string $action = 'blocked'): void {
        try {
            Database::execute(
                "INSERT INTO firewall_logs (ip_address, user_agent, path, threat_type, action_taken, created_at) VALUES (?, ?, ?, ?, ?, NOW())",
                [$ip, mb_strimwidth($ua, 0, 250, '...'), mb_strimwidth($path, 0, 250, '...'), $threatType, $action]
            );
        } catch (Throwable $e) {}
    }

    /**
     * Check if an IP matches a CIDR range or single IP [TS-INF-09]
     */
    public static function ipInRange(string $ip, string $range): bool {
        if (!str_contains($range, '/')) {
            return $ip === $range;
        }
        [$subnet, $bits] = explode('/', $range, 2);
        if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4) && filter_var($subnet, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4)) {
            $ipLong = ip2long($ip);
            $subnetLong = ip2long($subnet);
            $mask = -1 << (32 - (int)$bits);
            $subnetLong &= $mask;
            return ($ipLong & $mask) == $subnetLong;
        }
        return false;
    }

    /**
     * Validate against disposable/temporary burner email providers [TS-SEC-05]
     */
    public static function isDisposableEmail(string $email): bool {
        $parts = explode('@', strtolower(trim($email)));
        if (count($parts) !== 2) return true;
        $domain = $parts[1];

        $disposableDomains = [
            'mailinator.com', '10minutemail.com', 'tempmail.com', 'guerrillamail.com',
            'sharklasers.com', 'yopmail.com', 'throwawaymail.com', 'dispostable.com',
            'getairmail.com', 'fakemailgenerator.com', 'temp-mail.org', 'generator.email',
            'trashmail.com', 'maildrop.cc', 'getnada.com', 'inboxkitten.com', 'burnermail.io'
        ];

        return in_array($domain, $disposableDomains, true);
    }
}
