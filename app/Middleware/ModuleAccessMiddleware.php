<?php

namespace App\Middleware;

use App\Core\Auth;
use App\Models\Permission;
use Throwable;

class ModuleAccessMiddleware
{
    public function handle(string $moduleKey): void
    {
        $this->bootstrapPermissions($moduleKey);

        if (Auth::canAccessModule($moduleKey)) {
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
}
