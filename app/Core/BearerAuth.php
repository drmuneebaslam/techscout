<?php
namespace App\Core;

use App\Core\Database;
use App\Core\Request;
use App\Core\Response;

class BearerAuth
{
    /**
     * Authenticate an incoming REST API request using Bearer Token
     *
     * @param Request|null $request
     * @param string|null $requiredScope
     * @return array|null Returns the authenticated api_key record or null
     */
    public static function authenticate(?Request $request = null, ?string $requiredScope = null): ?array
    {
        $token = self::extractToken($request);
        if (!$token) {
            return null;
        }

        try {
            $db = Database::getConnection();
            $hash = hash('sha256', $token);

            $stmt = $db->prepare("
                SELECT * FROM api_keys 
                WHERE api_key_hash = :hash 
                  AND is_active = 1 
                  AND (expires_at IS NULL OR expires_at > NOW())
                LIMIT 1
            ");
            $stmt->execute([':hash' => $hash]);
            $apiKey = $stmt->fetch(\PDO::FETCH_ASSOC);

            if (!$apiKey) {
                return null;
            }

            // Check Scope if required
            if ($requiredScope !== null) {
                $scopes = json_decode($apiKey['scopes_json'] ?? '[]', true);
                if (!is_array($scopes) || (!in_array('*', $scopes) && !in_array($requiredScope, $scopes))) {
                    return ['_error' => 'forbidden', '_message' => "Insufficient scope: requires '{$requiredScope}'"];
                }
            }

            // Rate Limit Check (requests per minute window using session or DB counter)
            // Update usage telemetry
            $update = $db->prepare("
                UPDATE api_keys 
                SET requests_count = requests_count + 1, last_used_at = NOW() 
                WHERE id = :id
            ");
            $update->execute([':id' => (int)$apiKey['id']]);

            return $apiKey;
        } catch (\Throwable $e) {
            error_log('BearerAuth error: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Enforce authentication and terminate with JSON error if unauthorized
     *
     * @param Request|null $request
     * @param string|null $requiredScope
     * @return array
     */
    public static function requireAuth(?Request $request = null, ?string $requiredScope = null): array
    {
        $auth = self::authenticate($request, $requiredScope);

        if ($auth === null) {
            header('Content-Type: application/json; charset=utf-8');
            http_response_code(401);
            echo json_encode([
                'success' => false,
                'status'  => 401,
                'error'   => 'Unauthorized',
                'message' => 'Missing, invalid, or expired Bearer token. Use Authorization: Bearer ts_live_...'
            ], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
            exit;
        }

        if (isset($auth['_error']) && $auth['_error'] === 'forbidden') {
            header('Content-Type: application/json; charset=utf-8');
            http_response_code(403);
            echo json_encode([
                'success' => false,
                'status'  => 403,
                'error'   => 'Forbidden',
                'message' => $auth['_message'] ?? 'Access denied'
            ], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
            exit;
        }

        return $auth;
    }

    /**
     * Extract bearer token from Authorization header or fallbacks
     */
    public static function extractToken(?Request $request = null): ?string
    {
        $headers = function_exists('getallheaders') ? getallheaders() : [];
        $authHeader = '';

        foreach ($headers as $k => $v) {
            if (strtolower($k) === 'authorization') {
                $authHeader = trim((string)$v);
                break;
            }
        }

        if (empty($authHeader) && isset($_SERVER['HTTP_AUTHORIZATION'])) {
            $authHeader = trim((string)$_SERVER['HTTP_AUTHORIZATION']);
        }
        if (empty($authHeader) && isset($_SERVER['REDIRECT_HTTP_AUTHORIZATION'])) {
            $authHeader = trim((string)$_SERVER['REDIRECT_HTTP_AUTHORIZATION']);
        }

        if (!empty($authHeader) && preg_match('/Bearer\s+(\S+)/i', $authHeader, $matches)) {
            return $matches[1];
        }

        // Fallback to X-API-Key header
        foreach ($headers as $k => $v) {
            if (strtolower($k) === 'x-api-key') {
                return trim((string)$v);
            }
        }
        if (isset($_SERVER['HTTP_X_API_KEY'])) {
            return trim((string)$_SERVER['HTTP_X_API_KEY']);
        }

        // Fallback to query parameter (e.g. for testing / webhook listeners)
        if (isset($_GET['api_key'])) {
            return trim((string)$_GET['api_key']);
        }

        return null;
    }
}
