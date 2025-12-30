<?php

namespace App\Models;

use App\Core\DB;
use PDO;

class Sale
{
    /**
     * @return array<int, array<string, mixed>>
     */
    public static function all(?int $companyId = null): array
    {
        $pdo = DB::getConnection();
        $sql = 'SELECT s.id, s.company_id, s.cari_id, s.offer_id, s.currency, s.total_amount, s.status, s.created_at,
                       c.name AS cari_name, co.name AS company_name
                FROM sales s
                INNER JOIN caris c ON c.id = s.cari_id
                LEFT JOIN companies co ON co.id = s.company_id';

        $params = [];
        if ($companyId !== null) {
            $sql .= ' WHERE s.company_id = :company_id';
            $params[':company_id'] = $companyId;
        }

        $sql .= ' ORDER BY s.created_at DESC';

        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function findById(int $id, ?int $companyId = null): ?array
    {
        $pdo = DB::getConnection();
        $sql = 'SELECT s.id, s.company_id, s.cari_id, s.offer_id, s.currency, s.total_amount, s.status, s.created_at,
                       c.name AS cari_name, c.company_id AS cari_company_id, co.name AS company_name
                FROM sales s
                INNER JOIN caris c ON c.id = s.cari_id
                LEFT JOIN companies co ON co.id = s.company_id
                WHERE s.id = :id';

        $params = [':id' => $id];

        if ($companyId !== null) {
            $sql .= ' AND s.company_id = :company_id';
            $params[':company_id'] = $companyId;
        }

        $sql .= ' LIMIT 1';

        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);

        $record = $stmt->fetch(PDO::FETCH_ASSOC);

        return $record ?: null;
    }

    public static function create(array $data): int
    {
        $pdo = DB::getConnection();
        $stmt = $pdo->prepare(
            'INSERT INTO sales (company_id, cari_id, offer_id, currency, total_amount, status)
             VALUES (:company_id, :cari_id, :offer_id, :currency, :total_amount, :status)'
        );

        $stmt->execute([
            ':company_id' => $data['company_id'],
            ':cari_id' => $data['cari_id'],
            ':offer_id' => $data['offer_id'] ?? null,
            ':currency' => $data['currency'],
            ':total_amount' => $data['total_amount'],
            ':status' => $data['status'] ?? 'active',
        ]);

        return (int) $pdo->lastInsertId();
    }
}
