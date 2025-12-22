<?php

namespace App\Middleware;

use App\Core\Auth;

class PermissionMiddleware
{
    public function handle(string $permissionKey): void
    {
        if (!Auth::check()) {
            redirect('/login');
        }

        if ($permissionKey === '') {
            http_response_code(400);
            echo 'Yetki anahtarı bulunamadı.';
            exit;
        }

        if (!Auth::hasPermission($permissionKey)) {
            http_response_code(403);
            echo 'Bu işlemi yapmak için yetkiniz yok.';
            exit;
        }
    }
}
