<?php

namespace App\Controllers;

use App\Core\Auth;
use App\Core\DB;
use App\Core\Exceptions\DatabaseConnectionException;
use App\Models\Cari;
use App\Models\Offer;
use App\Models\OfferItem;
use App\Models\Permission;
use App\Models\Product;
use PDOException;
use Throwable;

class OfferController
{
    public function __construct()
    {
        $this->ensurePermissionsSeeded();
    }

    public function index(): void
    {
        $this->assertModuleAccess('offers');
        $isSuperAdmin = Auth::isSuperAdmin();
        $companyId = $isSuperAdmin ? null : $this->requireCompanyContext('/offers');

        try {
            $offers = Offer::all($companyId);
        } catch (DatabaseConnectionException|PDOException $e) {
            $this->handleQueryFailure($e);
            return;
        } catch (Throwable $e) {
            $this->handleUnexpected($e);
            return;
        }

        $flash = getFlash();
        view('offers/index', ['offers' => $offers, 'flash' => $flash, 'isSuperAdmin' => $isSuperAdmin]);
    }

    public function create(): void
    {
        $this->assertModuleAccess('offers');
        $this->assertPermission('offer.create', '/offers');
        $user = Auth::user();
        $companyId = $user['company_id'] ?? null;

        if ($companyId === null) {
            setFlash('error', 'Teklif oluşturmak için bir firma bağlamı gereklidir.');
            redirect('/offers');
        }

        try {
            $caris = Cari::all((int) $companyId);
            $products = Product::allByCompany((int) $companyId);
        } catch (DatabaseConnectionException|PDOException $e) {
            $this->handleQueryFailure($e);
            return;
        } catch (Throwable $e) {
            $this->handleUnexpected($e);
            return;
        }

        $flash = getFlash();
        view('offers/create', [
            'caris' => $caris,
            'products' => $products,
            'flash' => $flash,
        ]);
    }

    public function store(): void
    {
        $this->assertModuleAccess('offers');
        $token = $_POST['csrf_token'] ?? '';
        if (!verify_csrf_token($token)) {
            setFlash('error', 'Geçersiz oturum doğrulaması.');
            redirect('/offers/create');
        }

        $this->assertPermission('offer.create', '/offers');
        $user = Auth::user();
        $currentCompanyId = $user['company_id'] ?? null;
        $userId = $user['id'] ?? null;

        $cariId = (int) ($_POST['cari_id'] ?? 0);
        $currency = trim((string) ($_POST['currency'] ?? 'TRY'));
        $allowedCurrencies = ['TRY', 'USD', 'EUR'];

        if (!in_array($currency, $allowedCurrencies, true)) {
            setFlash('error', 'Geçersiz para birimi seçildi.');
            redirect('/offers/create');
        }

        if ($cariId <= 0) {
            setFlash('error', 'Cari seçimi zorunludur.');
            redirect('/offers/create');
        }

        try {
            $cari = Cari::findById($cariId, $currentCompanyId);
        } catch (DatabaseConnectionException|PDOException $e) {
            $this->handleQueryFailure($e);
            return;
        } catch (Throwable $e) {
            $this->handleUnexpected($e);
            return;
        }

        if (!$cari) {
            setFlash('error', 'Cari bulunamadı veya erişim yetkiniz yok.');
            redirect('/offers/create');
        }

        $targetCompanyId = (int) ($cari['company_id'] ?? 0);
        if ($targetCompanyId <= 0) {
            setFlash('error', 'Geçersiz firma bilgisi.');
            redirect('/offers/create');
        }

        $itemsData = $this->collectItemsFromRequest($targetCompanyId);
        if (empty($itemsData)) {
            setFlash('error', 'En az bir ürün satırı eklemelisiniz.');
            redirect('/offers/create');
        }

        if ($userId === null) {
            setFlash('error', 'Oturum bilgisi bulunamadı.');
            redirect('/offers');
        }

        $pdo = DB::getConnection();

        try {
            $pdo->beginTransaction();

            $offerId = Offer::create([
                'company_id' => $targetCompanyId,
                'cari_id' => $cariId,
                'currency' => $currency,
                'status' => 'draft',
                'total_amount' => 0,
                'created_by' => (int) $userId,
            ]);

            $total = 0.0;
            foreach ($itemsData as $item) {
                $item['offer_id'] = $offerId;
                OfferItem::create($item);
                $total += (float) $item['line_total'];
            }

            Offer::updateTotal($offerId, $total);

            $pdo->commit();
        } catch (DatabaseConnectionException|PDOException $e) {
            $pdo->rollBack();
            $this->handleQueryFailure($e);
            return;
        } catch (Throwable $e) {
            $pdo->rollBack();
            $this->handleUnexpected($e);
            return;
        }

        setFlash('success', 'Teklif başarıyla oluşturuldu.');
        redirect('/offers');
    }

