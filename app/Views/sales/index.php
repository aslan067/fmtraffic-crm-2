<?php
$title = 'Satışlar';
ob_start();
?>
<div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-4">
    <div>
        <p class="text-muted small mb-1">Satışlarınıza ait onaylı işlemleri ve tahsilatları takip edin.</p>
        <h2 class="h5 mb-0">Satışlar</h2>
    </div>
    <a href="/offers" class="btn btn-primary d-flex align-items-center gap-2" title="Onaylı teklifleri satışa dönüştürün">
        <i class="bi bi-plus-lg"></i>
        <span>Yeni Ekle</span>
    </a>
</div>

<?php if (!empty($flash['success'])): ?>
    <div class="alert alert-success"><?php echo htmlspecialchars($flash['success'], ENT_QUOTES, 'UTF-8'); ?></div>
<?php endif; ?>
<?php if (!empty($flash['error'])): ?>
    <div class="alert alert-danger"><?php echo htmlspecialchars($flash['error'], ENT_QUOTES, 'UTF-8'); ?></div>
<?php endif; ?>

<div class="card">
    <div class="card-header bg-white d-flex justify-content-between align-items-center">
        <h3 class="h6 mb-0">Satış Listesi</h3>
    </div>
    <div class="card-body">
        <?php if (empty($sales)): ?>
            <p class="text-muted mb-0">Henüz satış eklenmemiş.</p>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table table-striped table-hover align-middle mb-0">
                    <thead>
                        <tr>
                            <th scope="col">ID</th>
                            <th scope="col">Cari</th>
                            <?php if (!empty($isSuperAdmin)): ?>
                                <th scope="col">Firma</th>
                            <?php endif; ?>
                            <th scope="col">Para Birimi</th>
                            <th scope="col">Toplam</th>
                            <th scope="col">Durum</th>
                            <th scope="col">Oluşturulma</th>
                            <th scope="col" class="text-end">İşlemler</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($sales as $sale): ?>
                            <tr>
                                <td><?php echo htmlspecialchars((string) ($sale['id'] ?? ''), ENT_QUOTES, 'UTF-8'); ?></td>
                                <td><?php echo htmlspecialchars($sale['cari_name'] ?? '-', ENT_QUOTES, 'UTF-8'); ?></td>
                                <?php if (!empty($isSuperAdmin)): ?>
                                    <td><?php echo htmlspecialchars($sale['company_name'] ?? '-', ENT_QUOTES, 'UTF-8'); ?></td>
                                <?php endif; ?>
                                <td><?php echo htmlspecialchars($sale['currency'] ?? 'TRY', ENT_QUOTES, 'UTF-8'); ?></td>
                                <td><?php echo number_format((float) ($sale['total_amount'] ?? 0), 2, ',', '.'); ?></td>
                                <td><span class="badge text-bg-light"><?php echo htmlspecialchars($sale['status'] ?? 'active', ENT_QUOTES, 'UTF-8'); ?></span></td>
                                <td><?php echo htmlspecialchars($sale['created_at'] ?? '', ENT_QUOTES, 'UTF-8'); ?></td>
                                <td class="text-end"><a href="/sales/<?php echo htmlspecialchars((string) ($sale['id'] ?? ''), ENT_QUOTES, 'UTF-8'); ?>" class="btn btn-sm btn-outline-primary">Görüntüle</a></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php
$content = ob_get_clean();
include __DIR__ . '/../layout.php';
