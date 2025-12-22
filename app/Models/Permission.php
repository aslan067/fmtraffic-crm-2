<?php

namespace App\Models;

use App\Core\DB;
use PDO;

class Permission
{
    public static function findById(int $id): ?array
    {
        $pdo = DB::getConnection();
        $stmt = $pdo->prepare('SELECT id, `key`, description FROM permissions WHERE id = :id LIMIT 1');
        $stmt->execute([':id' => $id]);
        $permission = $stmt->fetch(PDO::FETCH_ASSOC);

        return $permission ?: null;
    }

    public static function findByKey(string $key): ?array
    {
        $pdo = DB::getConnection();
        $stmt = $pdo->prepare('SELECT id, `key`, description FROM permissions WHERE `key` = :key LIMIT 1');
        $stmt->execute([':key' => $key]);
        $permission = $stmt->fetch(PDO::FETCH_ASSOC);

        return $permission ?: null;
    }

    public static function all(): array
    {
        $pdo = DB::getConnection();
        $stmt = $pdo->query('SELECT id, `key`, description FROM permissions');
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
