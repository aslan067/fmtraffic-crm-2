<?php
$title = 'Satışlar';
ob_start();
?>
<div class="page-header">
    <div>
        <p class="eyebrow">Satışlar</p>
        <h1>Satış Yönetimi</h1>
    </div>
</div>

<?php if (!empty($flash['success'])): ?>
    <div class="alert success"><?php echo htmlspecialchars($flash['success'], ENT_QUOTES, 'UTF-8'); ?></div>
<?php endif; ?>
<?php if (!empty($flash['error'])): ?>
    <div class="alert error"><?php echo htmlspecialchars($flash['error'], ENT_QUOTES, 'UTF-8'); ?></div>
<?php endif; ?>

<div class="card">
    <div class="card-header">
        <h3 class="card-title">Satış Listesi</h3>
    </div>

    <?php if (empty($sales)): ?>
        <p class="muted">Henüz satış eklenmemiş.</p>
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
                        <th>Oluşturulma</th>
                        <th>İşlemler</th>
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
                            <td><span class="badge <?php echo htmlspecialchars($sale['status'] ?? 'active', ENT_QUOTES, 'UTF-8'); ?>"><?php echo htmlspecialchars($sale['status'] ?? 'active', ENT_QUOTES, 'UTF-8'); ?></span></td>
                            <td><?php echo htmlspecialchars($sale['created_at'] ?? '', ENT_QUOTES, 'UTF-8'); ?></td>
                            <td><a href="/sales/<?php echo htmlspecialchars((string) ($sale['id'] ?? ''), ENT_QUOTES, 'UTF-8'); ?>">Görüntüle</a></td>
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
