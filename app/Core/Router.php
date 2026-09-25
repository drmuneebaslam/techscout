<?php
namespace App\Core;

class Router {
    private array $routes = [];

    public function get(string $path, $handler, array $middlewares = []): void {
        $this->addRoute('GET', $path, $handler, $middlewares);
    }

    public function post(string $path, $handler, array $middlewares = []): void {
        $this->addRoute('POST', $path, $handler, $middlewares);
    }

    public function put(string $path, $handler, array $middlewares = []): void {
        $this->addRoute('PUT', $path, $handler, $middlewares);
    }

    public function patch(string $path, $handler, array $middlewares = []): void {
        $this->addRoute('PATCH', $path, $handler, $middlewares);
    }

    public function delete(string $path, $handler, array $middlewares = []): void {
        $this->addRoute('DELETE', $path, $handler, $middlewares);
    }

    private function addRoute(string $method, string $path, $handler, array $middlewares): void {
        $this->routes[] = [
            'method' => $method,
            'path' => '/' . trim($path, '/'),
            'handler' => $handler,
            'middlewares' => $middlewares
        ];
    }

    public function dispatch(Request $request): void {
        $reqMethod = $request->getMethod();
        $reqPath = $request->getPath();

        // Support PUT/DELETE method overrides in HTML forms via _method
        if ($reqMethod === 'POST' && $request->post('_method')) {
            $reqMethod = strtoupper($request->post('_method'));
        }

        // Handle HEAD requests as GET
        if ($reqMethod === 'HEAD') {
            $reqMethod = 'GET';
        }

        foreach ($this->routes as $route) {
            if ($route['method'] !== $reqMethod) {
                continue;
            }

            $pattern = preg_replace('/\{([a-zA-Z0-9_]+)\}/', '(?P<$1>[^/]+)', $route['path']);
            $pattern = '#^' . ($pattern === '/' ? '/' : $pattern) . '$#';


            if (preg_match($pattern, $reqPath, $matches)) {
                $params = array_filter($matches, 'is_string', ARRAY_FILTER_USE_KEY);

                // Run Middlewares
                foreach ($route['middlewares'] as $mw) {
                    $this->handleMiddleware($mw);
                }

                // Execute Handler
                $handler = $route['handler'];
                if (is_callable($handler)) {
                    call_user_func($handler, $request, $params);
                    return;
                }

                if (is_array($handler) && count($handler) === 2) {
                    [$class, $method] = $handler;
                    $controller = new $class();
                    $controller->$method($request, $params);
                    return;
                }
            }
        }

        // 404 Route Not Found
        http_response_code(404);
        try {
            if (class_exists('\\App\\Core\\ErrorLogger')) {
                $uri = $request->uri();
                $method = $request->method();
                $ref = $_SERVER['HTTP_REFERER'] ?? 'direct';
                // Avoid logging static asset probes or browser noise
                if (!in_array($uri, ['/favicon.ico', '/robots.txt', '/apple-touch-icon.png', '/sitemap.xml'], true) && !str_starts_with($uri, '/assets/')) {
                    \App\Core\ErrorLogger::log(
                        'NOT_FOUND_404',
                        "404 Route Not Found: {$method} {$uri} (Referrer: {$ref})",
                        __FILE__,
                        __LINE__,
                        "Unmatched URL route requested by client."
                    );
                }
            }
        } catch (\Throwable $e) {}
        Response::view('errors/404', ['title' => 'Page Not Found | TechScout']);
    }

    private function handleMiddleware(string $mw): void {
        $isAjax = (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest')
            || (isset($_SERVER['HTTP_ACCEPT']) && str_contains($_SERVER['HTTP_ACCEPT'], 'application/json'))
            || (isset($_SERVER['REQUEST_URI']) && (str_contains($_SERVER['REQUEST_URI'], '/api/') || str_contains($_SERVER['REQUEST_URI'], '/save-mappings')));

        if ($mw === 'auth' && !Auth::check()) {
            if ($isAjax) {
                Response::json(['success' => false, 'code' => 401, 'error' => 'Please sign in to access this resource.'], 401);
                exit;
            }
            Session::flash('error', 'Please sign in to access this page.');
            Response::redirect('/auth/login');
            exit;
        }

        if ($mw === 'author') {
            if (!Auth::check()) {
                if ($isAjax) {
                    Response::json(['success' => false, 'code' => 401, 'error' => 'Please sign in to access the Creator Studio.'], 401);
                    exit;
                }
                Session::flash('error', 'Please sign in to access the Creator Studio.');
                Response::redirect('/auth/login');
                exit;
            }
            if (!Auth::isAuthor()) {
                if ($isAjax) {
                    Response::json(['success' => false, 'code' => 403, 'error' => 'Author privileges required.'], 403);
                    exit;
                }
                $status = Auth::authorApplicationStatus();
                if ($status === 'rejected') {
                    Session::flash('error', 'Your author application has revisions requested. Please review editorial feedback and resubmit.');
                } elseif ($status === 'pending') {
                    Session::flash('info', 'Your author application is currently under editorial review.');
                } else {
                    Session::flash('info', 'Author privileges required. Apply to become a verified author.');
                }
                Response::redirect('/author/apply');
                exit;
            }
        }

        if ($mw === 'admin' && !Auth::isAdmin()) {
            if ($isAjax) {
                Response::json(['success' => false, 'code' => 403, 'error' => 'Administrative access required.'], 403);
                exit;
            }
            Session::flash('error', 'Administrative access required.');
            Response::redirect('/auth/login');
            exit;
        }

        if ($mw === 'editor' && !Auth::isEditor()) {
            if ($isAjax) {
                Response::json(['success' => false, 'code' => 403, 'error' => 'Editorial access required.'], 403);
                exit;
            }
            Session::flash('error', 'Editorial access required.');
            Response::redirect('/auth/login');
            exit;
        }
    }
}
