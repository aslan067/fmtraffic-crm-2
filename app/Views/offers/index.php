<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <title>Teklifler</title>
    <style>
        body { font-family: Arial, sans-serif; background: #f5f5f5; margin: 0; padding: 32px; }
        .container { max-width: 1100px; margin: 0 auto; }
        .card { background: #fff; border-radius: 8px; padding: 20px; box-shadow: 0 2px 6px rgba(0,0,0,0.08); }
        .header { display: flex; justify-content: space-between; align-items: center; }
        a.button { display: inline-block; padding: 10px 14px; background: #0d6efd; color: #fff; border-radius: 6px; text-decoration: none; }
        table { width: 100%; border-collapse: collapse; margin-top: 12px; }
        th, td { border: 1px solid #e5e5e5; padding: 10px; text-align: left; }
        th { background: #f1f3f5; }
        .badge { display: inline-block; padding: 4px 8px; border-radius: 12px; font-size: 12px; }
        .badge.draft { background: #e2e3e5; color: #383d41; }
        .badge.sent { background: #cff4fc; color: #055160; }
        .badge.approved { background: #d1e7dd; color: #0f5132; }
        .badge.rejected { background: #f8d7da; color: #842029; }
        .flash { padding: 10px 14px; border-radius: 6px; margin-bottom: 12px; }
        .flash-success { background: #d1e7dd; color: #0f5132; }
        .flash-error { background: #f8d7da; color: #842029; }
        .muted { color: #6c757d; }
    </style>
</head>
<body>
<div class="container">
    <div class="card">
        <div class="header">
            <h2>Teklifler</h2>
            <?php if (canAccess('offer', 'offer.create')): ?>
                <a href="/offers/create" class="button">Yeni Teklif</a>
            <?php endif; ?>
        </div>

        <?php if (!empty($flash['success'])): ?>
            <div class="flash flash-success"><?php echo htmlspecialchars($flash['success'], ENT_QUOTES, 'UTF-8'); ?></div>
        <?php endif; ?>
        <?php if (!empty($flash['error'])): ?>
            <div class="flash flash-error"><?php echo htmlspecialchars($flash['error'], ENT_QUOTES, 'UTF-8'); ?></div>
        <?php endif; ?>

        <?php if (empty($offers)): ?>
            <p class="muted">Henüz teklif eklenmemiş.</p>
        <?php else: ?>
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
                        <th>Oluşturan</th>
                        <th>Oluşturulma</th>
                        <th>İşlemler</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($offers as $offer): ?>
                        <tr>
                            <td><?php echo htmlspecialchars((string) ($offer['id'] ?? ''), ENT_QUOTES, 'UTF-8'); ?></td>
                            <td><?php echo htmlspecialchars($offer['cari_name'] ?? '-', ENT_QUOTES, 'UTF-8'); ?></td>
                            <?php if (!empty($isSuperAdmin)): ?>
                                <td><?php echo htmlspecialchars($offer['company_name'] ?? '-', ENT_QUOTES, 'UTF-8'); ?></td>
                            <?php endif; ?>
                            <td><?php echo htmlspecialchars($offer['currency'] ?? 'TRY', ENT_QUOTES, 'UTF-8'); ?></td>
                            <td><?php echo number_format((float) ($offer['total_amount'] ?? 0), 2, ',', '.'); ?></td>
                            <td><span class="badge <?php echo htmlspecialchars($offer['status'] ?? 'draft', ENT_QUOTES, 'UTF-8'); ?>"><?php echo htmlspecialchars($offer['status'] ?? 'draft', ENT_QUOTES, 'UTF-8'); ?></span></td>
                            <td><?php echo htmlspecialchars($offer['created_by_name'] ?? '-', ENT_QUOTES, 'UTF-8'); ?></td>
                            <td><?php echo htmlspecialchars($offer['created_at'] ?? '', ENT_QUOTES, 'UTF-8'); ?></td>
                            <td><a href="/offers/<?php echo htmlspecialchars((string) ($offer['id'] ?? ''), ENT_QUOTES, 'UTF-8'); ?>">Görüntüle</a></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>
</div>
</body>
</html>
