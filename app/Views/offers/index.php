<?php
$title = 'Teklifler';
ob_start();
?>
<div class="page-hero mb-4">
    <div>
        <p class="eyebrow text-uppercase mb-1">Teklifler</p>
        <h2 class="h4 mb-1">Teklif Akışı</h2>
        <p class="text-muted mb-0">Teklif akışını yönetin, durum ve tutarları operasyonel görünümle izleyin.</p>
    </div>
    <?php if (canAccess('offer', 'offer.create')): ?>
        <div class="d-flex gap-2 flex-wrap">
            <a href="/offers/create" class="btn btn-primary d-flex align-items-center gap-2">
                <i class="bi bi-plus-lg"></i>
                <span>Yeni Ekle</span>
            </a>
            <a href="/dashboard" class="btn btn-outline-secondary d-flex align-items-center gap-2">
                <i class="bi bi-graph-up"></i>
                <span>Özet</span>
            </a>
        </div>
    <?php endif; ?>
</div>

<?php if (!empty($flash['success'])): ?>
    <div class="alert alert-success"><?php echo htmlspecialchars($flash['success'], ENT_QUOTES, 'UTF-8'); ?></div>
<?php endif; ?>
<?php if (!empty($flash['error'])): ?>
    <div class="alert alert-danger"><?php echo htmlspecialchars($flash['error'], ENT_QUOTES, 'UTF-8'); ?></div>
<?php endif; ?>

<div class="card">
    <div class="card-header bg-white d-flex align-items-center justify-content-between">
        <div class="d-flex align-items-center gap-2">
            <div class="pill-icon"><i class="bi bi-file-earmark-text"></i></div>
            <div>
                <div class="text-uppercase small text-muted mb-0">Liste</div>
                <h3 class="h6 mb-0">Teklif Listesi</h3>
            </div>
        </div>
        <span class="badge text-bg-light border">Durum & toplam takibi</span>
    </div>
    <div class="card-body">
        <?php if (empty($offers)): ?>
            <p class="text-muted mb-0">Henüz teklif eklenmemiş.</p>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
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
                            <th scope="col">Oluşturan</th>
                            <th scope="col">Oluşturulma</th>
                            <th scope="col" class="text-end">İşlemler</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($offers as $offer): ?>
                            <tr>
                                <td><?php echo htmlspecialchars((string) ($offer['id'] ?? ''), ENT_QUOTES, 'UTF-8'); ?></td>
                                <td><?php echo htmlspecialchars($offer['cari_name'] ?? '-', ENT_QUOTES, 'UTF-8'); ?></td>
                                <?php if (!empty($isSuperAdmin)): ?>
                                    <td><?php echo htmlspecialchars($offer['company_name'] ?? '-', ENT_QUOTES, 'UTF-8'); ?></td>
                                <?php endif; ?>
                                <td><?php echo htmlspecialchars($offer['currency'] ?? 'TRY', ENT_QUOTES, 'UTF-8'); ?></td>
                                <td><?php echo number_format((float) ($offer['total_amount'] ?? 0), 2, ',', '.'); ?></td>
                                <td><span class="badge text-bg-light"><?php echo htmlspecialchars($offer['status'] ?? 'draft', ENT_QUOTES, 'UTF-8'); ?></span></td>
                                <td><?php echo htmlspecialchars($offer['created_by_name'] ?? '-', ENT_QUOTES, 'UTF-8'); ?></td>
                                <td><?php echo htmlspecialchars($offer['created_at'] ?? '', ENT_QUOTES, 'UTF-8'); ?></td>
                                <td class="text-end"><a href="/offers/<?php echo htmlspecialchars((string) ($offer['id'] ?? ''), ENT_QUOTES, 'UTF-8'); ?>" class="btn btn-sm btn-outline-primary">Görüntüle</a></td>
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
