<?php

namespace App\Models;

use App\Core\DB;
use PDO;

class User
{
    public static function findByEmail(string $email): ?array
    {
        $pdo = DB::getConnection();
        $stmt = $pdo->prepare('SELECT id, company_id, name, email, password_hash, status, is_super_admin, created_at FROM users WHERE email = :email LIMIT 1');
        $stmt->execute([':email' => $email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        return $user ?: null;
    }

    public static function countByCompany(int $companyId): int
    {
        $pdo = DB::getConnection();
        $stmt = $pdo->prepare('SELECT COUNT(*) FROM users WHERE company_id = :company_id AND is_super_admin = 0');
        $stmt->execute([':company_id' => $companyId]);

        return (int) $stmt->fetchColumn();
    }

    public static function create(array $data): int
    {
        $pdo = DB::getConnection();
        $stmt = $pdo->prepare(
            'INSERT INTO users (company_id, name, email, password_hash, status, is_super_admin)
             VALUES (:company_id, :name, :email, :password_hash, :status, :is_super_admin)'
        );

        $stmt->execute([
            ':company_id' => $data['company_id'],
            ':name' => $data['name'],
            ':email' => $data['email'],
            ':password_hash' => $data['password_hash'],
            ':status' => $data['status'] ?? 'active',
            ':is_super_admin' => $data['is_super_admin'] ?? 0,
        ]);

        return (int) $pdo->lastInsertId();
    }
}
