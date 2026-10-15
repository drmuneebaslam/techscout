<?php
namespace App\Core;

class Request {
    private string $method;
    private string $uri;
    private string $path;
    private array $queryParams;
    private array $bodyParams;
    private ?array $jsonBody = null;

    public function __construct() {
        $this->method = strtoupper($_SERVER['REQUEST_METHOD'] ?? 'GET');
        $this->uri = $_SERVER['REQUEST_URI'] ?? '/';
        
        $parsed = parse_url($this->uri);
        $rawPath = $parsed['path'] ?? '/';

        // Normalize Windows backslashes
        $scriptPath = str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'] ?? ''));
        $scriptDir = trim($scriptPath, '/');
        
        if ($scriptDir !== '') {
            $prefix = '/' . $scriptDir;
            if (str_starts_with($rawPath, $prefix)) {
                $rawPath = substr($rawPath, strlen($prefix));
            }
        }

        $this->path = '/' . trim($rawPath, '/');
        if ($this->path === '') {
            $this->path = '/';
        }

        $this->queryParams = $_GET;
        $this->bodyParams = $_POST;

        $contentType = $_SERVER['CONTENT_TYPE'] ?? '';
        if (str_contains($contentType, 'application/json')) {
            $rawInput = file_get_contents('php://input');
            $this->jsonBody = json_decode($rawInput, true) ?? [];
        }
    }

    public function getMethod(): string {
        return $this->method;
    }

    public function method(): string {
        return $this->method;
    }

    public function getPath(): string {
        return $this->path;
    }

    public function getUri(): string {
        return $this->uri;
    }

    public function uri(): string {
        return $this->path;
    }

    public function query(string $key, $default = null) {
        return $this->queryParams[$key] ?? $default;
    }

    public function post(string $key, $default = null) {
        return $this->bodyParams[$key] ?? $default;
    }

    public function get(string $key, $default = null) {
        return $this->queryParams[$key] ?? $this->bodyParams[$key] ?? ($this->jsonBody[$key] ?? $default);
    }

    public function json(?string $key = null, $default = null) {
        if ($this->jsonBody === null) {
            return $default;
        }
        if ($key === null) {
            return $this->jsonBody;
        }
        return $this->jsonBody[$key] ?? $default;
    }

    public function input(string $key, $default = null) {
        if ($this->jsonBody !== null && isset($this->jsonBody[$key])) {
            return $this->jsonBody[$key];
        }
        return $this->bodyParams[$key] ?? $this->queryParams[$key] ?? $default;
    }

    public function all(): array {
        return array_merge($this->queryParams, $this->bodyParams, $this->jsonBody ?? []);
    }

    public static function ip(): string {
        if (!empty($_SERVER['HTTP_CF_CONNECTING_IP'])) {
            return trim($_SERVER['HTTP_CF_CONNECTING_IP']);
        }
        if (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $parts = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']);
            return trim($parts[0]);
        }
        return $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1';
    }

    public function header(string $key, $default = null): ?string {
        $normalized = 'HTTP_' . strtoupper(str_replace('-', '_', $key));
        if (isset($_SERVER[$normalized])) {
            return (string)$_SERVER[$normalized];
        }
        $direct = strtoupper(str_replace('-', '_', $key));
        if (isset($_SERVER[$direct])) {
            return (string)$_SERVER[$direct];
        }
        return $default;
    }

    public function referrer(?string $default = null): ?string {
        return $_SERVER['HTTP_REFERER'] ?? $default;
    }

    public function referer(?string $default = null): ?string {
        return $this->referrer($default);
    }

    public function isAjax(): bool {
        $xrw = strtolower((string)($_SERVER['HTTP_X_REQUESTED_WITH'] ?? ''));
        $accept = (string)($_SERVER['HTTP_ACCEPT'] ?? '');
        return $xrw === 'xmlhttprequest' || str_contains($accept, 'application/json');
    }

    public function isPost(): bool {
        return $this->method === 'POST';
    }

    public function isGet(): bool {
        return $this->method === 'GET';
    }

    public function file(string $key) {
        return $_FILES[$key] ?? null;
    }

    public function files(): array {
        return $_FILES ?? [];
    }
}

