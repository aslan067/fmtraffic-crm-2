<?php

namespace App\Controllers;

use App\Core\Auth;
use App\Models\Company;
use App\Models\Package;
use App\Models\Subscription;
use App\Services\SubscriptionService;

class SuperAdminController
{
    private SubscriptionService $subscriptionService;

    public function __construct()
    {
        $this->subscriptionService = new SubscriptionService();
    }

    public function listCompanies(): void
    {
        $this->ensureSuperAdmin();

        $companies = Company::all();
        $packages = Package::all();

        $subscriptionMap = [];
        foreach ($companies as $company) {
            $subscriptionMap[$company['id']] = $this->subscriptionService->getActiveSubscription((int) $company['id']);
        }

        $flash = $this->pullFlash();

        view('super_admin/companies', [
            'companies' => $companies,
            'packages' => $packages,
            'subscriptions' => $subscriptionMap,
            'flash' => $flash,
        ]);
    }

    public function createCompany(): void
    {
        $this->ensureSuperAdmin();

        $token = $_POST['csrf_token'] ?? '';
        if (!verify_csrf_token($token)) {
            $this->setFlash('error', 'Geçersiz oturum doğrulaması.');
            redirect('/super-admin/companies');
        }

        $name = trim($_POST['name'] ?? '');
        $packageId = isset($_POST['package_id']) ? (int) $_POST['package_id'] : 0;

        if ($name === '') {
            $this->setFlash('error', 'Firma adı gereklidir.');
            redirect('/super-admin/companies');
        }

        $companyId = Company::create($name);

        $assigned = false;
        if ($packageId > 0) {
            Subscription::create([
                'company_id' => $companyId,
                'package_id' => $packageId,
                'status' => 'active',
                'started_at' => date('Y-m-d H:i:s'),
                'ends_at' => date('Y-m-d H:i:s', strtotime('+30 days')),
            ]);
            $assigned = true;
        }

        $message = $assigned ? 'Firma oluşturuldu ve paket ataması yapıldı.' : 'Firma oluşturuldu.';
        $this->setFlash('success', $message);
        redirect('/super-admin/companies');
    }

    public function assignPackage(): void
    {
        $this->ensureSuperAdmin();

        $token = $_POST['csrf_token'] ?? '';
        if (!verify_csrf_token($token)) {
            $this->setFlash('error', 'Geçersiz oturum doğrulaması.');
            redirect('/super-admin/companies');
        }

        $companyId = isset($_POST['company_id']) ? (int) $_POST['company_id'] : 0;
        $packageId = isset($_POST['package_id']) ? (int) $_POST['package_id'] : 0;
        $status = $_POST['status'] ?? 'active';
        $allowedStatuses = ['trial', 'active', 'suspended', 'expired'];
        if (!in_array($status, $allowedStatuses, true)) {
            $status = 'active';
        }

        if ($companyId <= 0 || $packageId <= 0) {
            $this->setFlash('error', 'Firma ve paket seçimi zorunludur.');
            redirect('/super-admin/companies');
        }

        Subscription::create([
            'company_id' => $companyId,
            'package_id' => $packageId,
            'status' => $status,
            'started_at' => date('Y-m-d H:i:s'),
            'ends_at' => date('Y-m-d H:i:s', strtotime('+30 days')),
        ]);

        $this->setFlash('success', 'Paket ataması yapıldı ve abonelik güncellendi.');
        redirect('/super-admin/companies');
    }

    private function ensureSuperAdmin(): void
    {
        if (!Auth::isSuperAdmin()) {
            http_response_code(403);
            echo 'Bu sayfaya yalnızca Super Admin erişebilir.';
            exit;
        }
    }

    private function setFlash(string $key, string $message): void
    {
        $_SESSION['flash'][$key] = $message;
    }

    private function pullFlash(): array
    {
        $flash = $_SESSION['flash'] ?? [];
        unset($_SESSION['flash']);

        return $flash;
    }
}
