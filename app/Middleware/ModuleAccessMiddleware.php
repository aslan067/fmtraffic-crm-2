<?php

namespace App\Middleware;

use App\Core\Auth;
use App\Core\ModuleRegistry;

class ModuleAccessMiddleware
{
    public function handle(string $moduleKey): void
    {
        if ($this->hasModuleAccess($moduleKey)) {
            return;
        }

        http_response_code(403);
        echo 'Bu modüle erişim yetkiniz yok.';
        exit;
    }

    private function hasModuleAccess(string $moduleKey): bool
    {
        if (Auth::canAccessModule($moduleKey)) {
            return true;
        }

        $module = ModuleRegistry::get($moduleKey);
        $permissionKey = (string) ($module['permission'] ?? '');

        if ($moduleKey === 'offers' && $permissionKey !== '' && Auth::hasPermission($permissionKey)) {
            // Permission DB'de var; modülü engelleme.
            return true;
        }

        return false;
    }
}
