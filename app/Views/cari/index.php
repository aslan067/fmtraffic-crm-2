<?php
$title = 'Cari Yönetimi';
ob_start();
?>
<div class="page-hero mb-4">
    <div>
        <p class="eyebrow text-uppercase mb-1">Cariler</p>
        <h2 class="h4 mb-1">Cari Yönetimi</h2>
        <p class="text-muted mb-0">Carilerinizin iletişim ve finansal detaylarını güven veren bir SaaS akışıyla yönetin.</p>
    </div>
    <div class="d-flex gap-2 flex-wrap">
        <a href="/caris/create" class="btn btn-primary d-flex align-items-center gap-2">
            <i class="bi bi-plus-lg"></i>
            <span>Yeni Ekle</span>
        </a>
        <a href="/dashboard" class="btn btn-outline-secondary d-flex align-items-center gap-2">
            <i class="bi bi-graph-up"></i>
            <span>Özet</span>
        </a>
    </div>
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
            <div class="pill-icon"><i class="bi bi-people"></i></div>
            <div>
                <div class="text-uppercase small text-muted mb-0">Liste</div>
                <h3 class="h6 mb-0">Cari Listesi</h3>
            </div>
        </div>
        <span class="badge text-bg-light border">Finans ve iletişim özet</span>
    </div>
    <div class="card-body">
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
                            <th scope="col" class="text-end">İşlemler</th>
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
                                <td class="text-end">
                                    <div class="d-inline-flex gap-2 flex-wrap">
                                        <a href="/caris/<?php echo htmlspecialchars($cari['id'] ?? '', ENT_QUOTES, 'UTF-8'); ?>/edit" class="btn btn-sm btn-outline-primary">Düzenle</a>
                                        <?php if (($cari['status'] ?? '') !== 'passive'): ?>
                                            <form method="POST" action="/caris/<?php echo htmlspecialchars($cari['id'] ?? '', ENT_QUOTES, 'UTF-8'); ?>/deactivate" class="d-inline" onsubmit="return confirm('Cari pasife alınsın mı?');">
                                                <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars(csrf_token(), ENT_QUOTES, 'UTF-8'); ?>">
                                                <button type="submit" class="btn btn-sm btn-outline-danger">Pasif Et</button>
                                            </form>
                                        <?php endif; ?>
                                    </div>
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
