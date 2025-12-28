<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <title>Cari Listesi</title>
    <style>
        body { font-family: Arial, sans-serif; background: #f5f5f5; margin: 0; padding: 32px; }
        .container { max-width: 960px; margin: 0 auto; background: #fff; padding: 20px; border-radius: 8px; box-shadow: 0 2px 6px rgba(0,0,0,0.08); }
        .actions { display: flex; gap: 8px; align-items: center; }
        table { width: 100%; border-collapse: collapse; margin-top: 16px; }
        th, td { border: 1px solid #e5e5e5; padding: 10px; text-align: left; }
        th { background: #f1f3f5; }
        .badge { display: inline-block; padding: 4px 8px; border-radius: 12px; font-size: 12px; }
        .badge.active { background: #d1e7dd; color: #0f5132; }
        .badge.passive { background: #f8d7da; color: #842029; }
        .flash { padding: 10px 14px; border-radius: 6px; margin-bottom: 12px; }
        .flash-success { background: #d1e7dd; color: #0f5132; }
        .flash-error { background: #f8d7da; color: #842029; }
        a.button { display: inline-block; padding: 10px 14px; background: #0d6efd; color: #fff; border-radius: 6px; text-decoration: none; }
        form.inline { display: inline; }
        button.danger { padding: 8px 12px; background: #dc3545; color: #fff; border: none; border-radius: 6px; cursor: pointer; }
        .muted { color: #6c757d; }
    </style>
</head>
<body>
<div class="container">
    <div style="display: flex; justify-content: space-between; align-items: center;">
        <h2>Cari Yönetimi</h2>
        <a href="/caris/create" class="button">Yeni Cari</a>
    </div>

    <?php if (!empty($flash['success'])): ?>
        <div class="flash flash-success"><?php echo htmlspecialchars($flash['success'], ENT_QUOTES, 'UTF-8'); ?></div>
    <?php endif; ?>
    <?php if (!empty($flash['error'])): ?>
        <div class="flash flash-error"><?php echo htmlspecialchars($flash['error'], ENT_QUOTES, 'UTF-8'); ?></div>
    <?php endif; ?>

    <?php if (empty($caris)): ?>
        <p class="muted">Henüz cari eklenmemiş.</p>
    <?php else: ?>
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
                        <td class="actions">
                            <a href="/caris/<?php echo htmlspecialchars($cari['id'] ?? '', ENT_QUOTES, 'UTF-8'); ?>/edit">Düzenle</a>
                            <?php if (($cari['status'] ?? '') !== 'passive'): ?>
                                <form method="POST" action="/caris/<?php echo htmlspecialchars($cari['id'] ?? '', ENT_QUOTES, 'UTF-8'); ?>/deactivate" class="inline" onsubmit="return confirm('Cari pasife alınsın mı?');">
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
</body>
</html>
