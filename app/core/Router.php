<?php
/**
 * Router.php — Simple MVC-lite router.
 *
 * Normalizes REQUEST_URI for both URL styles:
 * - http://localhost/YourFolder/          (rewrite or root index.php)
 * - http://localhost/YourFolder/public/   (direct public access)
 */

declare(strict_types=1);

class Router
{
    /** @var array<int, array{method: string, pattern: string, handler: string}> */
    private array $routes = [];

    public function get(string $path, string $handler): void
    {
        $this->add('GET', $path, $handler);
    }

    public function post(string $path, string $handler): void
    {
        $this->add('POST', $path, $handler);
    }

    private function add(string $method, string $path, string $handler): void
    {
        $this->routes[] = [
            'method' => strtoupper($method),
            'pattern' => $path,
            'handler' => $handler,
        ];
    }

    public function dispatch(): void
    {
        $method = $_SERVER['REQUEST_METHOD'] ?? 'GET';
        $uri = $this->normalizeUri();

        foreach ($this->routes as $route) {
            if ($route['method'] !== $method) {
                continue;
            }

            $params = $this->match($route['pattern'], $uri);
            if ($params === null) {
                continue;
            }

            $this->invoke($route['handler'], $params);
            return;
        }

        http_response_code(404);
        view('errors/404');
    }

    /**
     * Strip project base and optional /public from REQUEST_URI for route matching.
     */
    private function normalizeUri(): string
    {
        $uri = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/';

        $webRoot = defined('WEB_ROOT') ? WEB_ROOT : '';
        if ($webRoot !== '' && str_starts_with($uri, $webRoot)) {
            $uri = substr($uri, strlen($webRoot)) ?: '/';
        }

        if (str_starts_with($uri, '/public')) {
            $uri = substr($uri, 7) ?: '/';
        }

        if ($uri === '' || $uri === false) {
            $uri = '/';
        }
        if (!str_starts_with($uri, '/')) {
            $uri = '/' . $uri;
        }

        $uri = rtrim($uri, '/');
        return $uri === '' ? '/' : $uri;
    }

    private function match(string $pattern, string $uri): ?array
    {
        $regex = preg_replace('/\{([a-zA-Z_]+)\}/', '(?P<$1>[^/]+)', $pattern);
        $regex = '#^' . $regex . '$#';

        if (!preg_match($regex, $uri, $matches)) {
            return null;
        }

        $params = [];
        foreach ($matches as $key => $value) {
            if (is_string($key)) {
                $params[$key] = $value;
            }
        }
        return $params;
    }

    private function invoke(string $handler, array $params): void
    {
        [$class, $action] = explode('@', $handler, 2);
        $file = APP_PATH . '/controllers/' . $class . '.php';

        if (!is_readable($file)) {
            http_response_code(500);
            view('errors/500');
            return;
        }

        require_once $file;

        if (!class_exists($class)) {
            http_response_code(500);
            view('errors/500');
            return;
        }

        $controller = new $class();

        if (!method_exists($controller, $action)) {
            http_response_code(404);
            view('errors/404');
            return;
        }

        $controller->{$action}(...array_values($params));
    }
}
