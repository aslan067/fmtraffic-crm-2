<?php

namespace App\Controllers;

use App\Core\Auth;
use App\Core\Exceptions\DatabaseConnectionException;
use App\Models\Product;
use App\Models\ProductGroup;
use App\Services\LimitService;
use PDOException;
use Throwable;

class ProductController
{
    private LimitService $limitService;

    public function __construct()
    {
        $this->limitService = new LimitService();
    }

    public function index(): void
    {
        $isSuperAdmin = Auth::isSuperAdmin();
        $companyId = $this->resolveCompanyContext('/products', $isSuperAdmin);

        try {
            if ($isSuperAdmin) {
                $products = Product::allForSuperAdmin();
                $groups = [];
            } else {
                $products = Product::allByCompany((int) $companyId);
                $groups = ProductGroup::allByCompany((int) $companyId);
            }
        } catch (DatabaseConnectionException|PDOException $e) {
            $this->handleQueryFailure($e);
            return;
        } catch (Throwable $e) {
            $this->handleUnexpected($e);
            return;
        }

        $flash = getFlash();
        view('products/index', ['products' => $products, 'groups' => $groups, 'flash' => $flash]);
    }

    public function create(): void
    {
        $companyId = $this->requireCompanyContext('/products');

        try {
            $groups = ProductGroup::allByCompany($companyId);
        } catch (DatabaseConnectionException|PDOException $e) {
            $this->handleQueryFailure($e);
            return;
        } catch (Throwable $e) {
            $this->handleUnexpected($e);
            return;
        }

        $flash = getFlash();
        view('products/create', ['groups' => $groups, 'flash' => $flash]);
    }

    public function store(): void
    {
        $token = $_POST['csrf_token'] ?? '';
        if (!verify_csrf_token($token)) {
            setFlash('error', 'Geçersiz oturum doğrulaması.');
            redirect('/products/create');
        }

        $companyId = $this->requireCompanyContext('/products/create');

        $code = trim($_POST['code'] ?? '');
        $name = trim($_POST['name'] ?? '');
        $description = trim($_POST['description'] ?? '');
        $listPriceInput = trim($_POST['list_price'] ?? '');
        $stockInput = trim($_POST['stock_quantity'] ?? '0');
        $groupIdInput = $_POST['product_group_id'] ?? '';
        $category = trim($_POST['category'] ?? '');
        $currency = trim($_POST['currency'] ?? 'TRY');
        $unit = trim($_POST['unit'] ?? '');
        $imageUrl = trim($_POST['image_url'] ?? '');
        $newGroupName = trim($_POST['new_product_group'] ?? '');

        $listPriceNormalized = str_replace(',', '.', $listPriceInput);
        $stockQuantity = 0;
        $allowedCurrencies = ['TRY', 'USD', 'EUR'];

        if ($code === '' || $name === '' || $listPriceNormalized === '' || !is_numeric($listPriceNormalized)) {
            setFlash('error', 'Ürün kodu, adı ve liste fiyatı zorunludur.');
            redirect('/products/create');
        }

        if (!in_array($currency, $allowedCurrencies, true)) {
            setFlash('error', 'Geçersiz para birimi seçildi.');
            redirect('/products/create');
        }

        if ($stockInput !== '') {
            $validatedStock = filter_var($stockInput, FILTER_VALIDATE_INT);
            if ($validatedStock === false || (int) $validatedStock < 0) {
                setFlash('error', 'Stok miktarı negatif olamaz.');
                redirect('/products/create');
            }
            $stockQuantity = (int) $validatedStock;
        }

        if ($stockInput === '') {
            $stockQuantity = 0;
        }

        $listPrice = number_format((float) $listPriceNormalized, 2, '.', '');
        if ((float) $listPrice < 0) {
            setFlash('error', 'Liste fiyatı negatif olamaz.');
            redirect('/products/create');
        }

        try {
            $productGroupId = $this->validateExistingGroup($groupIdInput, $companyId, '/products/create');

            if (!$this->limitService->canAddProduct($companyId)) {
                setFlash('error', 'Ürün limitiniz dolmuştur. Paket yükseltiniz.');
                redirect('/products/create');
            }

            if (Product::existsWithCode($companyId, $code)) {
                setFlash('error', 'Aynı ürün kodu bu firmada zaten kullanılıyor.');
                redirect('/products/create');
            }

            if ($newGroupName !== '') {
                $productGroupId = ProductGroup::create([
                    'company_id' => $companyId,
                    'name' => $newGroupName,
                    'status' => 'active',
                ]);
            }

            Product::create([
                'company_id' => $companyId,
                'product_group_id' => $productGroupId,
                'code' => $code,
                'name' => $name,
                'description' => $description !== '' ? $description : null,
                'category' => $category !== '' ? $category : null,
                'currency' => $currency,
                'unit' => $unit !== '' ? $unit : null,
                'image_url' => $imageUrl !== '' ? $imageUrl : null,
                'list_price' => $listPrice,
                'stock_quantity' => $stockQuantity,
                'status' => 'active',
            ]);
        } catch (DatabaseConnectionException|PDOException $e) {
            $this->handleQueryFailure($e);
            return;
        } catch (Throwable $e) {
            $this->handleUnexpected($e);
            return;
        }

        setFlash('success', 'Ürün başarıyla oluşturuldu.');
        redirect('/products');
    }

