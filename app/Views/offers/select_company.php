<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <title>Firma Seç - Teklif Testi</title>
    <style>
        body { font-family: Arial, sans-serif; background: #f5f5f5; margin: 0; padding: 32px; }
        .container { max-width: 720px; margin: 0 auto; }
        .card { background: #fff; border-radius: 8px; padding: 20px; box-shadow: 0 2px 6px rgba(0,0,0,0.08); }
        label { display: block; margin-bottom: 6px; font-weight: bold; }
        select { width: 100%; padding: 10px; margin-bottom: 12px; border: 1px solid #ced4da; border-radius: 6px; }
        button { padding: 10px 14px; border: none; border-radius: 6px; cursor: pointer; background: #0d6efd; color: #fff; }
        .flash { padding: 10px 14px; border-radius: 6px; margin-bottom: 12px; }
        .flash-success { background: #d1e7dd; color: #0f5132; }
        .flash-error { background: #f8d7da; color: #842029; }
        .muted { color: #6c757d; }
    </style>
</head>
<body>
<div class="container">
    <div class="card">
        <h2>Super Admin - Teklif Test Firması Seç</h2>
        <p class="muted">Teklif oluştururken kullanılacak firma bağlamını seçin. Bu seçim sadece sizin oturumunuz için geçerlidir.</p>

        <?php if (!empty($flash['success'])): ?>
            <div class="flash flash-success"><?php echo htmlspecialchars($flash['success'], ENT_QUOTES, 'UTF-8'); ?></div>
        <?php endif; ?>
        <?php if (!empty($flash['error'])): ?>
            <div class="flash flash-error"><?php echo htmlspecialchars($flash['error'], ENT_QUOTES, 'UTF-8'); ?></div>
        <?php endif; ?>

        <form method="POST" action="/offers/select-company">
            <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars(csrf_token(), ENT_QUOTES, 'UTF-8'); ?>">

            <label for="company_id">Firma</label>
            <select id="company_id" name="company_id" required>
                <option value="">Seçiniz</option>
                <?php foreach ($companies as $company): ?>
                    <option value="<?php echo (int) ($company['id'] ?? 0); ?>" <?php echo (!empty($selectedCompanyId) && (int) $selectedCompanyId === (int) ($company['id'] ?? 0)) ? 'selected' : ''; ?>>
                        <?php echo htmlspecialchars($company['name'] ?? '', ENT_QUOTES, 'UTF-8'); ?>
                    </option>
                <?php endforeach; ?>
            </select>

            <button type="submit">Firma Seç</button>
        </form>
    </div>
</div>
</body>
</html>
