<?php

namespace App\Controllers;

use App\Core\Auth;
use App\Core\DB;
use App\Core\Exceptions\DatabaseConnectionException;
use App\Models\Offer;
use App\Models\OfferItem;
use App\Models\Permission;
use App\Models\Sale;
use App\Models\SaleItem;
use PDOException;
use Throwable;

class SaleController
{
    public function __construct()
    {
        $this->ensurePermissionsSeeded();
    }

    public function index(): void
    {
        $this->assertModuleAccess('sales');
        $isSuperAdmin = Auth::isSuperAdmin();
        $companyId = $isSuperAdmin ? null : $this->requireCompanyContext('/sales');

        try {
            $sales = Sale::all($companyId);
        } catch (DatabaseConnectionException|PDOException $e) {
            $this->handleQueryFailure($e);
            return;
        } catch (Throwable $e) {
            $this->handleUnexpected($e);
            return;
        }

        $flash = getFlash();
        view('sales/index', ['sales' => $sales, 'flash' => $flash, 'isSuperAdmin' => $isSuperAdmin]);
    }

    public function show($id): void
    {
        $this->assertModuleAccess('sales');
        $isSuperAdmin = Auth::isSuperAdmin();
        $companyId = $isSuperAdmin ? null : $this->requireCompanyContext('/sales');
        $saleId = (int) $id;

        try {
            $sale = Sale::findById($saleId, $companyId);
            $items = $sale ? SaleItem::getBySaleId($saleId) : [];
        } catch (DatabaseConnectionException|PDOException $e) {
            $this->handleQueryFailure($e);
            return;
        } catch (Throwable $e) {
            $this->handleUnexpected($e);
            return;
        }

        if (!$sale) {
            setFlash('error', 'Satış bulunamadı veya erişim yetkiniz yok.');
            redirect('/sales');
        }

        $flash = getFlash();
        view('sales/show', ['sale' => $sale, 'items' => $items, 'flash' => $flash]);
    }

    public function fromOffer($offerId): void
    {
        $this->assertModuleAccess('sales');
        $this->assertPermission('sale.create', '/offers');

        $isSuperAdmin = Auth::isSuperAdmin();
        $companyId = $isSuperAdmin ? null : $this->requireCompanyContext('/offers');
        $offerId = (int) $offerId;

        try {
            $offer = Offer::findById($offerId, $companyId);
        } catch (DatabaseConnectionException|PDOException $e) {
            $this->handleQueryFailure($e);
            return;
        } catch (Throwable $e) {
            $this->handleUnexpected($e);
            return;
        }

        if (!$offer) {
            setFlash('error', 'Teklif bulunamadı veya erişim yetkiniz yok.');
            redirect('/offers');
        }

        if (($offer['status'] ?? '') !== 'approved') {
            setFlash('error', 'Sadece onaylı teklifler satışa dönüştürülebilir.');
            redirect('/offers/' . $offerId);
        }

        $targetCompanyId = (int) ($offer['company_id'] ?? 0);

        if ($targetCompanyId <= 0) {
            setFlash('error', 'Geçersiz firma bilgisi.');
            redirect('/offers');
        }

        try {
            $pdo = DB::getConnection();
            $items = OfferItem::getByOfferId($offerId);

            if (empty($items)) {
                setFlash('error', 'Teklifte satır bulunamadı, satışa dönüştürülemedi.');
                redirect('/offers/' . $offerId);
            }

            $pdo->beginTransaction();

            $saleId = Sale::create([
                'company_id' => $targetCompanyId,
                'cari_id' => (int) ($offer['cari_id'] ?? 0),
                'offer_id' => $offerId,
                'currency' => (string) ($offer['currency'] ?? 'TRY'),
                'total_amount' => (float) ($offer['total_amount'] ?? 0),
                'status' => 'active',
            ]);

            foreach ($items as $item) {
                SaleItem::create([
                    'sale_id' => $saleId,
                    'product_id' => $item['product_id'] ?? null,
                    'product_name' => (string) ($item['product_name'] ?? ''),
                    'quantity' => $item['quantity'],
                    'unit_price' => $item['unit_price'],
                    'discount_rate' => $item['discount_rate'],
                    'discount_amount' => $item['discount_amount'],
                    'vat_rate' => $item['vat_rate'],
                    'line_total' => $item['line_total'],
                ]);
            }

            $pdo->commit();
        } catch (DatabaseConnectionException|PDOException $e) {
            if (isset($pdo) && $pdo->inTransaction()) {
                $pdo->rollBack();
            }
            $this->handleQueryFailure($e);
            return;
        } catch (Throwable $e) {
            if (isset($pdo) && $pdo->inTransaction()) {
                $pdo->rollBack();
            }
            $this->handleUnexpected($e);
            return;
        }

        setFlash('success', 'Teklif başarıyla satışa dönüştürüldü.');
        redirect('/sales/' . $saleId);
    }

    private function assertModuleAccess(string $moduleKey): void
    {
        if (Auth::canAccessModule($moduleKey)) {
            return;
        }

        http_response_code(403);
        echo 'Bu modüle erişim yetkiniz yok.';
        exit;
    }

    private function assertPermission(string $permissionKey, string $redirectPath): void
    {
        if (Auth::isSuperAdmin()) {
            return;
        }

        if (!Auth::hasPermission($permissionKey)) {
            http_response_code(403);
            echo 'Bu işlem için yetkiniz yok.';
            exit;
        }
    }

    private function requireCompanyContext(string $redirectPath): int
    {
        $user = Auth::user();
        $companyId = $user['company_id'] ?? null;

        if ($companyId === null) {
            $message = Auth::isSuperAdmin()
                ? 'Firma seçilmedi. Lütfen önce bir firma seçin.'
                : 'Bu işlem için bir firma bağlamı gereklidir.';
            setFlash('error', $message);
            redirect($redirectPath);
        }

        return (int) $companyId;
    }

    private function handleQueryFailure(Throwable $e): void
    {
        http_response_code(500);
        error_log('Sale SQL error: ' . $e->getMessage());
        echo 'Satış verileri yüklenirken bir hata oluştu.';
        exit;
    }

    private function handleUnexpected(Throwable $e): void
    {
        http_response_code(500);
        error_log('Unexpected sale error: ' . $e->getMessage());
        echo 'Satış verileri yüklenirken bir hata oluştu.';
        exit;
    }

    private function ensurePermissionsSeeded(): void
    {
        try {
            Permission::ensurePermissionWithRoles('sale.view', 'Satışları görüntüleme', ['Admin', 'Sales']);
            Permission::ensurePermissionWithRoles('sale.create', 'Satış oluşturma', ['Admin', 'Sales']);
        } catch (Throwable $e) {
            error_log('Sale permission seed error: ' . $e->getMessage());
        }
    }
}
