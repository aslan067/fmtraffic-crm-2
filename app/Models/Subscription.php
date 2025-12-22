<?php

namespace App\Models;

use App\Core\DB;
use PDO;

class Subscription
{
    public static function findById(int $id): ?array
    {
        $pdo = DB::getConnection();
        $stmt = $pdo->prepare('SELECT id, company_id, package_id, status, started_at, ends_at FROM subscriptions WHERE id = :id LIMIT 1');
        $stmt->execute([':id' => $id]);

        $subscription = $stmt->fetch(PDO::FETCH_ASSOC);

        return $subscription ?: null;
    }

    public static function findByCompany(int $companyId): array
    {
        $pdo = DB::getConnection();
        $stmt = $pdo->prepare('SELECT id, company_id, package_id, status, started_at, ends_at FROM subscriptions WHERE company_id = :company_id ORDER BY started_at DESC');
        $stmt->execute([':company_id' => $companyId]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function getActiveSubscription(int $companyId): ?array
    {
        $pdo = DB::getConnection();
        $stmt = $pdo->prepare(
            'SELECT id, company_id, package_id, status, started_at, ends_at
             FROM subscriptions
             WHERE company_id = :company_id
               AND status IN (\'trial\', \'active\')
               AND ends_at >= NOW()
             ORDER BY ends_at DESC
             LIMIT 1'
        );

        $stmt->execute([':company_id' => $companyId]);

        $subscription = $stmt->fetch(PDO::FETCH_ASSOC);

        return $subscription ?: null;
    }

    public static function create(array $data): int
    {
        $pdo = DB::getConnection();
        $stmt = $pdo->prepare(
            'INSERT INTO subscriptions (company_id, package_id, status, started_at, ends_at)
             VALUES (:company_id, :package_id, :status, :started_at, :ends_at)'
        );

        $stmt->execute([
            ':company_id' => (int) $data['company_id'],
            ':package_id' => (int) $data['package_id'],
            ':status' => $data['status'],
            ':started_at' => $data['started_at'],
            ':ends_at' => $data['ends_at'],
        ]);

        return (int) $pdo->lastInsertId();
    }
}
