<?php

namespace App\Core;

class Router
{
    private array $routes = [];

    public function __construct(array $routes)
    {
        $this->routes = $routes;
    }

    public function dispatch(): void
    {
        $method = $_SERVER['REQUEST_METHOD'] ?? 'GET';
        $uri = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/';

        foreach ($this->routes as $route) {
            if ($route['method'] !== $method || $route['path'] !== $uri) {
                continue;
            }

            if (!empty($route['middleware'])) {
                foreach ($route['middleware'] as $middlewareClass) {
                    if (is_array($middlewareClass)) {
                        [$class, $param] = $middlewareClass;
                        $middleware = new $class($param);
                    } else {
                        $middleware = new $middlewareClass();
                    }
                    if (method_exists($middleware, 'handle')) {
                        $middleware->handle();
                    }
                }
            }

            [$controllerClass, $action] = $route['handler'];
            $controller = new $controllerClass();
            $controller->$action();
            return;
        }

        http_response_code(404);
        echo '404 Not Found';
    }
}
