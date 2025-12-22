<?php

namespace App\Models;

use App\Core\DB;
use PDO;

class Role
{
    public static function findById(int $id, ?int $companyId = null): ?array
    {
        $pdo = DB::getConnection();
        $query = 'SELECT id, company_id, name, created_at FROM roles WHERE id = :id';
        $params = [':id' => $id];

        if ($companyId !== null) {
            $query .= ' AND (company_id IS NULL OR company_id = :company_id)';
            $params[':company_id'] = $companyId;
        }

        $query .= ' LIMIT 1';

        $stmt = $pdo->prepare($query);
        $stmt->execute($params);

        $role = $stmt->fetch(PDO::FETCH_ASSOC);

        return $role ?: null;
    }

    public static function findByCompany(int $companyId): array
    {
        $pdo = DB::getConnection();
        $stmt = $pdo->prepare('SELECT id, company_id, name, created_at FROM roles WHERE company_id IS NULL OR company_id = :company_id ORDER BY company_id IS NULL DESC, name ASC');
        $stmt->execute([':company_id' => $companyId]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function findByNameForCompany(string $name, int $companyId): ?array
    {
        $pdo = DB::getConnection();
        $stmt = $pdo->prepare('SELECT id, company_id, name, created_at FROM roles WHERE name = :name AND (company_id IS NULL OR company_id = :company_id) LIMIT 1');
        $stmt->execute([
            ':name' => $name,
            ':company_id' => $companyId,
        ]);

        $role = $stmt->fetch(PDO::FETCH_ASSOC);

        return $role ?: null;
    }

    public static function getRolesForUser(int $userId, int $companyId): array
    {
        $pdo = DB::getConnection();
        $stmt = $pdo->prepare(
            'SELECT r.id, r.company_id, r.name, r.created_at
             FROM user_roles ur
             INNER JOIN roles r ON r.id = ur.role_id
             WHERE ur.user_id = :user_id AND (r.company_id IS NULL OR r.company_id = :company_id)
             ORDER BY r.company_id IS NULL DESC, r.name ASC'
        );

        $stmt->execute([
            ':user_id' => $userId,
            ':company_id' => $companyId,
        ]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function getPermissionKeys(int $roleId): array
    {
        $pdo = DB::getConnection();
        $stmt = $pdo->prepare(
            'SELECT p.`key`
             FROM role_permissions rp
             INNER JOIN permissions p ON p.id = rp.permission_id
             WHERE rp.role_id = :role_id
             ORDER BY p.`key` ASC'
        );

        $stmt->execute([':role_id' => $roleId]);

        return $stmt->fetchAll(PDO::FETCH_COLUMN) ?: [];
    }
}
