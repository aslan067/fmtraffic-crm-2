<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <title>Teklif Detayı</title>
    <style>
        body { font-family: Arial, sans-serif; background: #f5f5f5; margin: 0; padding: 32px; }
        .container { max-width: 1000px; margin: 0 auto; }
        .card { background: #fff; border-radius: 8px; padding: 20px; box-shadow: 0 2px 6px rgba(0,0,0,0.08); margin-bottom: 16px; }
        .badge { display: inline-block; padding: 4px 8px; border-radius: 12px; font-size: 12px; }
        .badge.draft { background: #e2e3e5; color: #383d41; }
        .badge.sent { background: #cff4fc; color: #055160; }
        .badge.approved { background: #d1e7dd; color: #0f5132; }
        .badge.rejected { background: #f8d7da; color: #842029; }
        table { width: 100%; border-collapse: collapse; margin-top: 12px; }
        th, td { border: 1px solid #e5e5e5; padding: 8px; text-align: left; }
        th { background: #f1f3f5; }
        .flash { padding: 10px 14px; border-radius: 6px; margin-bottom: 12px; }
        .flash-success { background: #d1e7dd; color: #0f5132; }
        .flash-error { background: #f8d7da; color: #842029; }
        .muted { color: #6c757d; }
        button { padding: 10px 14px; border: none; border-radius: 6px; cursor: pointer; background: #0d6efd; color: #fff; }
        select { padding: 8px; border-radius: 6px; border: 1px solid #ced4da; }
    </style>
</head>
<body>
<div class="container">
    <div class="card">
        <a href="/offers" style="text-decoration:none;">← Teklif listesine dön</a>
    </div>

    <div class="card">
        <h2>Teklif #<?php echo htmlspecialchars((string) ($offer['id'] ?? ''), ENT_QUOTES, 'UTF-8'); ?></h2>

        <?php if (!empty($flash['success'])): ?>
            <div class="flash flash-success"><?php echo htmlspecialchars($flash['success'], ENT_QUOTES, 'UTF-8'); ?></div>
        <?php endif; ?>
        <?php if (!empty($flash['error'])): ?>
            <div class="flash flash-error"><?php echo htmlspecialchars($flash['error'], ENT_QUOTES, 'UTF-8'); ?></div>
        <?php endif; ?>

        <p><strong>Cari:</strong> <?php echo htmlspecialchars($offer['cari_name'] ?? '-', ENT_QUOTES, 'UTF-8'); ?></p>
        <p><strong>Para Birimi:</strong> <?php echo htmlspecialchars($offer['currency'] ?? 'TRY', ENT_QUOTES, 'UTF-8'); ?></p>
        <p><strong>Toplam:</strong> <?php echo number_format((float) ($offer['total_amount'] ?? 0), 2, ',', '.'); ?></p>
        <p><strong>Durum:</strong> <span class="badge <?php echo htmlspecialchars($offer['status'] ?? 'draft', ENT_QUOTES, 'UTF-8'); ?>"><?php echo htmlspecialchars($offer['status'] ?? 'draft', ENT_QUOTES, 'UTF-8'); ?></span></p>
        <p class="muted">Oluşturan: <?php echo htmlspecialchars($offer['created_by_name'] ?? '-', ENT_QUOTES, 'UTF-8'); ?> | Tarih: <?php echo htmlspecialchars($offer['created_at'] ?? '', ENT_QUOTES, 'UTF-8'); ?></p>

        <?php if (!empty($canUpdateStatus)): ?>
            <form method="POST" action="/offers/<?php echo htmlspecialchars((string) ($offer['id'] ?? ''), ENT_QUOTES, 'UTF-8'); ?>/status" style="margin-top: 12px;">
                <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars(csrf_token(), ENT_QUOTES, 'UTF-8'); ?>">
                <label for="status"><strong>Durumu Güncelle</strong></label>
                <select name="status" id="status">
                    <option value="sent" <?php echo (($offer['status'] ?? '') === 'sent') ? 'selected' : ''; ?>>sent</option>
                    <option value="approved" <?php echo (($offer['status'] ?? '') === 'approved') ? 'selected' : ''; ?>>approved</option>
                    <option value="rejected" <?php echo (($offer['status'] ?? '') === 'rejected') ? 'selected' : ''; ?>>rejected</option>
                </select>
                <button type="submit">Kaydet</button>
            </form>
        <?php endif; ?>
    </div>

    <div class="card">
        <h3>Satır Detayları</h3>
        <?php if (empty($items)): ?>
            <p class="muted">Herhangi bir satır bulunamadı.</p>
        <?php else: ?>
            <table>
                <thead>
                    <tr>
                        <th>Ürün</th>
                        <th>Miktar</th>
                        <th>Birim Fiyat</th>
                        <th>İskonto (%)</th>
                        <th>İskonto (Tutar)</th>
                        <th>KDV (%)</th>
                        <th>Satır Toplamı</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($items as $item): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($item['product_name'] ?? '-', ENT_QUOTES, 'UTF-8'); ?></td>
                            <td><?php echo htmlspecialchars((string) ($item['quantity'] ?? '0'), ENT_QUOTES, 'UTF-8'); ?></td>
                            <td><?php echo htmlspecialchars((string) ($item['unit_price'] ?? '0'), ENT_QUOTES, 'UTF-8'); ?></td>
                            <td><?php echo htmlspecialchars((string) ($item['discount_rate'] ?? '-'), ENT_QUOTES, 'UTF-8'); ?></td>
                            <td><?php echo htmlspecialchars((string) ($item['discount_amount'] ?? '0'), ENT_QUOTES, 'UTF-8'); ?></td>
                            <td><?php echo htmlspecialchars((string) ($item['vat_rate'] ?? '0'), ENT_QUOTES, 'UTF-8'); ?></td>
                            <td><?php echo htmlspecialchars((string) ($item['line_total'] ?? '0'), ENT_QUOTES, 'UTF-8'); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>
</div>
</body>
</html>
