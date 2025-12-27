<?php

namespace App\Middleware;

use App\Core\Auth;

class PermissionMiddleware
{
    public function handle(string $permissionKey): void
    {
        if (!Auth::check()) {
            $this->denyAccess();
        }

        if (Auth::isSuperAdmin()) {
            return;
        }

        if ($permissionKey === '') {
            $this->denyAccess();
        }

        if (!Auth::hasPermission($permissionKey)) {
            $this->denyAccess();
        }
    }

    private function denyAccess(): void
    {
        http_response_code(403);
        echo 'Bu modüle erişim yetkiniz yok.';
        exit;
    }
}
