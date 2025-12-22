<?php

namespace App\Services;

use App\Core\DB;
use App\Models\User;

class LimitService
{
    private ?SubscriptionService $subscriptionService = null;

    public function canAddUser(int $companyId): bool
    {
        if (!$this->subscriptionService()->isSubscriptionActive($companyId)) {
            return false;
        }

        $package = $this->subscriptionService()->getPackageLimits($companyId);

        if (!$package) {
            return false;
        }

        $maxUsers = (int) $package['max_users'];

        if ($maxUsers === 0) {
            return true;
        }

        $currentUsers = User::countByCompany($companyId);

        return $currentUsers < $maxUsers;
    }

    public function canAddProduct(int $companyId): bool
    {
        return $this->checkLimit($companyId, 'products', 'max_products');
    }

    public function canAddCari(int $companyId): bool
    {
        return $this->checkLimit($companyId, 'caris', 'max_caris');
    }

    private function checkLimit(int $companyId, string $table, string $limitKey): bool
    {
        if (!$this->subscriptionService()->isSubscriptionActive($companyId)) {
            return false;
        }

        $package = $this->subscriptionService()->getPackageLimits($companyId);

        if (!$package) {
            return false;
        }

        $maxAllowed = (int) ($package[$limitKey] ?? 0);

        if ($maxAllowed === 0) {
            return true;
        }

        $currentCount = $this->countByTable($table, $companyId);

        return $currentCount < $maxAllowed;
    }

    private function subscriptionService(): SubscriptionService
    {
        if (!$this->subscriptionService instanceof SubscriptionService) {
            $this->subscriptionService = new SubscriptionService();
        }

        return $this->subscriptionService;
    }

    private function countByTable(string $table, int $companyId): int
    {
        $pdo = DB::getConnection();
        $allowedTables = [
            'products' => 'products',
            'caris' => 'caris',
        ];

        if (!isset($allowedTables[$table])) {
            return 0;
        }

        $stmt = $pdo->prepare(sprintf('SELECT COUNT(*) FROM %s WHERE company_id = :company_id', $allowedTables[$table]));
        $stmt->execute([':company_id' => $companyId]);

        return (int) $stmt->fetchColumn();
    }
}
