<?php

namespace App\Middleware;

use App\Core\Auth;
use App\Services\FeatureService;

class FeatureMiddleware
{
    private ?FeatureService $featureService = null;

    public function handle(string $featureKey): void
    {
        if (!Auth::check()) {
            $this->denyAccess();
        }

        if (Auth::isSuperAdmin()) {
            return;
        }

        $companyId = Auth::user()['company_id'] ?? null;

        if ($companyId === null) {
            $this->denyAccess();
        }

        $this->featureService()->abortIfNoFeature((int) $companyId, $featureKey);
    }

    private function denyAccess(): void
    {
        http_response_code(403);
        echo 'Bu modüle erişim yetkiniz yok.';
        exit;
    }

    private function featureService(): FeatureService
    {
        if (!$this->featureService instanceof FeatureService) {
            $this->featureService = new FeatureService();
        }

        return $this->featureService;
    }
}
