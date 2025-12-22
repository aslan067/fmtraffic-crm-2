<?php

namespace App\Services;

use App\Core\Auth;
use App\Core\DB;

class FeatureService
{
    private SubscriptionService $subscriptionService;

    public function __construct()
    {
        $this->subscriptionService = new SubscriptionService();
    }

    public function companyHasFeature(int $companyId, string $featureKey): bool
    {
        $normalizedKey = trim(strtolower($featureKey));

        if ($normalizedKey === '') {
            return false;
        }

        if ($this->allowBySuperAdmin()) {
            return true;
        }

        $subscription = $this->subscriptionService->getActiveSubscription($companyId);

        if (!$subscription) {
            return false;
        }

        $packageId = (int) $subscription['package_id'];

        $pdo = DB::getConnection();
        $stmt = $pdo->prepare(
            'SELECT 1 FROM package_features WHERE package_id = :package_id AND feature_key = :feature_key LIMIT 1'
        );
        $stmt->execute([
            ':package_id' => $packageId,
            ':feature_key' => $normalizedKey,
        ]);

        return (bool) $stmt->fetchColumn();
    }

    public function abortIfNoFeature(int $companyId, string $featureKey): void
    {
        if ($this->companyHasFeature($companyId, $featureKey)) {
            return;
        }

        http_response_code(403);
        echo 'Mevcut paketiniz bu modülü kullanmaya yetmiyor.';
        exit;
    }

    private function allowBySuperAdmin(): bool
    {
        return Auth::isSuperAdmin();
    }
}
