<?php

namespace App\Models;

use App\Core\DB;
use PDO;

class Permission
{
    public static function findById(int $id): ?array
    {
        $pdo = DB::getConnection();
        $stmt = $pdo->prepare('SELECT id, `key`, description FROM permissions WHERE id = :id LIMIT 1');
        $stmt->execute([':id' => $id]);

        $permission = $stmt->fetch(PDO::FETCH_ASSOC);

        return $permission ?: null;
    }

    public static function findByKey(string $key): ?array
    {
        $pdo = DB::getConnection();
        $stmt = $pdo->prepare('SELECT id, `key`, description FROM permissions WHERE `key` = :key LIMIT 1');
        $stmt->execute([':key' => $key]);

        $permission = $stmt->fetch(PDO::FETCH_ASSOC);

        return $permission ?: null;
    }

    public static function ensure(string $key, ?string $description = null): ?array
    {
        $pdo = DB::getConnection();
        $stmt = $pdo->prepare(
            'INSERT IGNORE INTO permissions (`key`, description) VALUES (:key, :description)'
        );
        $stmt->execute([
            ':key' => $key,
            ':description' => $description,
        ]);

        return self::findByKey($key);
    }

    public static function findByCompany(int $companyId): array
    {
        // Permissions are global; return all for convenience to match the method contract.
        return self::all();
    }

    public static function all(): array
    {
        $pdo = DB::getConnection();
        $stmt = $pdo->query('SELECT id, `key`, description FROM permissions ORDER BY `key` ASC');

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Ensure a permission exists and is attached to the given global roles.
     *
     * @param array<int, string> $roleNames
     */
    public static function ensurePermissionWithRoles(string $key, ?string $description, array $roleNames): void
    {
        $pdo = DB::getConnection();

        $stmt = $pdo->prepare(
            'INSERT INTO permissions (`key`, description) VALUES (:key, :description)
             ON DUPLICATE KEY UPDATE description = VALUES(description)'
        );
        $stmt->execute([
            ':key' => $key,
            ':description' => $description,
        ]);

        $permission = self::findByKey($key);
        if (!$permission) {
            return;
        }

        if (empty($roleNames)) {
            return;
        }

        $placeholders = implode(',', array_fill(0, count($roleNames), '?'));
        $roleStmt = $pdo->prepare(
            'SELECT id FROM roles WHERE company_id IS NULL AND name IN (' . $placeholders . ')'
        );
        $roleStmt->execute($roleNames);
        $roles = $roleStmt->fetchAll(PDO::FETCH_COLUMN);

        if (empty($roles)) {
            return;
        }

        $mappingStmt = $pdo->prepare(
            'INSERT IGNORE INTO role_permissions (role_id, permission_id) VALUES (:role_id, :permission_id)'
        );

        foreach ($roles as $roleId) {
            $mappingStmt->execute([
                ':role_id' => $roleId,
                ':permission_id' => $permission['id'],
            ]);
        }
    }

    public static function getByRole(int $roleId): array
    {
        $pdo = DB::getConnection();
        $stmt = $pdo->prepare(
            'SELECT p.id, p.`key`, p.description
             FROM role_permissions rp
             INNER JOIN permissions p ON p.id = rp.permission_id
             WHERE rp.role_id = :role_id
             ORDER BY p.`key` ASC'
        );
        $stmt->execute([':role_id' => $roleId]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function userHasPermission(int $userId, int $companyId, string $permissionKey): bool
    {
        $pdo = DB::getConnection();
        $stmt = $pdo->prepare(
            'SELECT 1
             FROM user_roles ur
             INNER JOIN roles r ON r.id = ur.role_id
             INNER JOIN role_permissions rp ON rp.role_id = r.id
             INNER JOIN permissions p ON p.id = rp.permission_id
             WHERE ur.user_id = :user_id
               AND (r.company_id IS NULL OR r.company_id = :company_id)
               AND p.`key` = :permission_key
             LIMIT 1'
        );

        $stmt->execute([
            ':user_id' => $userId,
            ':company_id' => $companyId,
            ':permission_key' => $permissionKey,
        ]);

        return (bool) $stmt->fetchColumn();
    }

    public static function getByUserAndCompany(int $userId, int $companyId): array
    {
        $pdo = DB::getConnection();
        $stmt = $pdo->prepare(
            'SELECT DISTINCT p.`key`
             FROM user_roles ur
             INNER JOIN roles r ON r.id = ur.role_id
             INNER JOIN role_permissions rp ON rp.role_id = r.id
             INNER JOIN permissions p ON p.id = rp.permission_id
             WHERE ur.user_id = :user_id
               AND (r.company_id IS NULL OR r.company_id = :company_id)
             ORDER BY p.`key` ASC'
        );

        $stmt->execute([
            ':user_id' => $userId,
            ':company_id' => $companyId,
        ]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
