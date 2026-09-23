<?php
namespace App\Core;

class Response {
    private static bool $isAborting = false;

    public static function json(array $data, int $statusCode = 200): void {
        if (!headers_sent()) {
            http_response_code($statusCode);
            header('Content-Type: application/json; charset=utf-8');
        }
        echo json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        exit;
    }

    public static function view(string $viewPath, array $data = []): void {
        extract($data);
        $fullPath = __DIR__ . '/../../views/' . ltrim($viewPath, '/') . '.php';
        if (file_exists($fullPath)) {
            require $fullPath;
        } else {
            if (!headers_sent()) {
                http_response_code(500);
            }
            echo "View not found: " . htmlspecialchars($viewPath);
        }
        exit;
    }

    public static function redirect(string $path, int $statusCode = 302): void {
        $url = str_starts_with($path, 'http') ? $path : Config::url($path);
        if (!headers_sent()) {
            http_response_code($statusCode);
            header("Location: {$url}");
        } else {
            echo "<script>window.location.href=" . json_encode($url) . ";</script>";
        }
        exit;
    }

    public static function abort(int $statusCode = 404, string $customMessage = '', array $extraData = []): void {
        if (!headers_sent()) {
            http_response_code($statusCode);
        }

        // Prevent infinite recursion loops if error templates trigger errors or firewall blocks
        if (self::$isAborting) {
            echo "<h1>HTTP Error {$statusCode}</h1><p>" . htmlspecialchars($customMessage) . "</p>";
            exit;
        }
        self::$isAborting = true;

        $viewPath = __DIR__ . '/../../views/errors/' . $statusCode . '.php';
        if (!file_exists($viewPath)) {
            $viewPath = __DIR__ . '/../../views/errors/404.php';
        }
        
        $data = array_merge([
            'statusCode' => $statusCode,
            'customMessage' => $customMessage,
            'title' => "HTTP {$statusCode} Error | TechScout"
        ], $extraData);

        extract($data);
        if (file_exists($viewPath)) {
            require $viewPath;
        } else {
            echo "<h1>HTTP Error {$statusCode}</h1><p>" . htmlspecialchars($customMessage) . "</p>";
        }
        exit;
    }
}
