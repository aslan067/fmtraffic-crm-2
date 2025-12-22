<?php

declare(strict_types=1);

use App\Controllers\AuthController;
use App\Controllers\DashboardController;
use App\Controllers\ProductsController;
use App\Middleware\AuthMiddleware;

$routes = [
    ['method' => 'GET', 'path' => '/login', 'handler' => [AuthController::class, 'showLogin'], 'middleware' => []],
    ['method' => 'POST', 'path' => '/login', 'handler' => [AuthController::class, 'login'], 'middleware' => []],
    ['method' => 'POST', 'path' => '/logout', 'handler' => [AuthController::class, 'logout'], 'middleware' => [AuthMiddleware::class]],
    ['method' => 'GET', 'path' => '/dashboard', 'handler' => [DashboardController::class, 'index'], 'middleware' => [AuthMiddleware::class]],
    ['method' => 'GET', 'path' => '/products', 'handler' => [ProductsController::class, 'index'], 'middleware' => [AuthMiddleware::class, 'permission:product.view']],
    ['method' => 'GET', 'path' => '/', 'handler' => [AuthController::class, 'showLogin'], 'middleware' => []],
];
