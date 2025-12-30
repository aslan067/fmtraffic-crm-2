<?php

namespace App\Services;

use App\Core\DB;
use App\Models\Permission;
use App\Core\PermissionVersion;
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

        $syncedAny = false;

        foreach ($config as $moduleKey => $definition) {
            if (!array_key_exists($moduleKey, $modules)) {
                continue;
            }

            $permissions = (array) ($definition['permissions'] ?? []);
            foreach ($permissions as $permissionDefinition) {
                $syncedAny = self::syncPermission((array) $permissionDefinition) || $syncedAny;
            }
        }

        if ($syncedAny) {
            PermissionVersion::bump();
        }
    }

    /**
     * @param array<string, mixed> $definition
     * @return bool True if a new permission was created.
     */
    private static function syncPermission(array $definition): bool
    {
        $key = (string) ($definition['key'] ?? '');
        if ($key === '') {
            return false;
        }

        $description = $definition['description'] ?? null;
        $permissionExisted = Permission::findByKey($key) !== null;

        try {
            $permission = Permission::ensure($key, is_string($description) ? $description : null);
        } catch (Throwable $e) {
            error_log('Permission ensure failed: ' . $e->getMessage());
            return false;
        }

        if (!$permission) {
            return false;
        }

        $roles = array_filter(
            array_map('strval', (array) ($definition['roles'] ?? [])),
            static fn (string $role): bool => $role !== ''
        );

        if (empty($roles)) {
            return !$permissionExisted;
        }

        self::attachToRoles((int) $permission['id'], $roles);

        return !$permissionExisted;
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
