<?php

namespace App\Core;

use App\Services\PermissionSyncService;
use Throwable;

class ModuleRegistry
{
    private static ?array $modules = null;

    public static function all(): array
    {
        if (self::$modules === null) {
            $configPath = BASE_PATH . '/config/modules.php';
            self::$modules = file_exists($configPath) ? (array) require $configPath : [];

            self::syncPermissions();
        }

        return self::$modules;
    }

    public static function get(string $moduleKey): ?array
    {
        $modules = self::all();

        if (!array_key_exists($moduleKey, $modules)) {
            return null;
        }

        $module = $modules[$moduleKey];

        return is_array($module) ? $module : null;
    }

    private static function syncPermissions(): void
    {
        try {
            PermissionSyncService::sync(self::$modules ?? []);
        } catch (Throwable $e) {
            error_log('Permission sync failed: ' . $e->getMessage());
        }
    }
}
