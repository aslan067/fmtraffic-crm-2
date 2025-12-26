<?php

namespace App\Middleware;

use App\Core\Auth;

class SuperAdminMiddleware
{
    public function handle(): void
    {
        if (!Auth::check()) {
            $this->denyAccess();
        }

        if (!Auth::isSuperAdmin()) {
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
