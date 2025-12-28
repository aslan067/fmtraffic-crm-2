<?php

declare(strict_types=1);

use App\Controllers\AuthController;
use App\Controllers\DashboardController;
use App\Controllers\OfferController;
use App\Controllers\CariController;
use App\Controllers\ProductController;
use App\Controllers\SuperAdminController;
use App\Controllers\UsersController;
use App\Middleware\AuthMiddleware;
use App\Middleware\SuperAdminMiddleware;
use App\Middleware\FeatureMiddleware;

return [
    ['method' => 'GET', 'path' => '/login', 'handler' => [AuthController::class, 'showLogin'], 'middleware' => []],
    ['method' => 'POST', 'path' => '/login', 'handler' => [AuthController::class, 'login'], 'middleware' => []],
    ['method' => 'POST', 'path' => '/logout', 'handler' => [AuthController::class, 'logout'], 'middleware' => [AuthMiddleware::class]],
    ['method' => 'GET', 'path' => '/dashboard', 'handler' => [DashboardController::class, 'index'], 'middleware' => [AuthMiddleware::class]],
    ['method' => 'GET', 'path' => '/products', 'handler' => [ProductController::class, 'index'], 'middleware' => [AuthMiddleware::class, 'module:products']],
    ['method' => 'GET', 'path' => '/products/create', 'handler' => [ProductController::class, 'create'], 'middleware' => [AuthMiddleware::class, 'module:products']],
    ['method' => 'POST', 'path' => '/products/store', 'handler' => [ProductController::class, 'store'], 'middleware' => [AuthMiddleware::class, 'module:products']],
    ['method' => 'GET', 'path' => '/products/{id}/edit', 'handler' => [ProductController::class, 'edit'], 'middleware' => [AuthMiddleware::class, 'module:products']],
    ['method' => 'POST', 'path' => '/products/{id}/update', 'handler' => [ProductController::class, 'update'], 'middleware' => [AuthMiddleware::class, 'module:products']],
    ['method' => 'POST', 'path' => '/products/{id}/deactivate', 'handler' => [ProductController::class, 'deactivate'], 'middleware' => [AuthMiddleware::class, 'module:products']],
    ['method' => 'GET', 'path' => '/caris', 'handler' => [CariController::class, 'index'], 'middleware' => [AuthMiddleware::class, 'module:caris']],
    ['method' => 'GET', 'path' => '/caris/create', 'handler' => [CariController::class, 'create'], 'middleware' => [AuthMiddleware::class, 'module:caris']],
    ['method' => 'POST', 'path' => '/caris/store', 'handler' => [CariController::class, 'store'], 'middleware' => [AuthMiddleware::class, 'module:caris']],
    ['method' => 'GET', 'path' => '/caris/{id}/edit', 'handler' => [CariController::class, 'edit'], 'middleware' => [AuthMiddleware::class, 'module:caris']],
    ['method' => 'POST', 'path' => '/caris/{id}/update', 'handler' => [CariController::class, 'update'], 'middleware' => [AuthMiddleware::class, 'module:caris']],
    ['method' => 'POST', 'path' => '/caris/{id}/deactivate', 'handler' => [CariController::class, 'deactivate'], 'middleware' => [AuthMiddleware::class, 'module:caris']],
    ['method' => 'GET', 'path' => '/offers', 'handler' => [OfferController::class, 'index'], 'middleware' => [AuthMiddleware::class, 'module:offers']],
    ['method' => 'GET', 'path' => '/offers/create', 'handler' => [OfferController::class, 'create'], 'middleware' => [AuthMiddleware::class, 'module:offers']],
    ['method' => 'POST', 'path' => '/offers', 'handler' => [OfferController::class, 'store'], 'middleware' => [AuthMiddleware::class, 'module:offers']],
    ['method' => 'GET', 'path' => '/offers/{id}', 'handler' => [OfferController::class, 'show'], 'middleware' => [AuthMiddleware::class, 'module:offers']],
    ['method' => 'POST', 'path' => '/offers/{id}/status', 'handler' => [OfferController::class, 'updateStatus'], 'middleware' => [AuthMiddleware::class, 'module:offers']],
    ['method' => 'GET', 'path' => '/users/create', 'handler' => [UsersController::class, 'create'], 'middleware' => [AuthMiddleware::class]],
    ['method' => 'POST', 'path' => '/users', 'handler' => [UsersController::class, 'store'], 'middleware' => [AuthMiddleware::class]],
    ['method' => 'GET', 'path' => '/super-admin/companies', 'handler' => [SuperAdminController::class, 'listCompanies'], 'middleware' => [AuthMiddleware::class, SuperAdminMiddleware::class]],
    ['method' => 'POST', 'path' => '/super-admin/companies', 'handler' => [SuperAdminController::class, 'createCompany'], 'middleware' => [AuthMiddleware::class, SuperAdminMiddleware::class]],
    ['method' => 'POST', 'path' => '/super-admin/subscriptions', 'handler' => [SuperAdminController::class, 'assignPackage'], 'middleware' => [AuthMiddleware::class, SuperAdminMiddleware::class]],
    ['method' => 'GET', 'path' => '/', 'handler' => [AuthController::class, 'showLogin'], 'middleware' => []],
];
