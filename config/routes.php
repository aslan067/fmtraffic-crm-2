<?php

declare(strict_types=1);

use App\Controllers\AuthController;
use App\Controllers\DashboardController;
use App\Controllers\ProductsController;
use App\Controllers\SuperAdminController;
use App\Controllers\UsersController;
use App\Middleware\AuthMiddleware;
use App\Middleware\SuperAdminMiddleware;
use App\Middleware\FeatureMiddleware;

$routes = [
    ['method' => 'GET', 'path' => '/login', 'handler' => [AuthController::class, 'showLogin'], 'middleware' => []],
    ['method' => 'POST', 'path' => '/login', 'handler' => [AuthController::class, 'login'], 'middleware' => []],
    ['method' => 'POST', 'path' => '/logout', 'handler' => [AuthController::class, 'logout'], 'middleware' => [AuthMiddleware::class]],
    ['method' => 'GET', 'path' => '/dashboard', 'handler' => [DashboardController::class, 'index'], 'middleware' => [AuthMiddleware::class]],
    ['method' => 'GET', 'path' => '/products', 'handler' => [ProductsController::class, 'index'], 'middleware' => [AuthMiddleware::class, 'permission:product.view', 'feature:product']],
    ['method' => 'GET', 'path' => '/users/create', 'handler' => [UsersController::class, 'create'], 'middleware' => [AuthMiddleware::class]],
    ['method' => 'POST', 'path' => '/users', 'handler' => [UsersController::class, 'store'], 'middleware' => [AuthMiddleware::class]],
    ['method' => 'GET', 'path' => '/super-admin/companies', 'handler' => [SuperAdminController::class, 'listCompanies'], 'middleware' => [AuthMiddleware::class, SuperAdminMiddleware::class]],
    ['method' => 'POST', 'path' => '/super-admin/companies', 'handler' => [SuperAdminController::class, 'createCompany'], 'middleware' => [AuthMiddleware::class, SuperAdminMiddleware::class]],
    ['method' => 'POST', 'path' => '/super-admin/subscriptions', 'handler' => [SuperAdminController::class, 'assignPackage'], 'middleware' => [AuthMiddleware::class, SuperAdminMiddleware::class]],
    ['method' => 'GET', 'path' => '/', 'handler' => [AuthController::class, 'showLogin'], 'middleware' => []],
];