    public function edit($id): void
    {
        $isSuperAdmin = Auth::isSuperAdmin();
        $companyId = $this->resolveCompanyContext('/products', $isSuperAdmin);
        $productId = (int) $id;

        try {
            if ($isSuperAdmin) {
                $product = Product::findById($productId);
                $companyId = $product ? (int) ($product['company_id'] ?? 0) : null;
            } else {
                $product = Product::findByIdForCompany($productId, (int) $companyId);
            }

            $groups = $companyId ? ProductGroup::allByCompany((int) $companyId) : [];
        } catch (DatabaseConnectionException|PDOException $e) {
            $this->handleQueryFailure($e);
            return;
        } catch (Throwable $e) {
            $this->handleUnexpected($e);
            return;
        }

        if (!$product) {
            setFlash('error', 'Ürün bulunamadı veya erişim yetkiniz yok.');
            redirect('/products');
        }

        $flash = getFlash();
        view('products/edit', ['product' => $product, 'groups' => $groups, 'flash' => $flash]);
    }

    public function update($id): void
    {
        $token = $_POST['csrf_token'] ?? '';
        if (!verify_csrf_token($token)) {
            setFlash('error', 'Geçersiz oturum doğrulaması.');
            redirect('/products');
        }

        $isSuperAdmin = Auth::isSuperAdmin();
        $companyId = $this->resolveCompanyContext('/products', $isSuperAdmin);
        $productId = (int) $id;

        $code = trim($_POST['code'] ?? '');
        $name = trim($_POST['name'] ?? '');
        $description = trim($_POST['description'] ?? '');
        $category = trim($_POST['category'] ?? '');
        $currency = trim($_POST['currency'] ?? 'TRY');
        $unit = trim($_POST['unit'] ?? '');
        $imageUrl = trim($_POST['image_url'] ?? '');
        $listPriceInput = trim($_POST['list_price'] ?? '');
        $stockInput = trim($_POST['stock_quantity'] ?? '0');
        $status = trim($_POST['status'] ?? 'active');
        $groupIdInput = $_POST['product_group_id'] ?? '';
        $newGroupName = trim($_POST['new_product_group'] ?? '');

        $allowedStatus = ['active', 'passive'];
        $allowedCurrencies = ['TRY', 'USD', 'EUR'];
        $listPriceNormalized = str_replace(',', '.', $listPriceInput);

        if ($code === '' || $name === '' || $listPriceNormalized === '' || !is_numeric($listPriceNormalized)) {
            setFlash('error', 'Ürün kodu, adı ve liste fiyatı zorunludur.');
            redirect('/products/' . $productId . '/edit');
        }

        if ($stockInput !== '') {
            $validatedStock = filter_var($stockInput, FILTER_VALIDATE_INT);
            if ($validatedStock === false || (int) $validatedStock < 0) {
                setFlash('error', 'Stok miktarı negatif olamaz.');
                redirect('/products/' . $productId . '/edit');
            }
            $stockQuantity = (int) $validatedStock;
        } else {
            $stockQuantity = 0;
        }

        if (!in_array($status, $allowedStatus, true)) {
            setFlash('error', 'Geçersiz durum bilgisi.');
            redirect('/products/' . $productId . '/edit');
        }

        if (!in_array($currency, $allowedCurrencies, true)) {
            setFlash('error', 'Geçersiz para birimi seçildi.');
            redirect('/products/' . $productId . '/edit');
        }

        $listPrice = number_format((float) $listPriceNormalized, 2, '.', '');

        if ((float) $listPrice < 0) {
            setFlash('error', 'Liste fiyatı negatif olamaz.');
            redirect('/products/' . $productId . '/edit');
        }

        try {
            $product = $isSuperAdmin
                ? Product::findById($productId)
                : Product::findByIdForCompany($productId, (int) $companyId);
            if (!$product) {
                setFlash('error', 'Ürün bulunamadı veya erişim yetkiniz yok.');
                redirect('/products');
            }

            $companyIdForProduct = (int) ($product['company_id'] ?? 0);
            $productGroupId = $this->validateExistingGroup($groupIdInput, $companyIdForProduct, '/products/' . $productId . '/edit');

            if (Product::existsWithCode($companyIdForProduct, $code, $productId)) {
                setFlash('error', 'Aynı ürün kodu bu firmada zaten kullanılıyor.');
                redirect('/products/' . $productId . '/edit');
            }

            if ($newGroupName !== '') {
                $productGroupId = ProductGroup::create([
                    'company_id' => $companyIdForProduct,
                    'name' => $newGroupName,
                    'status' => 'active',
                ]);
            }

            Product::update($productId, $companyIdForProduct, [
                'product_group_id' => $productGroupId,
                'code' => $code,
                'name' => $name,
                'description' => $description !== '' ? $description : null,
                'category' => $category !== '' ? $category : null,
                'currency' => $currency,
                'unit' => $unit !== '' ? $unit : null,
                'image_url' => $imageUrl !== '' ? $imageUrl : null,
                'list_price' => $listPrice,
                'stock_quantity' => $stockQuantity,
                'status' => $status,
            ]);
        } catch (DatabaseConnectionException|PDOException $e) {
            $this->handleQueryFailure($e);
            return;
        } catch (Throwable $e) {
            $this->handleUnexpected($e);
            return;
        }

        setFlash('success', 'Ürün bilgileri güncellendi.');
        redirect('/products');
    }

