<?php
// Basic front controller and bootstrap file

declare(strict_types=1);

// 1) error reporting
error_reporting(E_ALL);
ini_set('display_errors', '1');

// 2) BASE_PATH tanımı
define('BASE_PATH', realpath(__DIR__));

// 3) autoload
spl_autoload_register(function ($class) {
    $file = BASE_PATH . '/' . str_replace('\\', '/', $class) . '.php';
    if (file_exists($file)) {
        require_once $file;
        return;
    }

    $normalizedClass = str_replace('\\', '/', $class);
    $appFile = BASE_PATH . '/' . preg_replace('#^App/#', 'app/', $normalizedClass) . '.php';
    if ($appFile && file_exists($appFile)) {
        require_once $appFile;
    }
});

// shared helpers
require_once BASE_PATH . '/app/helpers.php';

// 4) config yükleme
loadEnv(BASE_PATH . '/.env');
$config = require BASE_PATH . '/config/app.php';
ini_set('display_errors', ($config['debug'] ?? true) ? '1' : '0');

// 5) core class'ları yükleme ve Auth nesnesi oluşturma
use App\Core\Auth;
use App\Core\Router;

Auth::startSession();

$auth = new Auth();

// 6) Router başlatma ve dispatch
$routes = require BASE_PATH . '/config/routes.php';
$router = new Router($routes);
$router->dispatch();
