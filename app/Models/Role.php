<?php

namespace App\Models;

use App\Core\DB;
use PDO;

class Role
{
    public static function findById(int $id): ?array
    {
        $pdo = DB::getConnection();
        $stmt = $pdo->prepare('SELECT id, company_id, name, created_at FROM roles WHERE id = :id LIMIT 1');
        $stmt->execute([':id' => $id]);
        $role = $stmt->fetch(PDO::FETCH_ASSOC);

        return $role ?: null;
    }

    public static function findByCompany(?int $companyId): array
    {
        $pdo = DB::getConnection();
        if ($companyId === null) {
            $stmt = $pdo->query('SELECT id, company_id, name, created_at FROM roles WHERE company_id IS NULL');
        } else {
            $stmt = $pdo->prepare('SELECT id, company_id, name, created_at FROM roles WHERE company_id = :company_id OR company_id IS NULL');
            $stmt->execute([':company_id' => $companyId]);
        }
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function findByName(string $name, ?int $companyId = null): ?array
    {
        $pdo = DB::getConnection();
        $stmt = $pdo->prepare('SELECT id, company_id, name, created_at FROM roles WHERE name = :name AND (company_id = :company_id OR company_id IS NULL) ORDER BY company_id IS NULL DESC LIMIT 1');
        $stmt->execute([
            ':name' => $name,
            ':company_id' => $companyId,
        ]);
        $role = $stmt->fetch(PDO::FETCH_ASSOC);

        return $role ?: null;
    }
}