    public function show($id): void
    {
        $this->assertModuleAccess('offers');
        $isSuperAdmin = Auth::isSuperAdmin();
        $companyId = $isSuperAdmin ? null : $this->requireCompanyContext('/offers');
        $offerId = (int) $id;

        try {
            $offer = Offer::findById($offerId, $companyId);
            $items = $offer ? OfferItem::getByOfferId($offerId) : [];
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

        $flash = getFlash();
        view('offers/show', [
            'offer' => $offer,
            'items' => $items,
            'flash' => $flash,
            'canUpdateStatus' => can('offer.update_status'),
        ]);
    }

    public function updateStatus($id): void
    {
        $this->assertModuleAccess('offers');
        $token = $_POST['csrf_token'] ?? '';
        if (!verify_csrf_token($token)) {
            setFlash('error', 'Geçersiz oturum doğrulaması.');
            redirect('/offers');
        }

        $this->assertPermission('offer.update_status', '/offers');
        $isSuperAdmin = Auth::isSuperAdmin();
        $companyId = $isSuperAdmin ? null : $this->requireCompanyContext('/offers');
        $offerId = (int) $id;
        $newStatus = (string) ($_POST['status'] ?? '');
        $allowedStatus = ['sent', 'approved', 'rejected'];

        if (!in_array($newStatus, $allowedStatus, true)) {
            setFlash('error', 'Geçersiz durum bilgisi.');
            redirect('/offers/' . $offerId);
        }

        try {
            $offer = Offer::findById($offerId, $companyId);
            if (!$offer) {
                setFlash('error', 'Teklif bulunamadı veya erişim yetkiniz yok.');
                redirect('/offers');
            }

            $targetCompanyId = (int) ($offer['company_id'] ?? 0);
            Offer::updateStatus($offerId, $targetCompanyId, $newStatus);
        } catch (DatabaseConnectionException|PDOException $e) {
            $this->handleQueryFailure($e);
            return;
        } catch (Throwable $e) {
            $this->handleUnexpected($e);
            return;
        }

        setFlash('success', 'Teklif durumu güncellendi.');
        redirect('/offers/' . $offerId);
    }

    private function collectItemsFromRequest(int $companyId): array
    {
        $productIds = $_POST['product_id'] ?? [];
        $productNames = $_POST['product_name'] ?? [];
        $quantities = $_POST['quantity'] ?? [];
        $unitPrices = $_POST['unit_price'] ?? [];
        $discountRates = $_POST['discount_rate'] ?? [];
        $discountAmounts = $_POST['discount_amount'] ?? [];
        $vatRates = $_POST['vat_rate'] ?? [];

        $items = [];

        foreach ($productNames as $index => $rawName) {
            $quantityInput = $quantities[$index] ?? '';
            $unitPriceInput = $unitPrices[$index] ?? '';
            $vatRateInput = $vatRates[$index] ?? '';

            $quantity = (float) str_replace(',', '.', (string) $quantityInput);
            $unitPrice = (float) str_replace(',', '.', (string) $unitPriceInput);
            $vatRate = (float) str_replace(',', '.', (string) $vatRateInput);

            if ($quantity <= 0 || $unitPrice < 0 || $vatRate < 0) {
                continue;
            }

            $productId = isset($productIds[$index]) ? (int) $productIds[$index] : null;
            $product = null;

            if ($productId) {
                $product = Product::findByIdForCompany($productId, $companyId);
                if (!$product) {
                    continue;
                }
            }

            $name = trim((string) $rawName);
            if ($name === '' && $product) {
                $name = (string) ($product['name'] ?? '');
            }

            if ($name === '') {
                continue;
            }

            $net = $quantity * $unitPrice;

            $discountRate = isset($discountRates[$index]) && $discountRates[$index] !== ''
                ? (float) str_replace(',', '.', (string) $discountRates[$index])
                : null;

            $discountAmountInput = isset($discountAmounts[$index]) && $discountAmounts[$index] !== ''
                ? (float) str_replace(',', '.', (string) $discountAmounts[$index])
                : null;

            $calculatedDiscount = 0.0;
            if ($discountAmountInput !== null && $discountAmountInput > 0) {
                $calculatedDiscount = min($net, $discountAmountInput);
            } elseif ($discountRate !== null && $discountRate > 0) {
                $effectiveRate = min(100, $discountRate);
                $calculatedDiscount = min($net, $net * $effectiveRate / 100);
                $discountRate = $effectiveRate;
            } else {
                $discountRate = null;
            }

            $taxable = max(0, $net - $calculatedDiscount);
            $vatAmount = $taxable * $vatRate / 100;
            $lineTotal = $taxable + $vatAmount;

            $items[] = [
                'product_id' => $productId,
                'product_name' => $name,
                'quantity' => number_format($quantity, 2, '.', ''),
                'unit_price' => number_format($unitPrice, 2, '.', ''),
                'discount_rate' => $discountRate !== null ? number_format($discountRate, 2, '.', '') : null,
                'discount_amount' => number_format($calculatedDiscount, 2, '.', ''),
                'vat_rate' => number_format($vatRate, 2, '.', ''),
                'line_total' => number_format($lineTotal, 2, '.', ''),
            ];
        }

        return $items;
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

    private function handleQueryFailure(Throwable $e): void
    {
        http_response_code(500);
        error_log('Offer SQL error: ' . $e->getMessage());
        echo 'Teklif verileri yüklenirken bir hata oluştu.';
        exit;
    }

    private function handleUnexpected(Throwable $e): void
    {
        http_response_code(500);
        error_log('Unexpected offer error: ' . $e->getMessage());
        echo 'Teklif verileri yüklenirken bir hata oluştu.';
        exit;
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
            setFlash('error', 'Bu işlem için yetkiniz yok.');
            redirect($redirectPath);
        }
    }

    private function ensurePermissionsSeeded(): void
    {
        try {
            Permission::ensurePermissionWithRoles('offer.view', 'Teklifleri görüntüleme', ['Admin', 'Sales']);
            Permission::ensurePermissionWithRoles('offer.create', 'Teklif oluşturma', ['Admin', 'Sales']);
            Permission::ensurePermissionWithRoles('offer.update_status', 'Teklif durumu güncelleme', ['Admin', 'Sales']);
        } catch (Throwable $e) {
            error_log('Offer permission seed error: ' . $e->getMessage());
        }
    }
}
