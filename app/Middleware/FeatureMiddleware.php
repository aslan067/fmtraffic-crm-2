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
            redirect('/login');
        }

        if (Auth::isSuperAdmin()) {
            return;
        }

        $companyId = Auth::user()['company_id'] ?? null;

        if ($companyId === null) {
            http_response_code(403);
            echo 'Mevcut paketiniz bu modülü kullanmaya yetmiyor.';
            exit;
        }

        $this->featureService()->abortIfNoFeature((int) $companyId, $featureKey);
    }

    private function featureService(): FeatureService
    {
        if (!$this->featureService instanceof FeatureService) {
            $this->featureService = new FeatureService();
        }

        return $this->featureService;
    }
}
