<?php

namespace App\Models;

use App\Core\DB;
use PDO;

class Package
{
    public static function findById(int $id): ?array
    {
        $pdo = DB::getConnection();
        $stmt = $pdo->prepare('SELECT id, name, max_users, max_products, max_caris, created_at FROM packages WHERE id = :id LIMIT 1');
        $stmt->execute([':id' => $id]);
        $package = $stmt->fetch(PDO::FETCH_ASSOC);

        return $package ?: null;
    }

    public static function all(): array
    {
        $pdo = DB::getConnection();
        $stmt = $pdo->query('SELECT id, name, max_users, max_products, max_caris, created_at FROM packages');
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
