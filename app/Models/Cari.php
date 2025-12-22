<?php

namespace App\Models;

use App\Core\DB;
use PDO;

class Cari
{
    public static function allByCompany(int $companyId): array
    {
        $pdo = DB::getConnection();
        $stmt = $pdo->prepare(
            'SELECT id, company_id, type, name, tax_office, tax_number, status, created_at
             FROM caris
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
            'SELECT id, company_id, type, name, tax_office, tax_number, status, created_at
             FROM caris
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
            'INSERT INTO caris (company_id, type, name, tax_office, tax_number, status)
             VALUES (:company_id, :type, :name, :tax_office, :tax_number, :status)'
        );

        $stmt->execute([
            ':company_id' => $data['company_id'],
            ':type' => $data['type'],
            ':name' => $data['name'],
            ':tax_office' => $data['tax_office'] ?? null,
            ':tax_number' => $data['tax_number'] ?? null,
            ':status' => $data['status'] ?? 'active',
        ]);

        return (int) $pdo->lastInsertId();
    }

    public static function update(int $id, int $companyId, array $data): void
    {
        $pdo = DB::getConnection();
        $stmt = $pdo->prepare(
            'UPDATE caris
             SET type = :type,
                 name = :name,
                 tax_office = :tax_office,
                 tax_number = :tax_number,
                 status = :status
             WHERE id = :id AND company_id = :company_id'
        );

        $stmt->execute([
            ':id' => $id,
            ':company_id' => $companyId,
            ':type' => $data['type'],
            ':name' => $data['name'],
            ':tax_office' => $data['tax_office'] ?? null,
            ':tax_number' => $data['tax_number'] ?? null,
            ':status' => $data['status'] ?? 'active',
        ]);
    }

    public static function deactivate(int $id, int $companyId): void
    {
        $pdo = DB::getConnection();
        $stmt = $pdo->prepare(
            'UPDATE caris SET status = :status WHERE id = :id AND company_id = :company_id'
        );

        $stmt->execute([
            ':status' => 'passive',
            ':id' => $id,
            ':company_id' => $companyId,
        ]);
    }
}
