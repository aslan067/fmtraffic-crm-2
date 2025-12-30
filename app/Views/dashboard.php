<?php
$title = 'Dashboard';
ob_start();
?>
<div class="page-header">
    <div>
        <p class="eyebrow">Kontrol Paneli</p>
        <h1>Hoş geldiniz, <?php echo htmlspecialchars($user['name'] ?? '', ENT_QUOTES, 'UTF-8'); ?></h1>
        <p class="muted">Firma: <?php echo htmlspecialchars($user['company_name'] ?? '', ENT_QUOTES, 'UTF-8'); ?></p>
    </div>
</div>

<div class="card-grid">
    <div class="card stat-card">
        <div class="stat-title">Ürünler</div>
        <div class="stat-value">—</div>
        <p class="muted">Toplam ürün adedi placeholder.</p>
    </div>
    <div class="card stat-card">
        <div class="stat-title">Cariler</div>
        <div class="stat-value">—</div>
        <p class="muted">Cari özeti placeholder.</p>
    </div>
    <div class="card stat-card">
        <div class="stat-title">Teklifler</div>
        <div class="stat-value">—</div>
        <p class="muted">Teklif durumu placeholder.</p>
    </div>
    <div class="card stat-card">
        <div class="stat-title">Satışlar</div>
        <div class="stat-value">—</div>
        <p class="muted">Satış özeti placeholder.</p>
    </div>
</div>

<?php if (!empty($user['is_super_admin'])): ?>
    <div class="card" style="margin-top: 18px;">
        <div class="card-header">
            <h3 class="card-title">Super Admin</h3>
            <a href="/super-admin/companies" class="button primary">Firma Yönetimi</a>
        </div>
        <p class="muted">Super Admin olarak giriş yaptınız. Firma yönetimine gidebilirsiniz.</p>
    </div>
<?php else: ?>
    <div class="card" style="margin-top: 18px;">
        <?php if (can('users.create')): ?>
            <p><a href="/users/create" class="button">Kullanıcı oluştur</a></p>
        <?php else: ?>
            <p class="muted">Kullanıcı oluşturma yetkiniz veya paket özelliğiniz yok.</p>
        <?php endif; ?>
    </div>
<?php endif; ?>

<?php
$content = ob_get_clean();
include __DIR__ . '/layout.php';
