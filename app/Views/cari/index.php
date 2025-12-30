<?php
$title = 'Cari Yönetimi';
ob_start();
?>
<div class="container-fluid g-3">
    <div class="d-flex justify-content-between align-items-start flex-wrap mb-3">
        <div>
            <p class="text-uppercase text-muted small mb-1">Cariler</p>
            <h1 class="h4 mb-0">Cari Yönetimi</h1>
        </div>
        <a href="/caris/create" class="btn btn-primary">Yeni Cari</a>
    </div>

    <?php if (!empty($flash['success'])): ?>
        <div class="alert alert-success"><?php echo htmlspecialchars($flash['success'], ENT_QUOTES, 'UTF-8'); ?></div>
    <?php endif; ?>
    <?php if (!empty($flash['error'])): ?>
        <div class="alert alert-danger"><?php echo htmlspecialchars($flash['error'], ENT_QUOTES, 'UTF-8'); ?></div>
    <?php endif; ?>

    <div class="mb-3">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h3 class="h5 mb-0">Cari Listesi</h3>
        </div>

        <?php if (empty($caris)): ?>
            <p class="text-muted mb-0">Henüz cari eklenmemiş.</p>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead>
                        <tr>
                            <th scope="col">Ad</th>
                            <th scope="col">Tip</th>
                            <th scope="col">Telefon</th>
                            <th scope="col">E-posta</th>
                            <th scope="col">Durum</th>
                            <th scope="col">Oluşturulma</th>
                            <th scope="col">İşlemler</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($caris as $cari): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($cari['name'] ?? '', ENT_QUOTES, 'UTF-8'); ?></td>
                                <td><?php echo htmlspecialchars($cari['cari_type'] ?? '', ENT_QUOTES, 'UTF-8'); ?></td>
                                <td><?php echo htmlspecialchars($cari['phone'] ?? '-', ENT_QUOTES, 'UTF-8'); ?></td>
                                <td><?php echo htmlspecialchars($cari['email'] ?? '-', ENT_QUOTES, 'UTF-8'); ?></td>
                                <td><span class="badge text-bg-light"><?php echo htmlspecialchars($cari['status'] ?? 'active', ENT_QUOTES, 'UTF-8'); ?></span></td>
                                <td><?php echo htmlspecialchars($cari['created_at'] ?? '', ENT_QUOTES, 'UTF-8'); ?></td>
                                <td class="d-flex gap-2 flex-wrap">
                                    <a href="/caris/<?php echo htmlspecialchars($cari['id'] ?? '', ENT_QUOTES, 'UTF-8'); ?>/edit" class="btn btn-sm btn-outline-primary">Düzenle</a>
                                    <?php if (($cari['status'] ?? '') !== 'passive'): ?>
                                        <form method="POST" action="/caris/<?php echo htmlspecialchars($cari['id'] ?? '', ENT_QUOTES, 'UTF-8'); ?>/deactivate" class="d-inline" onsubmit="return confirm('Cari pasife alınsın mı?');">
                                            <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars(csrf_token(), ENT_QUOTES, 'UTF-8'); ?>">
                                            <button type="submit" class="btn btn-sm btn-outline-danger">Pasif Et</button>
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
</div>

<?php
$content = ob_get_clean();
include __DIR__ . '/../layout.php';
