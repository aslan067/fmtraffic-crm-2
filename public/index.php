<?php
// Basic front controller and bootstrap file

declare(strict_types=1);

session_start();

// Simple autoloader for App namespace
spl_autoload_register(function ($class) {
    $prefix = 'App\\';
    $baseDir = __DIR__ . '/../app/';

    if (strncmp($prefix, $class, strlen($prefix)) !== 0) {
        return;
    }

    $relativeClass = substr($class, strlen($prefix));
    $file = $baseDir . str_replace('\\', '/', $relativeClass) . '.php';

    if (file_exists($file)) {
        require $file;
    }
});

require_once __DIR__ . '/../config/app.php';
require_once __DIR__ . '/../config/routes.php';

use App\Core\Router;

$router = new Router($routes);
$router->dispatch();
