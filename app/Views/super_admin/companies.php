<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <title>Super Admin - Firmalar</title>
    <style>
        body { font-family: Arial, sans-serif; background: #f5f5f5; margin: 0; padding: 32px; }
        .card { background: #fff; border-radius: 8px; padding: 20px; margin-bottom: 20px; box-shadow: 0 2px 6px rgba(0,0,0,0.08); }
        table { width: 100%; border-collapse: collapse; margin-top: 12px; }
        th, td { border: 1px solid #e5e7eb; padding: 8px; text-align: left; }
        th { background: #f0f2f5; }
        .flash { padding: 10px 14px; border-radius: 6px; margin-bottom: 12px; }
        .flash-success { background: #d1e7dd; color: #0f5132; }
        .flash-error { background: #f8d7da; color: #842029; }
        label { display: block; margin-bottom: 6px; font-weight: bold; }
        input, select { width: 100%; padding: 8px; margin-bottom: 10px; border: 1px solid #ccc; border-radius: 4px; }
        button { padding: 10px 14px; background: #0d6efd; color: #fff; border: none; border-radius: 4px; cursor: pointer; }
        .muted { color: #6c757d; font-size: 12px; }
    </style>
</head>
<body>
    <div class="card">
        <h2>Super Admin - Firma Yönetimi</h2>
        <?php if (!empty($flash['success'])): ?>
            <div class="flash flash-success"><?php echo htmlspecialchars($flash['success'], ENT_QUOTES, 'UTF-8'); ?></div>
        <?php endif; ?>
        <?php if (!empty($flash['error'])): ?>
            <div class="flash flash-error"><?php echo htmlspecialchars($flash['error'], ENT_QUOTES, 'UTF-8'); ?></div>
        <?php endif; ?>
        <p class="muted">Bu ekran yalnızca Super Admin tarafından erişilebilir. Firmaları görüntüleyin, yeni firma ekleyin ve paket atayın.</p>
    </div>

    <div class="card">
        <h3>Firmalar</h3>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Ad</th>
                    <th>Durum</th>
                    <th>Aktif Paket</th>
                    <th>Abonelik Durumu</th>
                </tr>
            </thead>
            <tbody>
            <?php foreach ($companies as $company): ?>
                <?php $subscription = $subscriptions[$company['id']] ?? null; ?>
                <tr>
                    <td><?php echo (int) $company['id']; ?></td>
                    <td><?php echo htmlspecialchars($company['name'], ENT_QUOTES, 'UTF-8'); ?></td>
                    <td><?php echo htmlspecialchars($company['status'], ENT_QUOTES, 'UTF-8'); ?></td>
                    <td>
                        <?php
                        if ($subscription) {
                            $package = array_values(array_filter($packages, fn($p) => (int) $p['id'] === (int) $subscription['package_id']));
                            echo htmlspecialchars($package[0]['name'] ?? 'Bilinmiyor', ENT_QUOTES, 'UTF-8');
                        } else {
                            echo '<span class="muted">Paket yok</span>';
                        }
                        ?>
                    </td>
                    <td>
                        <?php if ($subscription): ?>
                            <?php echo htmlspecialchars($subscription['status'], ENT_QUOTES, 'UTF-8'); ?> (<?php echo htmlspecialchars($subscription['ends_at'], ENT_QUOTES, 'UTF-8'); ?>)
                        <?php else: ?>
                            <span class="muted">Aktif abonelik yok</span>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <div class="card">
        <h3>Yeni Firma Oluştur</h3>
        <form method="POST" action="/super-admin/companies">
            <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars(csrf_token(), ENT_QUOTES, 'UTF-8'); ?>">
            <label for="company_name">Firma Adı</label>
            <input id="company_name" name="name" type="text" required>

            <label for="company_package">Başlangıç Paketi (opsiyonel)</label>
            <select id="company_package" name="package_id">
                <option value="">Paket seçin</option>
                <?php foreach ($packages as $package): ?>
                    <option value="<?php echo (int) $package['id']; ?>">
                        <?php echo htmlspecialchars($package['name'], ENT_QUOTES, 'UTF-8'); ?>
                    </option>
                <?php endforeach; ?>
            </select>

            <button type="submit">Oluştur</button>
        </form>
    </div>

    <div class="card">
        <h3>Paketi Güncelle / Abonelik Başlat</h3>
        <form method="POST" action="/super-admin/subscriptions">
            <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars(csrf_token(), ENT_QUOTES, 'UTF-8'); ?>">
            <label for="assign_company">Firma</label>
            <select id="assign_company" name="company_id" required>
                <option value="">Seçin</option>
                <?php foreach ($companies as $company): ?>
                    <option value="<?php echo (int) $company['id']; ?>">
                        <?php echo htmlspecialchars($company['name'], ENT_QUOTES, 'UTF-8'); ?>
                    </option>
                <?php endforeach; ?>
            </select>

            <label for="assign_package">Paket</label>
            <select id="assign_package" name="package_id" required>
                <option value="">Seçin</option>
                <?php foreach ($packages as $package): ?>
                    <option value="<?php echo (int) $package['id']; ?>">
                        <?php echo htmlspecialchars($package['name'], ENT_QUOTES, 'UTF-8'); ?>
                    </option>
                <?php endforeach; ?>
            </select>

            <label for="status">Abonelik Durumu</label>
            <select id="status" name="status" required>
                <option value="active">active</option>
                <option value="trial">trial</option>
                <option value="suspended">suspended</option>
                <option value="expired">expired</option>
            </select>

            <button type="submit">Paketi Ata</button>
        </form>
    </div>
</body>
</html>
