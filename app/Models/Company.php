<?php

namespace App\Models;

use App\Core\DB;
use PDO;

class Company
{
    public static function findById(int $id): ?array
    {
        $pdo = DB::getConnection();
        $stmt = $pdo->prepare('SELECT id, name, status, created_at FROM companies WHERE id = :id LIMIT 1');
        $stmt->execute([':id' => $id]);
        $company = $stmt->fetch(PDO::FETCH_ASSOC);

        return $company ?: null;
    }

    public static function all(): array
    {
        $pdo = DB::getConnection();
        $stmt = $pdo->query('SELECT id, name, status, created_at FROM companies ORDER BY id ASC');

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function create(string $name): int
    {
        $pdo = DB::getConnection();
        $stmt = $pdo->prepare('INSERT INTO companies (name, status) VALUES (:name, :status)');
        $stmt->execute([
            ':name' => $name,
            ':status' => 'active',
        ]);

        return (int) $pdo->lastInsertId();
    }
}
