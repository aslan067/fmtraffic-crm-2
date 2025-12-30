<?php

namespace App\Models;

use App\Core\DB;
use PDO;

class SaleItem
{
    /**
     * @return array<int, array<string, mixed>>
     */
    public static function getBySaleId(int $saleId): array
    {
        $pdo = DB::getConnection();
        $stmt = $pdo->prepare(
            'SELECT id, sale_id, product_id, product_name, quantity, unit_price, discount_rate, discount_amount, vat_rate, line_total
             FROM sale_items
             WHERE sale_id = :sale_id
             ORDER BY id ASC'
        );

        $stmt->execute([':sale_id' => $saleId]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function create(array $data): int
    {
        $pdo = DB::getConnection();
        $stmt = $pdo->prepare(
            'INSERT INTO sale_items (sale_id, product_id, product_name, quantity, unit_price, discount_rate, discount_amount, vat_rate, line_total)
             VALUES (:sale_id, :product_id, :product_name, :quantity, :unit_price, :discount_rate, :discount_amount, :vat_rate, :line_total)'
        );

        $stmt->execute([
            ':sale_id' => $data['sale_id'],
            ':product_id' => $data['product_id'] ?? null,
            ':product_name' => $data['product_name'],
            ':quantity' => $data['quantity'],
            ':unit_price' => $data['unit_price'],
            ':discount_rate' => $data['discount_rate'],
            ':discount_amount' => $data['discount_amount'],
            ':vat_rate' => $data['vat_rate'],
            ':line_total' => $data['line_total'],
        ]);

        return (int) $pdo->lastInsertId();
    }
}
