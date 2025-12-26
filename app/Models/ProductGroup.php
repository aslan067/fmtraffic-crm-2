<?php

namespace App\Models;

use App\Core\DB;
use PDO;

class ProductGroup
{
    /**
     * @return array<int, array<string, mixed>>
     */
    public static function allByCompany(int $companyId): array
    {
        $pdo = DB::getConnection();
        $stmt = $pdo->prepare(
            'SELECT id, company_id, name, status, created_at
             FROM product_groups
             WHERE company_id = :company_id
             ORDER BY created_at DESC'
        );
        $stmt->execute([':company_id' => $companyId]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function findByIdForCompany(int $id, int $companyId): ?array
    {
        $pdo = DB::getConnection();
        $stmt = $pdo->prepare(
            'SELECT id, company_id, name, status, created_at
             FROM product_groups
             WHERE id = :id AND company_id = :company_id
             LIMIT 1'
        );
        $stmt->execute([
            ':id' => $id,
            ':company_id' => $companyId,
        ]);

        $record = $stmt->fetch(PDO::FETCH_ASSOC);

        return $record ?: null;
    }

    public static function create(array $data): int
    {
        $pdo = DB::getConnection();
        $stmt = $pdo->prepare(
            'INSERT INTO product_groups (company_id, name, status)
             VALUES (:company_id, :name, :status)'
        );

        $stmt->execute([
            ':company_id' => $data['company_id'],
            ':name' => $data['name'],
            ':status' => $data['status'] ?? 'active',
        ]);

        return (int) $pdo->lastInsertId();
    }
}
