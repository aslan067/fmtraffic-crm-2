<?php

namespace App\Middleware;

use App\Core\Auth;

class ModuleAccessMiddleware
{
    public function handle(string $moduleKey): void
    {
        if (Auth::canAccessModule($moduleKey)) {
            return;
        }

        http_response_code(403);
        echo 'Bu modüle erişim yetkiniz yok.';
        exit;
    }
}
