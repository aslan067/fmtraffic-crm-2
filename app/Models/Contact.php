<?php

namespace App\Models;

use App\Core\DB;
use PDO;

class Contact
{
    public static function allByCari(int $cariId): array
    {
        $pdo = DB::getConnection();
        $stmt = $pdo->prepare(
            'SELECT id, cari_id, name, email, phone, created_at
             FROM contacts
             WHERE cari_id = :cari_id
             ORDER BY created_at DESC'
        );
        $stmt->execute([':cari_id' => $cariId]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function findPrimaryByCari(int $cariId): ?array
    {
        $pdo = DB::getConnection();
        $stmt = $pdo->prepare(
            'SELECT id, cari_id, name, email, phone, created_at
             FROM contacts
             WHERE cari_id = :cari_id
             ORDER BY id ASC
             LIMIT 1'
        );
        $stmt->execute([':cari_id' => $cariId]);

        $record = $stmt->fetch(PDO::FETCH_ASSOC);

        return $record ?: null;
    }

    public static function create(array $data): int
    {
        $pdo = DB::getConnection();
        $stmt = $pdo->prepare(
            'INSERT INTO contacts (cari_id, name, email, phone)
             VALUES (:cari_id, :name, :email, :phone)'
        );

        $stmt->execute([
            ':cari_id' => $data['cari_id'],
            ':name' => $data['name'],
            ':email' => $data['email'] ?? null,
            ':phone' => $data['phone'] ?? null,
        ]);

        return (int) $pdo->lastInsertId();
    }

    public static function update(int $id, array $data): void
    {
        $pdo = DB::getConnection();
        $stmt = $pdo->prepare(
            'UPDATE contacts
             SET name = :name,
                 email = :email,
                 phone = :phone
             WHERE id = :id'
        );

        $stmt->execute([
            ':id' => $id,
            ':name' => $data['name'],
            ':email' => $data['email'] ?? null,
            ':phone' => $data['phone'] ?? null,
        ]);
    }

    public static function upsertPrimary(int $cariId, array $data): void
    {
        $primary = self::findPrimaryByCari($cariId);
        $hasData = trim($data['name'] ?? '') !== '' || trim($data['email'] ?? '') !== '' || trim($data['phone'] ?? '') !== '';

        if (!$hasData) {
            return;
        }

        if ($primary) {
            self::update((int) $primary['id'], $data);
            return;
        }

        self::create([
            'cari_id' => $cariId,
            'name' => $data['name'] ?? '',
            'email' => $data['email'] ?? null,
            'phone' => $data['phone'] ?? null,
        ]);
    }
}
