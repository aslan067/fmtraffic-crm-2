<?php

namespace App\Models;

use App\Core\DB;
use PDO;

class Offer
{
    /**
     * @return array<int, array<string, mixed>>
     */
    public static function all(?int $companyId = null): array
    {
        $pdo = DB::getConnection();
        $sql = 'SELECT o.id, o.company_id, o.cari_id, o.currency, o.status, o.total_amount, o.created_by, o.created_at,
                       c.name AS cari_name, u.name AS created_by_name, co.name AS company_name
                FROM offers o
                INNER JOIN caris c ON c.id = o.cari_id
                INNER JOIN users u ON u.id = o.created_by
                LEFT JOIN companies co ON co.id = o.company_id';

        $params = [];
        if ($companyId !== null) {
            $sql .= ' WHERE o.company_id = :company_id';
            $params[':company_id'] = $companyId;
        }

        $sql .= ' ORDER BY o.created_at DESC';

        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function findById(int $id, ?int $companyId = null): ?array
    {
        $pdo = DB::getConnection();
        $sql = 'SELECT o.id, o.company_id, o.cari_id, o.currency, o.status, o.total_amount, o.created_by, o.created_at,
                       c.name AS cari_name, c.company_id AS cari_company_id, u.name AS created_by_name, co.name AS company_name
                FROM offers o
                INNER JOIN caris c ON c.id = o.cari_id
                INNER JOIN users u ON u.id = o.created_by
                LEFT JOIN companies co ON co.id = o.company_id
                WHERE o.id = :id';

        $params = [':id' => $id];

        if ($companyId !== null) {
            $sql .= ' AND o.company_id = :company_id';
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
            'INSERT INTO offers (company_id, cari_id, currency, status, total_amount, created_by)
             VALUES (:company_id, :cari_id, :currency, :status, :total_amount, :created_by)'
        );

        $stmt->execute([
            ':company_id' => $data['company_id'],
            ':cari_id' => $data['cari_id'],
            ':currency' => $data['currency'],
            ':status' => $data['status'] ?? 'draft',
            ':total_amount' => $data['total_amount'] ?? 0,
            ':created_by' => $data['created_by'],
        ]);

        return (int) $pdo->lastInsertId();
    }

    public static function updateTotal(int $offerId, float $total): void
    {
        $pdo = DB::getConnection();
        $stmt = $pdo->prepare('UPDATE offers SET total_amount = :total WHERE id = :id');
        $stmt->execute([
            ':total' => number_format($total, 2, '.', ''),
            ':id' => $offerId,
        ]);
    }

    public static function updateStatus(int $offerId, int $companyId, string $status): void
    {
        $pdo = DB::getConnection();
        $stmt = $pdo->prepare(
            'UPDATE offers SET status = :status WHERE id = :id AND company_id = :company_id'
        );

        $stmt->execute([
            ':status' => $status,
            ':id' => $offerId,
            ':company_id' => $companyId,
        ]);
    }
}
