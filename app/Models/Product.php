<?php

namespace App\Models;

use App\Core\DB;
use PDO;

class Product
{
    /**
     * @return array<int, array<string, mixed>>
     */
    public static function allByCompany(int $companyId): array
    {
        $pdo = DB::getConnection();
        $stmt = $pdo->prepare(
            'SELECT p.id, p.company_id, p.product_group_id, p.code, p.name, p.description, p.category, p.currency, p.unit, p.image_url, p.list_price, p.stock_quantity, p.status, p.created_at,
                    pg.name AS group_name
             FROM products p
             LEFT JOIN product_groups pg ON pg.id = p.product_group_id
             WHERE p.company_id = :company_id
             ORDER BY p.created_at DESC'
        );
        $stmt->execute([':company_id' => $companyId]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    public static function allForSuperAdmin(): array
    {
        $pdo = DB::getConnection();
        $stmt = $pdo->query(
            'SELECT p.id, p.company_id, p.product_group_id, p.code, p.name, p.description, p.category, p.currency, p.unit, p.image_url, p.list_price, p.stock_quantity, p.status, p.created_at,
                    pg.name AS group_name
             FROM products p
             LEFT JOIN product_groups pg ON pg.id = p.product_group_id
             ORDER BY p.created_at DESC'
        );

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function findByIdForCompany(int $id, int $companyId): ?array
    {
        $pdo = DB::getConnection();
        $stmt = $pdo->prepare(
            'SELECT p.id, p.company_id, p.product_group_id, p.code, p.name, p.description, p.category, p.currency, p.unit, p.image_url, p.list_price, p.stock_quantity, p.status, p.created_at,
                    pg.name AS group_name
             FROM products p
             LEFT JOIN product_groups pg ON pg.id = p.product_group_id
             WHERE p.id = :id AND p.company_id = :company_id
             LIMIT 1'
        );
        $stmt->execute([
            ':id' => $id,
            ':company_id' => $companyId,
        ]);

        $record = $stmt->fetch(PDO::FETCH_ASSOC);

        return $record ?: null;
    }

    public static function findById(int $id): ?array
    {
        $pdo = DB::getConnection();
        $stmt = $pdo->prepare(
            'SELECT p.id, p.company_id, p.product_group_id, p.code, p.name, p.description, p.category, p.currency, p.unit, p.image_url, p.list_price, p.stock_quantity, p.status, p.created_at,
                    pg.name AS group_name
             FROM products p
             LEFT JOIN product_groups pg ON pg.id = p.product_group_id
             WHERE p.id = :id
             LIMIT 1'
        );
        $stmt->execute([':id' => $id]);

        $record = $stmt->fetch(PDO::FETCH_ASSOC);

        return $record ?: null;
    }

    public static function existsWithCode(int $companyId, string $code, ?int $excludeId = null): bool
    {
        $pdo = DB::getConnection();

        $sql = 'SELECT COUNT(*) FROM products WHERE company_id = :company_id AND code = :code';
        $params = [
            ':company_id' => $companyId,
            ':code' => $code,
        ];

        if ($excludeId !== null) {
            $sql .= ' AND id != :id';
            $params[':id'] = $excludeId;
        }

        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);

        return (int) $stmt->fetchColumn() > 0;
    }

    public static function create(array $data): int
    {
        $pdo = DB::getConnection();
        $stmt = $pdo->prepare(
            'INSERT INTO products (company_id, product_group_id, code, name, description, category, currency, unit, image_url, list_price, stock_quantity, status)
             VALUES (:company_id, :product_group_id, :code, :name, :description, :category, :currency, :unit, :image_url, :list_price, :stock_quantity, :status)'
        );

        $stmt->execute([
            ':company_id' => $data['company_id'],
            ':product_group_id' => $data['product_group_id'] ?? null,
            ':code' => $data['code'],
            ':name' => $data['name'],
            ':description' => $data['description'] ?? null,
            ':category' => $data['category'] ?? null,
            ':currency' => $data['currency'] ?? 'TRY',
            ':unit' => $data['unit'] ?? null,
            ':image_url' => $data['image_url'] ?? null,
            ':list_price' => $data['list_price'],
            ':stock_quantity' => $data['stock_quantity'],
            ':status' => $data['status'] ?? 'active',
        ]);

        return (int) $pdo->lastInsertId();
    }

    public static function update(int $id, int $companyId, array $data): void
    {
        $pdo = DB::getConnection();
        $stmt = $pdo->prepare(
            'UPDATE products
             SET product_group_id = :product_group_id,
                 code = :code,
                 name = :name,
                 description = :description,
                 category = :category,
                 currency = :currency,
                 unit = :unit,
                 image_url = :image_url,
                 list_price = :list_price,
                 stock_quantity = :stock_quantity,
                 status = :status
             WHERE id = :id AND company_id = :company_id'
        );

        $stmt->execute([
            ':product_group_id' => $data['product_group_id'] ?? null,
            ':code' => $data['code'],
            ':name' => $data['name'],
            ':description' => $data['description'] ?? null,
            ':category' => $data['category'] ?? null,
            ':currency' => $data['currency'] ?? 'TRY',
            ':unit' => $data['unit'] ?? null,
            ':image_url' => $data['image_url'] ?? null,
            ':list_price' => $data['list_price'],
            ':stock_quantity' => $data['stock_quantity'],
            ':status' => $data['status'] ?? 'active',
            ':id' => $id,
            ':company_id' => $companyId,
        ]);
    }

    public static function deactivate(int $id, int $companyId): void
    {
        $pdo = DB::getConnection();
        $stmt = $pdo->prepare(
            'UPDATE products SET status = :status WHERE id = :id AND company_id = :company_id'
        );
        $stmt->execute([
            ':status' => 'passive',
            ':id' => $id,
            ':company_id' => $companyId,
        ]);
    }
}
