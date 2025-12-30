<?php
$title = 'Dashboard';
ob_start();
?>
<div class="container-fluid g-3">
    <div class="d-flex justify-content-between align-items-start flex-wrap mb-4">
        <div>
            <p class="text-uppercase text-muted small mb-1">Kontrol Paneli</p>
            <h1 class="h3 mb-1">Hoş geldiniz, <?php echo htmlspecialchars($user['name'] ?? '', ENT_QUOTES, 'UTF-8'); ?></h1>
            <p class="text-muted mb-0">Firma: <?php echo htmlspecialchars($user['company_name'] ?? '', ENT_QUOTES, 'UTF-8'); ?></p>
        </div>
    </div>

    <div class="row row-cols-1 row-cols-md-2 row-cols-xl-4 g-3">
        <div class="col">
            <div class="card h-100">
                <div class="card-body">
                    <div class="text-muted small text-uppercase mb-1">Ürünler</div>
                    <div class="display-6 fw-semibold">—</div>
                    <p class="text-muted mb-0">Toplam ürün adedi placeholder.</p>
                </div>
            </div>
        </div>
        <div class="col">
            <div class="card h-100">
                <div class="card-body">
                    <div class="text-muted small text-uppercase mb-1">Cariler</div>
                    <div class="display-6 fw-semibold">—</div>
                    <p class="text-muted mb-0">Cari özeti placeholder.</p>
                </div>
            </div>
        </div>
        <div class="col">
            <div class="card h-100">
                <div class="card-body">
                    <div class="text-muted small text-uppercase mb-1">Teklifler</div>
                    <div class="display-6 fw-semibold">—</div>
                    <p class="text-muted mb-0">Teklif durumu placeholder.</p>
                </div>
            </div>
        </div>
        <div class="col">
            <div class="card h-100">
                <div class="card-body">
                    <div class="text-muted small text-uppercase mb-1">Satışlar</div>
                    <div class="display-6 fw-semibold">—</div>
                    <p class="text-muted mb-0">Satış özeti placeholder.</p>
                </div>
            </div>
        </div>
    </div>

    <?php if (!empty($user['is_super_admin'])): ?>
        <div class="card mt-4">
            <div class="card-body d-flex justify-content-between align-items-center gap-3 flex-wrap">
                <div>
                    <h3 class="h5 mb-1">Super Admin</h3>
                    <p class="text-muted mb-0">Super Admin olarak giriş yaptınız. Firma yönetimine gidebilirsiniz.</p>
                </div>
                <a href="/super-admin/companies" class="btn btn-primary">Firma Yönetimi</a>
            </div>
        </div>
    <?php else: ?>
        <div class="card mt-4">
            <div class="card-body">
                <?php if (can('users.create')): ?>
                    <a href="/users/create" class="btn btn-outline-primary">Kullanıcı oluştur</a>
                <?php else: ?>
                    <p class="text-muted mb-0">Kullanıcı oluşturma yetkiniz veya paket özelliğiniz yok.</p>
                <?php endif; ?>
            </div>
        </div>
    <?php endif; ?>
</div>

<?php
$content = ob_get_clean();
include __DIR__ . '/layout.php';
