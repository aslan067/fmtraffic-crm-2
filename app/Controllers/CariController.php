<?php

namespace App\Controllers;

use App\Core\Auth;
use App\Core\Exceptions\DatabaseConnectionException;
use App\Models\Cari;
use App\Services\LimitService;
use PDOException;
use Throwable;

class CariController
{
    private LimitService $limitService;

    public function __construct()
    {
        $this->limitService = new LimitService();
    }

    public function index(): void
    {
        $isSuperAdmin = Auth::isSuperAdmin();
        $companyId = $isSuperAdmin ? null : $this->requireCompanyContext('/caris');

        try {
            $caris = Cari::all($companyId);
        } catch (DatabaseConnectionException $e) {
            $this->handleDatabaseIssue($e, '/login');
            return;
        } catch (PDOException $e) {
            $this->handleQueryFailure($e, '/caris');
            return;
        } catch (Throwable $e) {
            $this->handleUnexpected($e, '/caris');
            return;
        }

        $flash = getFlash();
        view('cari/index', ['caris' => $caris, 'flash' => $flash]);
    }

    public function create(): void
    {
        $this->requireCompanyContext('/caris');
        $flash = getFlash();
        view('cari/create', ['flash' => $flash]);
    }

    public function store(): void
    {
        $token = $_POST['csrf_token'] ?? '';
        if (!verify_csrf_token($token)) {
            setFlash('error', 'Geçersiz oturum doğrulaması.');
            redirect('/caris/create');
        }

        $companyId = $this->requireCompanyContext('/caris/create');

        $cariType = trim($_POST['cari_type'] ?? '');
        $name = trim($_POST['name'] ?? '');
        $phone = trim($_POST['phone'] ?? '');
        $email = trim($_POST['email'] ?? '');

        $allowedTypes = ['customer', 'supplier', 'both'];
        if ($name === '' || !in_array($cariType, $allowedTypes, true)) {
            setFlash('error', 'Cari tipi ve ad bilgisi zorunludur.');
            redirect('/caris/create');
        }

        try {
            if (!$this->limitService->canAddCari($companyId)) {
                setFlash('error', 'Cari limitiniz dolmuştur. Paket yükseltiniz.');
                redirect('/caris/create');
            }

            $cariId = Cari::create([
                'company_id' => $companyId,
                'cari_type' => $cariType,
                'name' => $name,
                'phone' => $phone !== '' ? $phone : null,
                'email' => $email !== '' ? $email : null,
                'status' => 'active',
            ]);
        } catch (DatabaseConnectionException $e) {
            $this->handleDatabaseIssue($e, '/caris/create');
            return;
        } catch (PDOException $e) {
            $this->handleQueryFailure($e, '/caris/create');
            return;
        } catch (Throwable $e) {
            $this->handleUnexpected($e, '/caris/create');
            return;
        }

        setFlash('success', 'Cari başarıyla oluşturuldu.');
        redirect('/caris');
    }

    public function edit($id): void
    {
        $isSuperAdmin = Auth::isSuperAdmin();
        $companyId = $isSuperAdmin ? null : $this->requireCompanyContext('/caris');
        $cariId = (int) $id;

        try {
            $cari = Cari::findById($cariId, $companyId);
        } catch (DatabaseConnectionException $e) {
            $this->handleDatabaseIssue($e, '/caris');
            return;
        } catch (PDOException $e) {
            $this->handleQueryFailure($e, '/caris');
            return;
        } catch (Throwable $e) {
            $this->handleUnexpected($e, '/caris');
            return;
        }

        if (!$cari) {
            setFlash('error', 'Cari bulunamadı veya erişim yetkiniz yok.');
            redirect('/caris');
        }

        $flash = getFlash();
        view('cari/edit', ['cari' => $cari, 'flash' => $flash]);
    }

