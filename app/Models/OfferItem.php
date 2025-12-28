<?php

namespace App\Models;

use App\Core\DB;
use PDO;

class OfferItem
{
    /**
     * @return array<int, array<string, mixed>>
     */
    public static function getByOfferId(int $offerId): array
    {
        $pdo = DB::getConnection();
        $stmt = $pdo->prepare(
            'SELECT id, offer_id, product_id, product_name, quantity, unit_price, discount_rate, discount_amount, vat_rate, line_total
             FROM offer_items
             WHERE offer_id = :offer_id
             ORDER BY id ASC'
        );

        $stmt->execute([':offer_id' => $offerId]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function create(array $data): int
    {
        $pdo = DB::getConnection();
        $stmt = $pdo->prepare(
            'INSERT INTO offer_items (offer_id, product_id, product_name, quantity, unit_price, discount_rate, discount_amount, vat_rate, line_total)
             VALUES (:offer_id, :product_id, :product_name, :quantity, :unit_price, :discount_rate, :discount_amount, :vat_rate, :line_total)'
        );

        $stmt->execute([
            ':offer_id' => $data['offer_id'],
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
