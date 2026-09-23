<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Core\Request;
use App\Core\Response;
use App\Core\IncomingWebhookEngine;
use App\Core\Auth;
use App\Core\Database;

class IncomingWebhookController {

    /**
     * Public Webhook Receiver Endpoint: POST /api/v1/webhook/{endpoint_slug}
     */
    public function handle(Request $request, array $params): void {
        header('Content-Type: application/json; charset=utf-8');

        $slug = trim((string)($params['endpoint_slug'] ?? ''));
        $rawBody = file_get_contents('php://input') ?: '';
        $headers = function_exists('getallheaders') ? getallheaders() : [];
        $ip = Request::ip();

        $result = IncomingWebhookEngine::processPayload($slug, $headers, $rawBody, $ip);

        http_response_code($result['code'] ?? 200);
        echo json_encode($result, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
        exit;
    }

    /**
     * Admin Test Simulation: POST /api/v1/webhook/simulate
     */
    public function simulate(Request $request): void {
        if (!Auth::check() || (Auth::user()['role'] ?? '') !== 'admin') {
            Response::json(['error' => 'Admin authorization required'], 403);
            return;
        }

        $slug = trim((string)$request->post('slug', ''));
        $rawBody = trim((string)$request->post('payload', '{}'));
        $authHeader = trim((string)$request->post('auth_header', ''));

        $headers = [];
        if (!empty($authHeader)) {
            $headers['Authorization'] = $authHeader;
        }

        $result = IncomingWebhookEngine::processPayload($slug, $headers, $rawBody, Request::ip());
        Response::json($result, $result['code'] ?? 200);
    }

    /**
     * Live Poller for Admin JSON Listener: GET /api/v1/webhook/live-stream/{slug}
     */
    public function liveStream(Request $request, array $params): void {
        if (!Auth::check() || (Auth::user()['role'] ?? '') !== 'admin') {
            Response::json(['error' => 'Admin authorization required'], 403);
            return;
        }

        $slug = trim((string)($params['slug'] ?? ''));
        $sinceId = (int)$request->query('since_id', 0);

        try {
            $logs = Database::query(
                "SELECT * FROM incoming_webhook_logs 
                 WHERE endpoint_slug = ? AND id > ? 
                 ORDER BY id DESC LIMIT 20",
                [$slug, $sinceId]
            );

            Response::json([
                'success' => true,
                'slug' => $slug,
                'count' => count($logs),
                'logs' => $logs
            ]);
        } catch (\Throwable $e) {
            Response::json(['error' => $e->getMessage()], 500);
        }
    }
}
