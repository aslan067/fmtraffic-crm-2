<?php
$title = 'Teklifler';
ob_start();
?>
<div class="page-header">
    <div>
        <p class="eyebrow">Teklifler</p>
        <h1>Teklif Yönetimi</h1>
    </div>
    <?php if (canAccess('offer', 'offer.create')): ?>
        <a href="/offers/create" class="button primary">Yeni Teklif</a>
    <?php endif; ?>
</div>

<?php if (!empty($flash['success'])): ?>
    <div class="alert success"><?php echo htmlspecialchars($flash['success'], ENT_QUOTES, 'UTF-8'); ?></div>
<?php endif; ?>
<?php if (!empty($flash['error'])): ?>
    <div class="alert error"><?php echo htmlspecialchars($flash['error'], ENT_QUOTES, 'UTF-8'); ?></div>
<?php endif; ?>

<div class="card">
    <div class="card-header">
        <h3 class="card-title">Teklif Listesi</h3>
    </div>

    <?php if (empty($offers)): ?>
        <p class="muted">Henüz teklif eklenmemiş.</p>
    <?php else: ?>
        <div class="table-wrapper">
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Cari</th>
                        <?php if (!empty($isSuperAdmin)): ?>
                            <th>Firma</th>
                        <?php endif; ?>
                        <th>Para Birimi</th>
                        <th>Toplam</th>
                        <th>Durum</th>
                        <th>Oluşturan</th>
                        <th>Oluşturulma</th>
                        <th>İşlemler</th>
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
                            <td><span class="badge <?php echo htmlspecialchars($offer['status'] ?? 'draft', ENT_QUOTES, 'UTF-8'); ?>"><?php echo htmlspecialchars($offer['status'] ?? 'draft', ENT_QUOTES, 'UTF-8'); ?></span></td>
                            <td><?php echo htmlspecialchars($offer['created_by_name'] ?? '-', ENT_QUOTES, 'UTF-8'); ?></td>
                            <td><?php echo htmlspecialchars($offer['created_at'] ?? '', ENT_QUOTES, 'UTF-8'); ?></td>
                            <td><a href="/offers/<?php echo htmlspecialchars((string) ($offer['id'] ?? ''), ENT_QUOTES, 'UTF-8'); ?>">Görüntüle</a></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</div>

<?php
$content = ob_get_clean();
include __DIR__ . '/../layout.php';