    public function deactivate($id): void
    {
        $token = $_POST['csrf_token'] ?? '';
        if (!verify_csrf_token($token)) {
            setFlash('error', 'Geçersiz oturum doğrulaması.');
            redirect('/products');
        }

        $isSuperAdmin = Auth::isSuperAdmin();
        $companyId = $this->resolveCompanyContext('/products', $isSuperAdmin);
        $productId = (int) $id;

        try {
            if ($isSuperAdmin) {
                $product = Product::findById($productId);
                if ($product && isset($product['company_id'])) {
                    Product::deactivate($productId, (int) $product['company_id']);
                }
            } else {
                Product::deactivate($productId, (int) $companyId);
            }
        } catch (DatabaseConnectionException|PDOException $e) {
            $this->handleQueryFailure($e);
            return;
        } catch (Throwable $e) {
            $this->handleUnexpected($e);
            return;
        }

        setFlash('success', 'Ürün pasife alındı.');
        redirect('/products');
    }

    private function validateExistingGroup($groupIdInput, int $companyId, string $redirectPath): ?int
    {
        if ($groupIdInput === '' || $groupIdInput === null) {
            return null;
        }

        $groupId = (int) $groupIdInput;
        $group = ProductGroup::findByIdForCompany($groupId, $companyId);

        if (!$group) {
            setFlash('error', 'Seçilen ürün grubu bulunamadı veya erişim yetkiniz yok.');
            redirect($redirectPath);
        }

        if (($group['status'] ?? '') !== 'active') {
            setFlash('error', 'Seçilen ürün grubu aktif değil.');
            redirect($redirectPath);
        }

        return $groupId;
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

    private function resolveCompanyContext(string $redirectPath, bool $allowSuperAdmin = false): ?int
    {
        $user = Auth::user();
        $companyId = $user['company_id'] ?? null;

        if ($companyId === null) {
            if ($allowSuperAdmin && Auth::isSuperAdmin()) {
                return null;
            }

            setFlash('error', 'Bu işlem için bir firma bağlamı gereklidir.');
            redirect($redirectPath);
        }

        return (int) $companyId;
    }

    private function handleQueryFailure(Throwable $e): void
    {
        http_response_code(500);
        error_log('Product SQL error: ' . $e->getMessage());
        echo 'Ürün verileri yüklenirken bir sistem hatası oluştu.';
        exit;
    }

    private function handleUnexpected(Throwable $e): void
    {
        http_response_code(500);
        error_log('Unexpected product error: ' . $e->getMessage());
        echo 'Ürün verileri yüklenirken bir sistem hatası oluştu.';
        exit;
    }
}
