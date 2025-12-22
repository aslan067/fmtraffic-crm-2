<?php

namespace App\Services;

use App\Models\Package;
use App\Models\Subscription;

class SubscriptionService
{
    public function getActiveSubscription(int $companyId): ?array
    {
        return Subscription::getActiveSubscription($companyId);
    }

    public function getPackageLimits(int $companyId): ?array
    {
        $subscription = $this->getActiveSubscription($companyId);

        if (!$subscription) {
            return null;
        }

        $package = Package::findById((int) $subscription['package_id']);

        return $package ?: null;
    }

    public function isSubscriptionActive(int $companyId): bool
    {
        return $this->getActiveSubscription($companyId) !== null;
    }
}
