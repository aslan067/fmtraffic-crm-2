<?php

namespace App\Middleware;

use App\Core\Auth;

class PermissionMiddleware
{
    private string $permissionKey;

    public function __construct(string $permissionKey)
    {
        $this->permissionKey = $permissionKey;
    }

    public function handle(): void
    {
        if (!Auth::hasPermission($this->permissionKey)) {
            http_response_code(403);
            echo '403 Forbidden - Permission required: ' . htmlspecialchars($this->permissionKey, ENT_QUOTES, 'UTF-8');
            exit;
        }
    }
}
