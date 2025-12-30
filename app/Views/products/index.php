<?php
$title = 'Ürün Yönetimi';
ob_start();
?>
<div class="page-header">
    <div>
        <p class="eyebrow">Ürünler</p>
        <h1>Ürün Yönetimi</h1>
    </div>
    <?php if (canAccess('product', 'product.create')): ?>
        <a href="/products/create" class="button primary">Yeni Ürün</a>
    <?php endif; ?>
</div>

<?php if (!empty($flash['success'])): ?>
    <div class="alert success"><?php echo htmlspecialchars($flash['success'], ENT_QUOTES, 'UTF-8'); ?></div>
<?php endif; ?>
<?php if (!empty($flash['error'])): ?>
    <div class="alert error"><?php echo htmlspecialchars($flash['error'], ENT_QUOTES, 'UTF-8'); ?></div>
<?php endif; ?>

<div class="card-grid">
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Ürün Listesi</h3>
        </div>

        <?php if (empty($products)): ?>
            <p class="muted">Henüz ürün eklenmemiş.</p>
        <?php else: ?>
            <div class="table-wrapper">
                <table>
                    <thead>
                        <tr>
                            <th>Kod</th>
                            <th>Ad</th>
                            <th>Grup</th>
                            <th>Kategori</th>
                            <th>Para Birimi</th>
                            <th>Liste Fiyatı</th>
                            <th>Stok</th>
                            <th>Durum</th>
                            <th>Oluşturulma</th>
                            <?php if (canAccess('product', 'product.edit') || canAccess('product', 'product.deactivate')): ?>
                                <th>İşlemler</th>
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
                                <td><span class="badge <?php echo htmlspecialchars($product['status'] ?? 'active', ENT_QUOTES, 'UTF-8'); ?>"><?php echo htmlspecialchars($product['status'] ?? '', ENT_QUOTES, 'UTF-8'); ?></span></td>
                                <td><?php echo htmlspecialchars($product['created_at'] ?? '', ENT_QUOTES, 'UTF-8'); ?></td>
                                <?php if (canAccess('product', 'product.edit') || canAccess('product', 'product.deactivate')): ?>
                                    <td class="table-actions">
                                        <?php if (canAccess('product', 'product.edit')): ?>
                                            <a href="/products/<?php echo htmlspecialchars($product['id'] ?? '', ENT_QUOTES, 'UTF-8'); ?>/edit">Düzenle</a>
                                        <?php endif; ?>
                                        <?php if (canAccess('product', 'product.deactivate') && ($product['status'] ?? '') !== 'passive'): ?>
                                            <form method="POST" action="/products/<?php echo htmlspecialchars($product['id'] ?? '', ENT_QUOTES, 'UTF-8'); ?>/deactivate" class="form-inline" onsubmit="return confirm('Ürün pasife alınsın mı?');">
                                                <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars(csrf_token(), ENT_QUOTES, 'UTF-8'); ?>">
                                                <button type="submit" class="button danger">Pasif Et</button>
                                            </form>
                                        <?php endif; ?>
                                    </td>
                                <?php endif; ?>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>

    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Ürün Grupları</h3>
        </div>
        <p class="muted">Gruplar ürünleri kategorize eder. Yeni grup eklemek için ürün oluştururken “Yeni grup adı” alanını kullanabilirsiniz.</p>
        <?php if (empty($groups)): ?>
            <p class="muted">Henüz grup tanımlanmadı.</p>
        <?php else: ?>
            <ul class="group-list">
                <?php foreach ($groups as $group): ?>
                    <li>
                        <div style="display:flex; align-items:center; gap:8px;">
                            <strong><?php echo htmlspecialchars($group['name'] ?? '', ENT_QUOTES, 'UTF-8'); ?></strong>
                            <span class="badge <?php echo htmlspecialchars($group['status'] ?? 'active', ENT_QUOTES, 'UTF-8'); ?>"><?php echo htmlspecialchars($group['status'] ?? '', ENT_QUOTES, 'UTF-8'); ?></span>
                        </div>
                        <div class="muted">Oluşturulma: <?php echo htmlspecialchars($group['created_at'] ?? '', ENT_QUOTES, 'UTF-8'); ?></div>
                    </li>
                <?php endforeach; ?>
            </ul>
        <?php endif; ?>
    </div>
</div>

<?php
$content = ob_get_clean();
include __DIR__ . '/../layout.php';
