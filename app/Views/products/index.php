<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <title>Ürün Yönetimi</title>
    <style>
        body { font-family: Arial, sans-serif; background: #f5f5f5; margin: 0; padding: 32px; }
        .container { max-width: 1080px; margin: 0 auto; display: grid; grid-template-columns: 2fr 1fr; gap: 16px; }
        .card { background: #fff; border-radius: 8px; padding: 20px; box-shadow: 0 2px 6px rgba(0,0,0,0.08); }
        .header { display: flex; justify-content: space-between; align-items: center; }
        a.button { display: inline-block; padding: 10px 14px; background: #0d6efd; color: #fff; border-radius: 6px; text-decoration: none; }
        table { width: 100%; border-collapse: collapse; margin-top: 12px; }
        th, td { border: 1px solid #e5e5e5; padding: 10px; text-align: left; }
        th { background: #f1f3f5; }
        .badge { display: inline-block; padding: 4px 8px; border-radius: 12px; font-size: 12px; }
        .badge.active { background: #d1e7dd; color: #0f5132; }
        .badge.passive { background: #f8d7da; color: #842029; }
        .flash { padding: 10px 14px; border-radius: 6px; margin-bottom: 12px; }
        .flash-success { background: #d1e7dd; color: #0f5132; }
        .flash-error { background: #f8d7da; color: #842029; }
        .muted { color: #6c757d; }
        form.inline { display: inline; }
        button.danger { padding: 8px 12px; background: #dc3545; color: #fff; border: none; border-radius: 6px; cursor: pointer; }
        ul.group-list { list-style: none; padding-left: 0; margin: 0; }
        ul.group-list li { padding: 8px 0; border-bottom: 1px solid #f1f3f5; }
    </style>
</head>
<body>
<div class="container">
    <div class="card">
        <div class="header">
            <h2>Ürünler</h2>
            <a href="/products/create" class="button">Yeni Ürün</a>
        </div>

        <?php if (!empty($flash['success'])): ?>
            <div class="flash flash-success"><?php echo htmlspecialchars($flash['success'], ENT_QUOTES, 'UTF-8'); ?></div>
        <?php endif; ?>
        <?php if (!empty($flash['error'])): ?>
            <div class="flash flash-error"><?php echo htmlspecialchars($flash['error'], ENT_QUOTES, 'UTF-8'); ?></div>
        <?php endif; ?>

        <?php if (empty($products)): ?>
            <p class="muted">Henüz ürün eklenmemiş.</p>
        <?php else: ?>
            <table>
                <thead>
                    <tr>
                        <th>Kod</th>
                        <th>Ad</th>
                        <th>Grup</th>
                        <th>Liste Fiyatı</th>
                        <th>Stok</th>
                        <th>Durum</th>
                        <th>Oluşturulma</th>
                        <th>İşlemler</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($products as $product): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($product['code'] ?? '', ENT_QUOTES, 'UTF-8'); ?></td>
                            <td><?php echo htmlspecialchars($product['name'] ?? '', ENT_QUOTES, 'UTF-8'); ?></td>
                            <td><?php echo htmlspecialchars($product['group_name'] ?? '-', ENT_QUOTES, 'UTF-8'); ?></td>
                            <td><?php echo number_format((float) ($product['list_price'] ?? 0), 2, ',', '.'); ?></td>
                            <td><?php echo htmlspecialchars((string) ($product['stock_quantity'] ?? 0), ENT_QUOTES, 'UTF-8'); ?></td>
                            <td><span class="badge <?php echo htmlspecialchars($product['status'] ?? 'active', ENT_QUOTES, 'UTF-8'); ?>"><?php echo htmlspecialchars($product['status'] ?? '', ENT_QUOTES, 'UTF-8'); ?></span></td>
                            <td><?php echo htmlspecialchars($product['created_at'] ?? '', ENT_QUOTES, 'UTF-8'); ?></td>
                            <td>
                                <a href="/products/<?php echo htmlspecialchars($product['id'] ?? '', ENT_QUOTES, 'UTF-8'); ?>/edit">Düzenle</a>
                                <?php if (($product['status'] ?? '') !== 'passive'): ?>
                                    <form method="POST" action="/products/<?php echo htmlspecialchars($product['id'] ?? '', ENT_QUOTES, 'UTF-8'); ?>/deactivate" class="inline" onsubmit="return confirm('Ürün pasife alınsın mı?');">
                                        <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars(csrf_token(), ENT_QUOTES, 'UTF-8'); ?>">
                                        <button type="submit" class="danger">Pasif Et</button>
                                    </form>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>

    <div class="card">
        <h3>Ürün Grupları</h3>
        <p class="muted">Gruplar ürünleri kategorize eder. Yeni grup eklemek için ürün oluştururken “Yeni grup adı” alanını kullanabilirsiniz.</p>
        <?php if (empty($groups)): ?>
            <p class="muted">Henüz grup tanımlanmadı.</p>
        <?php else: ?>
            <ul class="group-list">
                <?php foreach ($groups as $group): ?>
                    <li>
                        <strong><?php echo htmlspecialchars($group['name'] ?? '', ENT_QUOTES, 'UTF-8'); ?></strong>
                        <span class="badge <?php echo htmlspecialchars($group['status'] ?? 'active', ENT_QUOTES, 'UTF-8'); ?>" style="margin-left:6px;"><?php echo htmlspecialchars($group['status'] ?? '', ENT_QUOTES, 'UTF-8'); ?></span><br>
                        <span class="muted">Oluşturulma: <?php echo htmlspecialchars($group['created_at'] ?? '', ENT_QUOTES, 'UTF-8'); ?></span>
                    </li>
                <?php endforeach; ?>
            </ul>
        <?php endif; ?>
    </div>
</div>
</body>
</html>
