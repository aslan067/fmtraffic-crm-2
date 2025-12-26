<?php

namespace App\Middleware;

use App\Core\Auth;

class AuthMiddleware
{
    public function handle(): void
    {
        if (!Auth::check()) {
            http_response_code(403);
            echo 'Bu modüle erişim yetkiniz yok.';
            exit;
        }
    }
}
