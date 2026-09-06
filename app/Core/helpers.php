<?php
use App\Core\Config;
use App\Core\Auth;
use App\Core\Session;

if (!function_exists('url')) {
    function url(string $path = ''): string {
        $path = trim($path);
        if ($path === '' || $path === '#') {
            return $path;
        }
        if (str_starts_with($path, 'http://') || str_starts_with($path, 'https://') || str_starts_with($path, 'data:') || str_starts_with($path, 'mailto:') || str_starts_with($path, 'javascript:') || str_starts_with($path, 'tel:')) {
            return $path;
        }

        $base = rtrim(Config::get('app_url', 'http://localhost/techscout.n'), '/');
        
        // Strip duplicate subfolder prefix if already present in path
        $parsed = parse_url($base);
        $basePath = trim($parsed['path'] ?? '', '/');
        if ($basePath !== '') {
            $prefix = '/' . $basePath;
            if (str_starts_with($path, $prefix . '/')) {
                $path = substr($path, strlen($prefix));
            } elseif ($path === $prefix) {
                $path = '';
            }
        }

        $cleanPath = ltrim($path, '/');
        return $cleanPath ? "{$base}/{$cleanPath}" : $base . '/';
    }
}

if (!function_exists('image_url')) {
    /**
     * Universal Image URL Resolver: Resolves avatars, covers, uploads, and media
     * ensuring they strictly use the Admin CMS configured base URL.
     */
    function image_url(?string $path, string $default = ''): string {
        $path = trim((string)$path);
        if ($path === '') {
            return $default;
        }
        if (str_starts_with($path, 'http://') || str_starts_with($path, 'https://') || str_starts_with($path, 'data:')) {
            return $path;
        }
        return url($path);
    }
}

if (!function_exists('csrf_token')) {
    function csrf_token(): string {
        return Session::csrfToken();
    }
}

if (!function_exists('csrf_field')) {
    function csrf_field(): string {
        $token = csrf_token();
        return '<input type="hidden" name="_csrf_token" value="' . htmlspecialchars($token) . '">';
    }
}

if (!function_exists('method_field')) {
    function method_field(string $method): string {
        return '<input type="hidden" name="_method" value="' . strtoupper($method) . '">';
    }
}

if (!function_exists('auth_user')) {
    function auth_user(): ?array {
        return Auth::user();
    }
}

if (!function_exists('is_admin')) {
    function is_admin(): bool {
        return Auth::isAdmin();
    }
}

if (!function_exists('is_author')) {
    function is_author(): bool {
        return Auth::isAuthor();
    }
}

if (!function_exists('content_url')) {
    function content_url($typeOrItem, ?string $slug = null): string {
        $type = 'article';
        $itemSlug = '';

        if (is_array($typeOrItem)) {
            $type = $typeOrItem['content_type'] ?? 'article';
            $itemSlug = $typeOrItem['slug'] ?? '';
        } else {
            $type = (string)$typeOrItem;
            $itemSlug = (string)($slug ?? '');
        }

        switch ($type) {
            case 'ai_tool':
                $prefix = '/tools/';
                break;
            case 'gpt_listing':
            case 'gpt':
                $prefix = '/gpts/';
                break;
            case 'prompt':
                $prefix = '/prompts/';
                break;
            case 'campaign':
                $prefix = '/campaigns/';
                break;
            case 'article':
            default:
                $prefix = '/article/';
                break;
        }

        return url($prefix . $itemSlug);
    }
}

