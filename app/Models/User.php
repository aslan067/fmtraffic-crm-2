<?php

namespace App\Models;

use App\Core\DB;
use PDO;

class User
{
    public static function findByEmail(string $email): ?array
    {
        $pdo = DB::getConnection();
        $stmt = $pdo->prepare('SELECT id, company_id, name, email, password_hash, status, created_at FROM users WHERE email = :email LIMIT 1');
        $stmt->execute([':email' => $email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        return $user ?: null;
    }
}
