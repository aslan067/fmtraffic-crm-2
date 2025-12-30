<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <title>Satış Detayı</title>
    <style>
        body { font-family: Arial, sans-serif; background: #f5f5f5; margin: 0; padding: 32px; }
        .container { max-width: 1000px; margin: 0 auto; }
        .card { background: #fff; border-radius: 8px; padding: 20px; box-shadow: 0 2px 6px rgba(0,0,0,0.08); margin-bottom: 16px; }
        .badge { display: inline-block; padding: 4px 8px; border-radius: 12px; font-size: 12px; }
        .badge.active { background: #d1e7dd; color: #0f5132; }
        .badge.cancelled { background: #f8d7da; color: #842029; }
        table { width: 100%; border-collapse: collapse; margin-top: 12px; }
        th, td { border: 1px solid #e5e5e5; padding: 8px; text-align: left; }
        th { background: #f1f3f5; }
        .flash { padding: 10px 14px; border-radius: 6px; margin-bottom: 12px; }
        .flash-success { background: #d1e7dd; color: #0f5132; }
        .flash-error { background: #f8d7da; color: #842029; }
        .muted { color: #6c757d; }
        a.button { display: inline-block; padding: 10px 14px; border-radius: 6px; background: #0d6efd; color: #fff; text-decoration: none; }
    </style>
</head>
<body>
<div class="container">
    <div class="card">
        <a href="/sales" style="text-decoration:none;">← Satış listesine dön</a>
    </div>

    <div class="card">
        <h2>Satış #<?php echo htmlspecialchars((string) ($sale['id'] ?? ''), ENT_QUOTES, 'UTF-8'); ?></h2>

        <?php if (!empty($flash['success'])): ?>
            <div class="flash flash-success"><?php echo htmlspecialchars($flash['success'], ENT_QUOTES, 'UTF-8'); ?></div>
        <?php endif; ?>
        <?php if (!empty($flash['error'])): ?>
            <div class="flash flash-error"><?php echo htmlspecialchars($flash['error'], ENT_QUOTES, 'UTF-8'); ?></div>
        <?php endif; ?>

        <p><strong>Cari:</strong> <?php echo htmlspecialchars($sale['cari_name'] ?? '-', ENT_QUOTES, 'UTF-8'); ?></p>
        <p><strong>Firma:</strong> <?php echo htmlspecialchars($sale['company_name'] ?? '-', ENT_QUOTES, 'UTF-8'); ?></p>
        <p><strong>Para Birimi:</strong> <?php echo htmlspecialchars($sale['currency'] ?? 'TRY', ENT_QUOTES, 'UTF-8'); ?></p>
        <p><strong>Toplam:</strong> <?php echo number_format((float) ($sale['total_amount'] ?? 0), 2, ',', '.'); ?></p>
        <p><strong>Durum:</strong> <span class="badge <?php echo htmlspecialchars($sale['status'] ?? 'active', ENT_QUOTES, 'UTF-8'); ?>"><?php echo htmlspecialchars($sale['status'] ?? 'active', ENT_QUOTES, 'UTF-8'); ?></span></p>
        <p class="muted">Tarih: <?php echo htmlspecialchars($sale['created_at'] ?? '', ENT_QUOTES, 'UTF-8'); ?></p>

        <?php if (!empty($sale['offer_id'])): ?>
            <p><a class="button" href="/offers/<?php echo htmlspecialchars((string) ($sale['offer_id'] ?? ''), ENT_QUOTES, 'UTF-8'); ?>">Teklif Detayına Git</a></p>
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
