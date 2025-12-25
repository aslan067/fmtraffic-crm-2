<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <title>Dashboard</title>
    <style>
        body { font-family: Arial, sans-serif; background: #f5f5f5; margin: 0; padding: 40px; }
        .container { max-width: 640px; margin: 0 auto; background: #fff; padding: 24px; border-radius: 8px; box-shadow: 0 2px 6px rgba(0,0,0,0.1); }
        form { margin-top: 12px; }
        button { padding: 10px 16px; background: #dc3545; color: #fff; border: none; border-radius: 4px; cursor: pointer; }
    </style>
</head>
<body>
<div class="container">
    <h2>Dashboard</h2>
    <p>Hoş geldiniz, <?php echo htmlspecialchars($user['name'] ?? '', ENT_QUOTES, 'UTF-8'); ?> /
        Firma: <?php echo htmlspecialchars($user['company_name'] ?? '', ENT_QUOTES, 'UTF-8'); ?></p>

    <div style="margin: 16px 0; padding: 12px; background: #f8f9fa; border-radius: 8px;">
        <strong>Menü</strong>
        <ul style="list-style: none; padding-left: 0; margin: 8px 0 0 0;">
            <?php if (canAccess('cari', 'cari.view')): ?>
                <li style="margin-bottom: 6px;"><a href="/caris">Cari Yönetimi</a></li>
            <?php endif; ?>

            <?php if (can('users.create')): ?>
                <li style="margin-bottom: 6px;"><a href="/users/create">Kullanıcı Oluştur</a></li>
            <?php endif; ?>

            <?php if (canAccess('product', 'product.view')): ?>
                <li style="margin-bottom: 6px;"><a href="/products">Ürünler</a></li>
            <?php endif; ?>

            <?php if (!empty($user['is_super_admin'])): ?>
                <li style="margin-bottom: 6px;"><a href="/super-admin/companies">Super Admin - Firma Yönetimi</a></li>
            <?php endif; ?>
        </ul>
    </div>

    <?php if (!empty($user['is_super_admin'])): ?>
        <p style="color:#0d6efd;"><strong>Super Admin</strong> olarak giriş yaptınız. <a href="/super-admin/companies">Firma yönetimine git</a>.</p>
    <?php else: ?>
        <?php if (can('users.create')): ?>
            <p><a href="/users/create">Kullanıcı oluştur</a> sayfasından paket limitine tabi kullanıcı ekleyebilirsiniz.</p>
        <?php else: ?>
            <p style="color:#6c757d;">Kullanıcı oluşturma yetkiniz veya paket özelliğiniz yok.</p>
        <?php endif; ?>
    <?php endif; ?>
    <form method="POST" action="/logout">
        <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars(csrf_token(), ENT_QUOTES, 'UTF-8'); ?>">
        <button type="submit">Çıkış</button>
    </form>
</div>
</body>
</html>
