<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <title>Yeni Cari</title>
    <style>
        body { font-family: Arial, sans-serif; background: #f5f5f5; margin: 0; padding: 32px; }
        .card { max-width: 640px; margin: 0 auto; background: #fff; border-radius: 8px; padding: 20px; box-shadow: 0 2px 6px rgba(0,0,0,0.08); }
        label { display: block; margin-bottom: 6px; font-weight: bold; }
        input, select { width: 100%; padding: 10px; margin-bottom: 12px; border: 1px solid #ccc; border-radius: 4px; }
        button { padding: 12px 16px; background: #198754; color: #fff; border: none; border-radius: 6px; cursor: pointer; }
        .flash { padding: 10px 14px; border-radius: 6px; margin-bottom: 12px; }
        .flash-success { background: #d1e7dd; color: #0f5132; }
        .flash-error { background: #f8d7da; color: #842029; }
        .actions { display: flex; gap: 10px; align-items: center; }
        a { color: #0d6efd; text-decoration: none; }
    </style>
</head>
<body>
<div class="card">
    <h2>Yeni Cari Ekle</h2>
    <?php if (!empty($flash['success'])): ?>
        <div class="flash flash-success"><?php echo htmlspecialchars($flash['success'], ENT_QUOTES, 'UTF-8'); ?></div>
    <?php endif; ?>
    <?php if (!empty($flash['error'])): ?>
        <div class="flash flash-error"><?php echo htmlspecialchars($flash['error'], ENT_QUOTES, 'UTF-8'); ?></div>
    <?php endif; ?>

    <form method="POST" action="/caris/store">
        <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars(csrf_token(), ENT_QUOTES, 'UTF-8'); ?>">

        <label for="type">Cari Tipi</label>
        <select id="type" name="type" required>
            <option value="">Seçiniz</option>
            <option value="customer">Müşteri</option>
            <option value="supplier">Tedarikçi</option>
            <option value="both">Müşteri &amp; Tedarikçi</option>
        </select>

        <label for="name">Cari Adı</label>
        <input id="name" name="name" type="text" required>

        <label for="tax_office">Vergi Dairesi</label>
        <input id="tax_office" name="tax_office" type="text">

        <label for="tax_number">Vergi Numarası</label>
        <input id="tax_number" name="tax_number" type="text">

        <h4>Temel İletişim (opsiyonel)</h4>
        <label for="contact_name">İlgili Kişi</label>
        <input id="contact_name" name="contact_name" type="text">

        <label for="contact_email">E-posta</label>
        <input id="contact_email" name="contact_email" type="email">

        <label for="contact_phone">Telefon</label>
        <input id="contact_phone" name="contact_phone" type="text">

        <div class="actions">
            <button type="submit">Kaydet</button>
            <a href="/caris">Listeye Dön</a>
        </div>
    </form>
</div>
</body>
</html>
