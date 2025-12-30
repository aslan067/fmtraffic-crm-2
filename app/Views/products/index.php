<?php
$title = 'Ürün Yönetimi';
ob_start();
?>
<div class="page-hero mb-4">
    <div>
        <p class="eyebrow text-uppercase mb-1">Ürünler</p>
        <h2 class="h4 mb-1">Ürün Yönetimi</h2>
        <p class="text-muted mb-0">Ürünleri yönetin, fiyatları ve stokları izleyin.</p>
    </div>
    <?php if (canAccess('product', 'product.create')): ?>
        <a href="/products/create" class="btn btn-primary d-flex align-items-center gap-2">
            <i class="bi bi-plus-lg"></i>
            <span>Yeni Ekle</span>
        </a>
    <?php endif; ?>
</div>

<?php if (!empty($flash['success'])): ?>
    <div class="alert alert-success"><?php echo htmlspecialchars($flash['success'], ENT_QUOTES, 'UTF-8'); ?></div>
<?php endif; ?>
<?php if (!empty($flash['error'])): ?>
    <div class="alert alert-danger"><?php echo htmlspecialchars($flash['error'], ENT_QUOTES, 'UTF-8'); ?></div>
<?php endif; ?>

<div class="row g-4">
    <div class="col-12 col-xl-8">
        <div class="card h-100">
            <div class="card-header bg-white d-flex align-items-center justify-content-between">
                <div class="d-flex align-items-center gap-2">
                    <div class="pill-icon bg-primary-subtle text-primary"><i class="bi bi-box-seam"></i></div>
                    <div>
                        <div class="text-uppercase small text-muted mb-0">Liste</div>
                        <h3 class="h6 mb-0">Ürün Listesi</h3>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <?php if (empty($products)): ?>
                    <p class="text-muted mb-0">Henüz ürün eklenmemiş.</p>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-striped table-hover align-middle mb-0">
                            <thead>
                                <tr>
                                    <th scope="col">Kod</th>
                                    <th scope="col">Ad</th>
                                    <th scope="col">Grup</th>
                                    <th scope="col">Kategori</th>
                                    <th scope="col">Para Birimi</th>
                                    <th scope="col">Liste Fiyatı</th>
                                    <th scope="col">Stok</th>
                                    <th scope="col">Durum</th>
                                    <th scope="col">Oluşturulma</th>
                                    <?php if (canAccess('product', 'product.edit') || canAccess('product', 'product.deactivate')): ?>
                                        <th scope="col" class="text-end">İşlemler</th>
                                    <?php endif; ?>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($products as $product): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($product['code'] ?? '', ENT_QUOTES, 'UTF-8'); ?></td>
                                        <td><?php echo htmlspecialchars($product['name'] ?? '', ENT_QUOTES, 'UTF-8'); ?></td>
                                        <td><?php echo htmlspecialchars($product['group_name'] ?? '-', ENT_QUOTES, 'UTF-8'); ?></td>
                                        <td><?php echo htmlspecialchars($product['category'] ?? '-', ENT_QUOTES, 'UTF-8'); ?></td>
                                        <td><?php echo htmlspecialchars($product['currency'] ?? 'TRY', ENT_QUOTES, 'UTF-8'); ?></td>
                                        <td><?php echo number_format((float) ($product['list_price'] ?? 0), 2, ',', '.'); ?></td>
                                        <td><?php echo htmlspecialchars((string) ($product['stock_quantity'] ?? 0), ENT_QUOTES, 'UTF-8'); ?></td>
                                        <td><span class="badge text-bg-light"><?php echo htmlspecialchars($product['status'] ?? '', ENT_QUOTES, 'UTF-8'); ?></span></td>
                                        <td><?php echo htmlspecialchars($product['created_at'] ?? '', ENT_QUOTES, 'UTF-8'); ?></td>
                                        <?php if (canAccess('product', 'product.edit') || canAccess('product', 'product.deactivate')): ?>
                                            <td class="text-end">
                                                <div class="d-inline-flex gap-2 flex-wrap">
                                                    <?php if (canAccess('product', 'product.edit')): ?>
                                                        <a href="/products/<?php echo htmlspecialchars($product['id'] ?? '', ENT_QUOTES, 'UTF-8'); ?>/edit" class="btn btn-sm btn-outline-primary">Düzenle</a>
                                                    <?php endif; ?>
                                                    <?php if (canAccess('product', 'product.deactivate') && ($product['status'] ?? '') !== 'passive'): ?>
                                                        <form method="POST" action="/products/<?php echo htmlspecialchars($product['id'] ?? '', ENT_QUOTES, 'UTF-8'); ?>/deactivate" class="d-inline" onsubmit="return confirm('Ürün pasife alınsın mı?');">
                                                            <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars(csrf_token(), ENT_QUOTES, 'UTF-8'); ?>">
                                                            <button type="submit" class="btn btn-sm btn-outline-danger">Pasif Et</button>
                                                        </form>
                                                    <?php endif; ?>
                                                </div>
                                            </td>
                                        <?php endif; ?>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <div class="col-12 col-xl-4">
        <div class="card h-100">
            <div class="card-header bg-white d-flex align-items-center gap-2">
                <div class="pill-icon bg-secondary-subtle text-secondary"><i class="bi bi-collection"></i></div>
                <div>
                    <div class="text-uppercase small text-muted mb-0">Gruplar</div>
                    <h3 class="h6 mb-0">Ürün Grupları</h3>
                </div>
            </div>
            <div class="card-body">
                <p class="text-muted small">Gruplar ürünleri kategorize eder. Yeni grup eklemek için ürün oluştururken “Yeni grup adı” alanını kullanabilirsiniz.</p>
                <?php if (empty($groups)): ?>
                    <p class="text-muted mb-0">Henüz grup tanımlanmadı.</p>
                <?php else: ?>
                    <div class="list-group">
                        <?php foreach ($groups as $group): ?>
                            <div class="list-group-item d-flex justify-content-between align-items-start">
                                <div>
                                    <div class="fw-semibold"><?php echo htmlspecialchars($group['name'] ?? '', ENT_QUOTES, 'UTF-8'); ?></div>
                                    <div class="text-muted small">Oluşturulma: <?php echo htmlspecialchars($group['created_at'] ?? '', ENT_QUOTES, 'UTF-8'); ?></div>
                                </div>
                                <span class="badge text-bg-light"><?php echo htmlspecialchars($group['status'] ?? '', ENT_QUOTES, 'UTF-8'); ?></span>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?php
$content = ob_get_clean();
include __DIR__ . '/../layout.php';
