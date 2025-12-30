<?php
$title = 'Teklifler';
ob_start();
?>
<div class="container-fluid g-3">
    <div class="d-flex justify-content-between align-items-start flex-wrap mb-3">
        <div>
            <p class="text-uppercase text-muted small mb-1">Teklifler</p>
            <h1 class="h4 mb-0">Teklif Yönetimi</h1>
        </div>
        <?php if (canAccess('offer', 'offer.create')): ?>
            <a href="/offers/create" class="btn btn-primary">Yeni Teklif</a>
        <?php endif; ?>
    </div>

    <?php if (!empty($flash['success'])): ?>
        <div class="alert alert-success"><?php echo htmlspecialchars($flash['success'], ENT_QUOTES, 'UTF-8'); ?></div>
    <?php endif; ?>
    <?php if (!empty($flash['error'])): ?>
        <div class="alert alert-danger"><?php echo htmlspecialchars($flash['error'], ENT_QUOTES, 'UTF-8'); ?></div>
    <?php endif; ?>

    <div class="mb-3">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h3 class="h5 mb-0">Teklif Listesi</h3>
        </div>

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
                            <th scope="col">İşlemler</th>
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
                                <td><a href="/offers/<?php echo htmlspecialchars((string) ($offer['id'] ?? ''), ENT_QUOTES, 'UTF-8'); ?>" class="btn btn-sm btn-outline-primary">Görüntüle</a></td>
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
