<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <title>Yeni Ürün</title>
    <style>
        body { font-family: Arial, sans-serif; background: #f5f5f5; margin: 0; padding: 32px; }
        .card { max-width: 720px; margin: 0 auto; background: #fff; border-radius: 8px; padding: 20px; box-shadow: 0 2px 6px rgba(0,0,0,0.08); }
        label { display: block; margin-bottom: 6px; font-weight: bold; }
        input, select, textarea { width: 100%; padding: 10px; margin-bottom: 12px; border: 1px solid #ccc; border-radius: 4px; }
        button { padding: 12px 16px; background: #198754; color: #fff; border: none; border-radius: 6px; cursor: pointer; }
        .actions { display: flex; gap: 10px; align-items: center; }
        a { color: #0d6efd; text-decoration: none; }
        .flash { padding: 10px 14px; border-radius: 6px; margin-bottom: 12px; }
        .flash-success { background: #d1e7dd; color: #0f5132; }
        .flash-error { background: #f8d7da; color: #842029; }
        .muted { color: #6c757d; }
    </style>
</head>
<body>
<div class="card">
    <h2>Yeni Ürün Ekle</h2>

    <?php if (!empty($flash['success'])): ?>
        <div class="flash flash-success"><?php echo htmlspecialchars($flash['success'], ENT_QUOTES, 'UTF-8'); ?></div>
    <?php endif; ?>
    <?php if (!empty($flash['error'])): ?>
        <div class="flash flash-error"><?php echo htmlspecialchars($flash['error'], ENT_QUOTES, 'UTF-8'); ?></div>
    <?php endif; ?>

    <form method="POST" action="/products/store">
        <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars(csrf_token(), ENT_QUOTES, 'UTF-8'); ?>">

        <label for="code">Ürün Kodu</label>
        <input id="code" name="code" type="text" required>

        <label for="name">Ürün Adı</label>
        <input id="name" name="name" type="text" required>

        <label for="product_group_id">Ürün Grubu</label>
        <select id="product_group_id" name="product_group_id">
            <option value="">Seçiniz</option>
            <?php foreach ($groups as $group): ?>
                <?php if (($group['status'] ?? '') !== 'active') { continue; } ?>
                <option value="<?php echo htmlspecialchars($group['id'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
                    <?php echo htmlspecialchars($group['name'] ?? '', ENT_QUOTES, 'UTF-8'); ?>
                </option>
            <?php endforeach; ?>
        </select>
        <p class="muted" style="margin-top:-6px;">Listeye eklemek için aşağıya yeni grup adı yazabilirsiniz.</p>

        <label for="new_product_group">Yeni Grup Adı (opsiyonel)</label>
        <input id="new_product_group" name="new_product_group" type="text" placeholder="Örn: Elektronik">

        <label for="description">Açıklama</label>
        <textarea id="description" name="description" rows="3" placeholder="Kısa açıklama"></textarea>

        <label for="list_price">Liste Fiyatı</label>
        <input id="list_price" name="list_price" type="number" min="0" step="0.01" required>

        <label for="stock_quantity">Stok Miktarı</label>
        <input id="stock_quantity" name="stock_quantity" type="number" min="0" step="1" value="0">

        <div class="actions">
            <button type="submit">Kaydet</button>
            <a href="/products">Listeye Dön</a>
        </div>
    </form>
</div>
</body>
</html>
