<?php

namespace App\Models;

use App\Core\DB;
use PDO;

class Cari
{
    public static function all(?int $companyId = null): array
    {
        $pdo = DB::getConnection();
        $query = 'SELECT id, company_id, name, cari_type, phone, email, status, created_at FROM caris';

        if ($companyId !== null) {
            $query .= ' WHERE company_id = :company_id';
        }

        $query .= ' ORDER BY created_at DESC';

        $stmt = $pdo->prepare($query);
        $params = $companyId !== null ? [':company_id' => $companyId] : [];
        $stmt->execute($params);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function findById(int $id, ?int $companyId = null): ?array
    {
        $pdo = DB::getConnection();
        $query = 'SELECT id, company_id, name, cari_type, phone, email, status, created_at FROM caris WHERE id = :id';
        $params = [':id' => $id];

        if ($companyId !== null) {
            $query .= ' AND company_id = :company_id';
            $params[':company_id'] = $companyId;
        }

        $query .= ' LIMIT 1';

        $stmt = $pdo->prepare($query);
        $stmt->execute($params);

        $record = $stmt->fetch(PDO::FETCH_ASSOC);

        return $record ?: null;
    }

    public static function create(array $data): int
    {
        $pdo = DB::getConnection();
        $stmt = $pdo->prepare(
            'INSERT INTO caris (company_id, name, cari_type, phone, email, status)
             VALUES (:company_id, :name, :cari_type, :phone, :email, :status)'
        );

        $stmt->execute([
            ':company_id' => $data['company_id'],
            ':name' => $data['name'],
            ':cari_type' => $data['cari_type'],
            ':phone' => $data['phone'] ?? null,
            ':email' => $data['email'] ?? null,
            ':status' => $data['status'] ?? 'active',
        ]);

        return (int) $pdo->lastInsertId();
    }

    public static function update(int $id, int $companyId, array $data): void
    {
        $pdo = DB::getConnection();
        $stmt = $pdo->prepare(
            'UPDATE caris
             SET name = :name,
                 cari_type = :cari_type,
                 phone = :phone,
                 email = :email,
                 status = :status
             WHERE id = :id AND company_id = :company_id'
        );

        $stmt->execute([
            ':id' => $id,
            ':company_id' => $companyId,
            ':name' => $data['name'],
            ':cari_type' => $data['cari_type'],
            ':phone' => $data['phone'] ?? null,
            ':email' => $data['email'] ?? null,
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
