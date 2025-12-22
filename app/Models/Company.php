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
}
