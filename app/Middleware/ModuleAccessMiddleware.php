<?php

namespace App\Middleware;

use App\Core\Auth;
use App\Core\ModuleRegistry;
use App\Models\Permission;
use Throwable;

class ModuleAccessMiddleware
{
    public function handle(string $moduleKey): void
    {
        $this->bootstrapPermissions($moduleKey);

        if ($this->hasModuleAccess($moduleKey)) {
            return;
        }

        http_response_code(403);
        echo 'Bu modüle erişim yetkiniz yok.';
        exit;
    }

    private function bootstrapPermissions(string $moduleKey): void
    {
        if ($moduleKey !== 'offers') {
            return;
        }

        try {
            Permission::ensurePermissionWithRoles('offer.view', 'Teklifleri görüntüleme', ['Admin', 'Sales']);
            Permission::ensurePermissionWithRoles('offer.create', 'Teklif oluşturma', ['Admin', 'Sales']);
            Permission::ensurePermissionWithRoles('offer.update_status', 'Teklif durumu güncelleme', ['Admin', 'Sales']);
        } catch (Throwable $e) {
            error_log('Offer permission bootstrap error: ' . $e->getMessage());
        }
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
