<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <title>Giriş Yap</title>
    <style>
        body { font-family: Arial, sans-serif; background: #f5f5f5; margin: 0; padding: 40px; }
        .container { max-width: 400px; margin: 0 auto; background: #fff; padding: 24px; border-radius: 8px; box-shadow: 0 2px 6px rgba(0,0,0,0.1); }
        .alert { padding: 10px 12px; border-radius: 6px; margin-bottom: 12px; }
        .alert.error { background: #fde4e4; color: #b00020; border: 1px solid #f5b4b4; }
        .alert.success { background: #e7f4e4; color: #2d6a2d; border: 1px solid #b8e0b8; }
        label { display: block; margin-bottom: 6px; }
        input[type="email"], input[type="password"] { width: 100%; padding: 10px; margin-bottom: 12px; border: 1px solid #ccc; border-radius: 4px; }
        button { width: 100%; padding: 10px; background: #007bff; color: #fff; border: none; border-radius: 4px; cursor: pointer; }
    </style>
</head>
<body>
<div class="container">
    <h2>CRM Giriş</h2>
    <?php if (!empty($error)): ?>
        <div class="alert error"><?php echo htmlspecialchars($error, ENT_QUOTES, 'UTF-8'); ?></div>
    <?php endif; ?>
    <?php if (!empty($success)): ?>
        <div class="alert success"><?php echo htmlspecialchars($success, ENT_QUOTES, 'UTF-8'); ?></div>
    <?php endif; ?>
    <form method="POST" action="/login">
        <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars(csrf_token(), ENT_QUOTES, 'UTF-8'); ?>">
        <label for="email">E-posta</label>
        <input id="email" name="email" type="email" required>

        <label for="password">Şifre</label>
        <input id="password" name="password" type="password" required>

        <button type="submit">Giriş Yap</button>
    </form>
</div>
</body>
</html>
