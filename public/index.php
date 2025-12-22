<?php
// Basic front controller and bootstrap file

declare(strict_types=1);

// 1) error reporting
error_reporting(E_ALL);
ini_set('display_errors', '1');

// 2) autoload
spl_autoload_register(function ($class) {
    $baseDir = dirname(__DIR__) . '/';
    $file = $baseDir . str_replace('\\', '/', $class) . '.php';

    if (file_exists($file)) {
        require_once $file;
        return;
    }

    if (str_starts_with($class, 'App\\')) {
        $relative = substr($class, strlen('App\\'));
        $appFile = $baseDir . 'app/' . str_replace('\\', '/', $relative) . '.php';
        if (file_exists($appFile)) {
            require_once $appFile;
        }
    }
});

// shared helpers
require_once dirname(__DIR__) . '/app/helpers.php';

// 3) config yükleme
loadEnv(dirname(__DIR__) . '/.env');
$config = require dirname(__DIR__) . '/config/app.php';
ini_set('display_errors', ($config['debug'] ?? true) ? '1' : '0');

// 4) core class'ları yükleme
use App\Core\Auth;
use App\Core\Router;

Auth::startSession();

// 5) Auth nesnesi oluşturma
$auth = new Auth();

// 6) Router başlatma ve dispatch
$routes = require dirname(__DIR__) . '/config/routes.php';
$router = new Router($routes);
$router->dispatch();
