<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <title>Teklif Oluştur</title>
    <style>
        body { font-family: Arial, sans-serif; background: #f5f5f5; margin: 0; padding: 32px; }
        .container { max-width: 1100px; margin: 0 auto; }
        .card { background: #fff; border-radius: 8px; padding: 20px; box-shadow: 0 2px 6px rgba(0,0,0,0.08); }
        label { display: block; margin-bottom: 6px; font-weight: bold; }
        select, input[type="text"], input[type="number"] { width: 100%; padding: 10px; margin-bottom: 12px; border: 1px solid #ced4da; border-radius: 6px; }
        table { width: 100%; border-collapse: collapse; margin-top: 12px; }
        th, td { border: 1px solid #e5e5e5; padding: 8px; text-align: left; }
        th { background: #f1f3f5; }
        button { padding: 10px 14px; border: none; border-radius: 6px; cursor: pointer; }
        .primary { background: #0d6efd; color: #fff; }
        .secondary { background: #6c757d; color: #fff; }
        .danger { background: #dc3545; color: #fff; }
        .flash { padding: 10px 14px; border-radius: 6px; margin-bottom: 12px; }
        .flash-success { background: #d1e7dd; color: #0f5132; }
        .flash-error { background: #f8d7da; color: #842029; }
    </style>
</head>
<body>
<div class="container">
    <div class="card">
        <h2>Yeni Teklif</h2>

        <?php if (!empty($flash['success'])): ?>
            <div class="flash flash-success"><?php echo htmlspecialchars($flash['success'], ENT_QUOTES, 'UTF-8'); ?></div>
        <?php endif; ?>
        <?php if (!empty($flash['error'])): ?>
            <div class="flash flash-error"><?php echo htmlspecialchars($flash['error'], ENT_QUOTES, 'UTF-8'); ?></div>
        <?php endif; ?>

        <form method="POST" action="/offers">
            <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars(csrf_token(), ENT_QUOTES, 'UTF-8'); ?>">

            <label for="cari_id">Cari</label>
            <select name="cari_id" id="cari_id" required>
                <option value="">Seçiniz</option>
                <?php foreach ($caris as $cari): ?>
                    <option value="<?php echo htmlspecialchars((string) ($cari['id'] ?? ''), ENT_QUOTES, 'UTF-8'); ?>">
                        <?php echo htmlspecialchars($cari['name'] ?? '', ENT_QUOTES, 'UTF-8'); ?>
                    </option>
                <?php endforeach; ?>
            </select>

            <label for="currency">Para Birimi</label>
            <select name="currency" id="currency" required>
                <option value="TRY">TRY</option>
                <option value="USD">USD</option>
                <option value="EUR">EUR</option>
            </select>

            <h3>Ürün Satırları</h3>
            <table id="items-table">
                <thead>
                    <tr>
                        <th>Ürün</th>
                        <th>Ürün Adı (Snapshot)</th>
                        <th>Miktar</th>
                        <th>Birim Fiyat</th>
                        <th>İskonto (%)</th>
                        <th>İskonto (Tutar)</th>
                        <th>KDV (%)</th>
                        <th>Sil</th>
                    </tr>
                </thead>
                <tbody id="items-body">
                </tbody>
            </table>

            <div style="margin-top: 12px;">
                <button type="button" class="secondary" onclick="addRow()">Satır Ekle</button>
            </div>

            <div style="margin-top: 20px;">
                <button type="submit" class="primary">Teklifi Kaydet</button>
                <a href="/offers" class="danger" style="margin-left: 8px; display:inline-block; padding:10px 14px; text-decoration:none;">İptal</a>
            </div>
        </form>
    </div>
</div>
<script>
    const products = <?php echo json_encode(array_values($products), JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT); ?>;

    function buildProductOptions() {
        let options = '<option value=\"\">Seçiniz</option>';
        products.forEach(p => {
            options += `<option value=\"${p.id}\">${p.code ?? ''} - ${p.name ?? ''}</option>`;
        });
        return options;
    }

    function addRow() {
        const tbody = document.getElementById('items-body');
        const row = document.createElement('tr');
        row.innerHTML = `
            <td>
                <select name=\"product_id[]\">${buildProductOptions()}</select>
            </td>
            <td><input type=\"text\" name=\"product_name[]\" placeholder=\"Ürün adı\" /></td>
            <td><input type=\"number\" name=\"quantity[]\" step=\"0.01\" min=\"0\" value=\"1\" /></td>
            <td><input type=\"number\" name=\"unit_price[]\" step=\"0.01\" min=\"0\" value=\"0\" /></td>
            <td><input type=\"number\" name=\"discount_rate[]\" step=\"0.01\" min=\"0\" value=\"0\" /></td>
            <td><input type=\"number\" name=\"discount_amount[]\" step=\"0.01\" min=\"0\" value=\"0\" /></td>
            <td><input type=\"number\" name=\"vat_rate[]\" step=\"0.01\" min=\"0\" value=\"20\" /></td>
            <td><button type=\"button\" class=\"danger\" onclick=\"removeRow(this)\">Sil</button></td>
        `;
        tbody.appendChild(row);
    }

    function removeRow(button) {
        const row = button.closest('tr');
        if (row) {
            row.remove();
        }
    }

    addRow();
</script>
</body>
</html>
