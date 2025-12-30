<?php

namespace App\Services;

use App\Core\DB;
use App\Models\Permission;
use PDO;
use Throwable;

class PermissionSyncService
{
    private static bool $synced = false;

    /**
     * @param array<string, mixed> $modules
     */
    public static function sync(array $modules): void
    {
        if (self::$synced) {
            return;
        }

        self::$synced = true;

        $configPath = BASE_PATH . '/config/permissions.php';
        if (!file_exists($configPath)) {
            return;
        }

        $config = require $configPath;
        if (!is_array($config)) {
            return;
        }

        foreach ($config as $moduleKey => $definition) {
            if (!array_key_exists($moduleKey, $modules)) {
                continue;
            }

            $permissions = (array) ($definition['permissions'] ?? []);
            foreach ($permissions as $permissionDefinition) {
                self::syncPermission((array) $permissionDefinition);
            }
        }
    }

    /**
     * @param array<string, mixed> $definition
     */
    private static function syncPermission(array $definition): void
    {
        $key = (string) ($definition['key'] ?? '');
        if ($key === '') {
            return;
        }

        $description = $definition['description'] ?? null;

        try {
            $permission = Permission::ensure($key, is_string($description) ? $description : null);
        } catch (Throwable $e) {
            error_log('Permission ensure failed: ' . $e->getMessage());
            return;
        }

        if (!$permission) {
            return;
        }

        $roles = array_filter(
            array_map('strval', (array) ($definition['roles'] ?? [])),
            static fn (string $role): bool => $role !== ''
        );

        if (empty($roles)) {
            return;
        }

        self::attachToRoles((int) $permission['id'], $roles);
    }

    /**
     * @param array<int, string> $roleNames
     */
    private static function attachToRoles(int $permissionId, array $roleNames): void
    {
        $pdo = DB::getConnection();

        $placeholders = implode(',', array_fill(0, count($roleNames), '?'));
        $stmt = $pdo->prepare(
            'SELECT id FROM roles WHERE company_id IS NULL AND name IN (' . $placeholders . ')'
        );
        $stmt->execute($roleNames);
        $roleIds = $stmt->fetchAll(PDO::FETCH_COLUMN);

        if (empty($roleIds)) {
            return;
        }

        $mappingStmt = $pdo->prepare(
            'INSERT IGNORE INTO role_permissions (role_id, permission_id) VALUES (:role_id, :permission_id)'
        );

        foreach ($roleIds as $roleId) {
            $mappingStmt->execute([
                ':role_id' => (int) $roleId,
                ':permission_id' => $permissionId,
            ]);
        }
    }
}