    public function update($id): void
    {
        $token = $_POST['csrf_token'] ?? '';
        if (!verify_csrf_token($token)) {
            setFlash('error', 'Geçersiz oturum doğrulaması.');
            redirect('/caris');
        }

        $isSuperAdmin = Auth::isSuperAdmin();
        $companyId = $isSuperAdmin ? null : $this->requireCompanyContext('/caris');
        $cariId = (int) $id;

        $cariType = trim($_POST['cari_type'] ?? '');
        $name = trim($_POST['name'] ?? '');
        $status = trim($_POST['status'] ?? 'active');
        $phone = trim($_POST['phone'] ?? '');
        $email = trim($_POST['email'] ?? '');

        $allowedTypes = ['customer', 'supplier', 'both'];
        $allowedStatus = ['active', 'passive'];

        if ($name === '' || !in_array($cariType, $allowedTypes, true) || !in_array($status, $allowedStatus, true)) {
            setFlash('error', 'Cari tipi, ad ve durum bilgileri zorunludur.');
            redirect('/caris/' . $cariId . '/edit');
        }

        try {
            $existing = Cari::findById($cariId, $companyId);
            if (!$existing) {
                setFlash('error', 'Cari bulunamadı veya erişim yetkiniz yok.');
                redirect('/caris');
            }

            $targetCompanyId = (int) $existing['company_id'];

            Cari::update($cariId, $targetCompanyId, [
                'cari_type' => $cariType,
                'name' => $name,
                'phone' => $phone !== '' ? $phone : null,
                'email' => $email !== '' ? $email : null,
                'status' => $status,
            ]);
        } catch (DatabaseConnectionException $e) {
            $this->handleDatabaseIssue($e, '/caris/' . $cariId . '/edit');
            return;
        } catch (PDOException $e) {
            $this->handleQueryFailure($e, '/caris/' . $cariId . '/edit');
            return;
        } catch (Throwable $e) {
            $this->handleUnexpected($e, '/caris/' . $cariId . '/edit');
            return;
        }

        setFlash('success', 'Cari bilgileri güncellendi.');
        redirect('/caris');
    }

    public function deactivate($id): void
    {
        $token = $_POST['csrf_token'] ?? '';
        if (!verify_csrf_token($token)) {
            setFlash('error', 'Geçersiz oturum doğrulaması.');
            redirect('/caris');
        }

        $isSuperAdmin = Auth::isSuperAdmin();
        $companyId = $isSuperAdmin ? null : $this->requireCompanyContext('/caris');
        $cariId = (int) $id;

        try {
            $existing = Cari::findById($cariId, $companyId);
            if (!$existing) {
                setFlash('error', 'Cari bulunamadı veya erişim yetkiniz yok.');
                redirect('/caris');
            }

            Cari::deactivate($cariId, (int) $existing['company_id']);
        } catch (DatabaseConnectionException $e) {
            $this->handleDatabaseIssue($e, '/caris');
            return;
        } catch (PDOException $e) {
            $this->handleQueryFailure($e, '/caris');
            return;
        } catch (Throwable $e) {
            $this->handleUnexpected($e, '/caris');
            return;
        }

        setFlash('success', 'Cari pasife alındı.');
        redirect('/caris');
    }

    private function requireCompanyContext(string $redirectPath): int
    {
        $user = Auth::user();
        $companyId = $user['company_id'] ?? null;

        if ($companyId === null) {
            setFlash('error', 'Bu işlem için bir firma bağlamı gereklidir.');
            redirect($redirectPath);
        }

        return (int) $companyId;
    }

    private function handleDatabaseIssue(DatabaseConnectionException $e, string $redirectPath): void
    {
        error_log('Database connection issue: ' . $e->getMessage());
        setFlash('error', 'Sistem geçici olarak kullanılamıyor (DB bağlantısı yok).');
        redirect($redirectPath);
    }

    private function handleUnexpected(Throwable $e, string $redirectPath): void
    {
        error_log('Unexpected cari error: ' . $e->getMessage());
        setFlash('error', 'Beklenmeyen bir hata oluştu. Lütfen tekrar deneyin.');
        redirect($redirectPath);
    }

    private function handleQueryFailure(PDOException $e, string $redirectPath): void
    {
        error_log('Cari SQL error: ' . $e->getMessage());
        setFlash('error', 'İşlem sırasında bir hata oluştu. Lütfen tekrar deneyin.');
        redirect($redirectPath);
    }
}
