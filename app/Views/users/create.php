<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <title>Kullanıcı Oluştur</title>
    <style>
        body { font-family: Arial, sans-serif; background: #f5f5f5; margin: 0; padding: 32px; }
        .card { max-width: 480px; margin: 0 auto; background: #fff; border-radius: 8px; padding: 20px; box-shadow: 0 2px 6px rgba(0,0,0,0.08); }
        label { display: block; margin-bottom: 6px; font-weight: bold; }
        input { width: 100%; padding: 10px; margin-bottom: 12px; border: 1px solid #ccc; border-radius: 4px; }
        button { width: 100%; padding: 12px; background: #198754; color: #fff; border: none; border-radius: 4px; cursor: pointer; }
        .flash { padding: 10px 14px; border-radius: 6px; margin-bottom: 12px; }
        .flash-success { background: #d1e7dd; color: #0f5132; }
        .flash-error { background: #f8d7da; color: #842029; }
    </style>
</head>
<body>
    <div class="card">
        <h2>Yeni Kullanıcı Oluştur</h2>
        <?php if (!empty($flash['success'])): ?>
            <div class="flash flash-success"><?php echo htmlspecialchars($flash['success'], ENT_QUOTES, 'UTF-8'); ?></div>
        <?php endif; ?>
        <?php if (!empty($flash['error'])): ?>
            <div class="flash flash-error"><?php echo htmlspecialchars($flash['error'], ENT_QUOTES, 'UTF-8'); ?></div>
        <?php endif; ?>
        <form method="POST" action="/users">
            <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars(csrf_token(), ENT_QUOTES, 'UTF-8'); ?>">

            <label for="name">Ad Soyad</label>
            <input id="name" name="name" type="text" required>

            <label for="email">E-posta</label>
            <input id="email" name="email" type="email" required>

            <label for="password">Şifre</label>
            <input id="password" name="password" type="password" required>

            <button type="submit">Kaydet</button>
        </form>
    </div>
</body>
</html>
