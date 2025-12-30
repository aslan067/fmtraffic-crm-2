<?php
$title = 'Cari Yönetimi';
ob_start();
?>
<div class="page-header">
    <div>
        <p class="eyebrow">Cariler</p>
        <h1>Cari Yönetimi</h1>
    </div>
    <a href="/caris/create" class="button primary">Yeni Cari</a>
</div>

<?php if (!empty($flash['success'])): ?>
    <div class="alert success"><?php echo htmlspecialchars($flash['success'], ENT_QUOTES, 'UTF-8'); ?></div>
<?php endif; ?>
<?php if (!empty($flash['error'])): ?>
    <div class="alert error"><?php echo htmlspecialchars($flash['error'], ENT_QUOTES, 'UTF-8'); ?></div>
<?php endif; ?>

<div class="card">
    <div class="card-header">
        <h3 class="card-title">Cari Listesi</h3>
    </div>

    <?php if (empty($caris)): ?>
        <p class="muted">Henüz cari eklenmemiş.</p>
    <?php else: ?>
        <div class="table-wrapper">
            <table>
                <thead>
                    <tr>
                        <th>Ad</th>
                        <th>Tip</th>
                        <th>Telefon</th>
                        <th>E-posta</th>
                        <th>Durum</th>
                        <th>Oluşturulma</th>
                        <th>İşlemler</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($caris as $cari): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($cari['name'] ?? '', ENT_QUOTES, 'UTF-8'); ?></td>
                            <td><?php echo htmlspecialchars($cari['cari_type'] ?? '', ENT_QUOTES, 'UTF-8'); ?></td>
                            <td><?php echo htmlspecialchars($cari['phone'] ?? '-', ENT_QUOTES, 'UTF-8'); ?></td>
                            <td><?php echo htmlspecialchars($cari['email'] ?? '-', ENT_QUOTES, 'UTF-8'); ?></td>
                            <td><span class="badge <?php echo htmlspecialchars($cari['status'] ?? 'active', ENT_QUOTES, 'UTF-8'); ?>"><?php echo htmlspecialchars($cari['status'] ?? '', ENT_QUOTES, 'UTF-8'); ?></span></td>
                            <td><?php echo htmlspecialchars($cari['created_at'] ?? '', ENT_QUOTES, 'UTF-8'); ?></td>
                            <td class="table-actions">
                                <a href="/caris/<?php echo htmlspecialchars($cari['id'] ?? '', ENT_QUOTES, 'UTF-8'); ?>/edit">Düzenle</a>
                                <?php if (($cari['status'] ?? '') !== 'passive'): ?>
                                    <form method="POST" action="/caris/<?php echo htmlspecialchars($cari['id'] ?? '', ENT_QUOTES, 'UTF-8'); ?>/deactivate" class="form-inline" onsubmit="return confirm('Cari pasife alınsın mı?');">
                                        <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars(csrf_token(), ENT_QUOTES, 'UTF-8'); ?>">
                                        <button type="submit" class="button danger">Pasif Et</button>
                                    </form>
                                <?php endif; ?>
                            </td>
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
