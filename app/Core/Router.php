<?php

namespace App\Core;

use App\Middleware\PermissionMiddleware;
use App\Middleware\FeatureMiddleware;
use Throwable;

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

            $this->runMiddleware($route['middleware'] ?? []);
            $this->runHandler($route['handler']);
            return;
        }

        http_response_code(404);
        echo '404 Not Found';
    }

    /**
     * @param array<string|class-string> $middlewareDefinitions
     */
    private function runMiddleware(array $middlewareDefinitions): void
    {
        foreach ($middlewareDefinitions as $middlewareClass) {
            if (!is_string($middlewareClass)) {
                continue;
            }

            if (str_starts_with($middlewareClass, 'permission:')) {
                $this->ensureClassAvailable(PermissionMiddleware::class);
                $permissionKey = substr($middlewareClass, strlen('permission:'));
                $middleware = new PermissionMiddleware();
                if (method_exists($middleware, 'handle')) {
                    $middleware->handle($permissionKey);
                }
                continue;
            }

            if (str_starts_with($middlewareClass, 'feature:')) {
                $this->ensureClassAvailable(FeatureMiddleware::class);
                $featureKey = substr($middlewareClass, strlen('feature:'));
                $middleware = new FeatureMiddleware();
                if (method_exists($middleware, 'handle')) {
                    $middleware->handle($featureKey);
                }
                continue;
            }

            $this->ensureClassAvailable($middlewareClass);

            $middleware = new $middlewareClass();
            if (method_exists($middleware, 'handle')) {
                $middleware->handle();
            }
        }
    }

    /**
     * @param array{0: class-string, 1: string} $handler
     */
    private function runHandler(array $handler): void
    {
        [$controllerClass, $action] = $handler;

        $this->ensureClassAvailable($controllerClass);

        if (!method_exists($controllerClass, $action)) {
            $this->abortWithError(500, sprintf('Controller action not found: %s::%s', $controllerClass, $action));
        }

        $controller = new $controllerClass();
        $controller->$action();
    }

    private function ensureClassAvailable(string $className): void
    {
        if (class_exists($className)) {
            return;
        }

        $this->abortWithError(500, sprintf('Class not found: %s', $className));
    }

    private function abortWithError(int $statusCode, string $message): void
    {
        try {
            http_response_code($statusCode);
        } catch (Throwable $e) {
            // noop
        }

        echo $message;
        exit;
    }
}
