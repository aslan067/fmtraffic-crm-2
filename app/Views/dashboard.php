<?php
$title = 'Dashboard';
ob_start();
?>
<div class="row g-4">
    <div class="col-12 col-sm-6 col-xl-3">
        <div class="card h-100">
            <div class="card-body">
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <div class="icon-circle bg-primary-subtle text-primary d-flex align-items-center justify-content-center">
                        <i class="bi bi-box-seam"></i>
                    </div>
                    <span class="badge text-bg-light">Özet</span>
                </div>
                <h5 class="card-title mb-1">Ürünler</h5>
                <p class="text-muted small mb-0">Ürün stokları ve katalog durumu placeholder.</p>
            </div>
        </div>
    </div>
    <div class="col-12 col-sm-6 col-xl-3">
        <div class="card h-100">
            <div class="card-body">
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <div class="icon-circle bg-success-subtle text-success d-flex align-items-center justify-content-center">
                        <i class="bi bi-people"></i>
                    </div>
                    <span class="badge text-bg-light">Özet</span>
                </div>
                <h5 class="card-title mb-1">Cariler</h5>
                <p class="text-muted small mb-0">Cari ilişkileri ve iletişim kayıtları placeholder.</p>
            </div>
        </div>
    </div>
    <div class="col-12 col-sm-6 col-xl-3">
        <div class="card h-100">
            <div class="card-body">
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <div class="icon-circle bg-warning-subtle text-warning d-flex align-items-center justify-content-center">
                        <i class="bi bi-file-earmark-text"></i>
                    </div>
                    <span class="badge text-bg-light">Özet</span>
                </div>
                <h5 class="card-title mb-1">Teklifler</h5>
                <p class="text-muted small mb-0">Aktif teklif akışı ve taslaklar placeholder.</p>
            </div>
        </div>
    </div>
    <div class="col-12 col-sm-6 col-xl-3">
        <div class="card h-100">
            <div class="card-body">
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <div class="icon-circle bg-danger-subtle text-danger d-flex align-items-center justify-content-center">
                        <i class="bi bi-cash-coin"></i>
                    </div>
                    <span class="badge text-bg-light">Özet</span>
                </div>
                <h5 class="card-title mb-1">Satışlar</h5>
                <p class="text-muted small mb-0">Satış performansı ve tahsilat akışı placeholder.</p>
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
        <div class="card-body d-flex justify-content-between align-items-center gap-3 flex-wrap">
            <div>
                <h3 class="h6 mb-1">Kullanıcı Yönetimi</h3>
                <p class="text-muted mb-0">Yetkiniz dahilinde yeni kullanıcı ekleyebilirsiniz.</p>
            </div>
            <?php if (can('users.create')): ?>
                <a href="/users/create" class="btn btn-outline-primary">Kullanıcı oluştur</a>
            <?php else: ?>
                <span class="text-muted small">Kullanıcı oluşturma yetkiniz veya paket özelliğiniz yok.</span>
            <?php endif; ?>
        </div>
    </div>
<?php endif; ?>

<?php
$content = ob_get_clean();
include __DIR__ . '/layout.php';
