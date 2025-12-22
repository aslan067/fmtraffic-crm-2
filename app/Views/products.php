<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <title>Ürünler</title>
    <style>
        body { font-family: Arial, sans-serif; background: #f5f5f5; margin: 0; padding: 40px; }
        .container { max-width: 720px; margin: 0 auto; background: #fff; padding: 24px; border-radius: 8px; box-shadow: 0 2px 6px rgba(0,0,0,0.1); }
        .badge { display: inline-block; padding: 4px 10px; background: #0d6efd; color: #fff; border-radius: 12px; font-size: 12px; }
        .muted { color: #6c757d; }
    </style>
</head>
<body>
<div class="container">
    <h2>Ürünler</h2>
    <p class="muted">Bu sayfa <span class="badge">permission:product.view</span> middleware'i ile korunmaktadır.</p>
    <p>Hoş geldiniz, <?php echo htmlspecialchars($user['name'] ?? '', ENT_QUOTES, 'UTF-8'); ?>.</p>
    <p>Bu aşamada ürün listesi yerine örnek yetki kontrolü gösterilmektedir.</p>
</div>
</body>
</html>
