<?php

namespace App\Middleware;

use App\Core\Auth;

class SuperAdminMiddleware
{
    public function handle(): void
    {
        if (!Auth::check()) {
            redirect('/login');
        }

        if (!Auth::isSuperAdmin()) {
            http_response_code(403);
            echo 'Bu sayfaya yalnızca Super Admin erişebilir.';
            exit;
        }
    }
}
