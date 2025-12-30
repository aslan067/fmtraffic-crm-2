<?php
$title = 'Güncel Durum';
$companyName = $user['company_name'] ?? 'Şirket';

ob_start();
?>
<div class="page-hero mb-4">
    <div>
        <p class="eyebrow text-uppercase mb-1">Finansal Özet</p>
        <h2 class="h3 mb-2">Güncel Durum</h2>
        <p class="text-muted mb-0"><?php echo htmlspecialchars($companyName, ENT_QUOTES, 'UTF-8'); ?> için tahsilat, ödeme ve operasyon görünümü.</p>
    </div>
    <div class="hero-actions d-flex align-items-center gap-2 flex-wrap">
        <?php if (can('users.create')): ?>
            <a href="/users/create" class="btn btn-outline-secondary btn-sm d-flex align-items-center gap-2">
                <i class="bi bi-person-plus"></i>
                <span>Kullanıcı Oluştur</span>
            </a>
        <?php endif; ?>
        <?php if (!empty($user['is_super_admin'])): ?>
            <a href="/super-admin/companies" class="btn btn-primary btn-sm d-flex align-items-center gap-2">
                <i class="bi bi-shield-lock"></i>
                <span>Firma Yönetimi</span>
            </a>
        <?php endif; ?>
    </div>
</div>

<div class="grid-tiles three mb-4">
    <div class="stat-card">
        <div class="d-flex align-items-center justify-content-between mb-2">
            <span class="accent-chip"><i class="bi bi-cash-coin"></i> Tahsilat</span>
            <span class="badge text-bg-light">Bu ay</span>
        </div>
        <div class="d-flex align-items-center justify-content-between">
            <div>
                <div class="text-muted small text-uppercase">Toplam</div>
                <div class="stat-value">₺ 1.250.000</div>
                <div class="stat-trend trend-up">+12% artış</div>
            </div>
            <div class="sparkline w-50"></div>
        </div>
    </div>
    <div class="stat-card">
        <div class="d-flex align-items-center justify-content-between mb-2">
            <span class="accent-chip"><i class="bi bi-credit-card-2-front"></i> Ödeme</span>
            <span class="badge text-bg-light">Nakit çıkışı</span>
        </div>
        <div class="d-flex align-items-center justify-content-between">
            <div>
                <div class="text-muted small text-uppercase">Planlanmış</div>
                <div class="stat-value">₺ 940.000</div>
                <div class="stat-trend trend-neutral">₺ 180.000 gecikmiş</div>
            </div>
            <div class="sparkline w-50"></div>
        </div>
    </div>
    <div class="stat-card">
        <div class="d-flex align-items-center justify-content-between mb-2">
            <span class="accent-chip"><i class="bi bi-bar-chart"></i> Nakit Akışı</span>
            <span class="badge text-bg-light">Güncel</span>
        </div>
        <div class="d-flex align-items-center justify-content-between">
            <div>
                <div class="text-muted small text-uppercase">Öngörü</div>
                <div class="stat-value">₺ 310.000</div>
                <div class="stat-trend trend-up">+₺ 45.000 net</div>
            </div>
            <div class="sparkline w-50"></div>
        </div>
    </div>
</div>

<div class="card mb-4">
    <div class="card-header bg-white d-flex align-items-center justify-content-between">
        <div class="d-flex align-items-center gap-2">
            <div class="pill-icon"><i class="bi bi-activity"></i></div>
            <div>
                <div class="text-uppercase small text-muted mb-0">Finansal bloklar</div>
                <h3 class="h6 mb-0">Tahsilat ve ödeme dengesi</h3>
            </div>
        </div>
        <span class="badge text-bg-light">Gerçek veri gerekmez</span>
    </div>
    <div class="card-body">
        <div class="row g-4">
            <div class="col-12 col-lg-6">
                <div class="stat-card h-100">
                    <div class="d-flex align-items-center justify-content-between mb-3">
                        <div>
                            <div class="text-uppercase small text-muted mb-1">Tahsilat akışı</div>
                            <h4 class="h6 mb-0">Planlanmış vs gecikmiş</h4>
                        </div>
                        <span class="badge text-bg-light">Örnek</span>
                    </div>
                    <div class="mb-3">
                        <div class="sparkline w-100"></div>
                    </div>
                    <div class="d-flex gap-3">
                        <div class="flex-grow-1">
                            <div class="text-muted small text-uppercase">Planlanan</div>
                            <div class="fw-semibold">₺ 1.250.000</div>
                            <div class="text-success small">+₺ 220.000</div>
                        </div>
                        <div class="flex-grow-1">
                            <div class="text-muted small text-uppercase">Gecikmiş</div>
                            <div class="fw-semibold">₺ 240.000</div>
                            <div class="text-danger small">-₺ 65.000</div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-12 col-lg-6">
                <div class="stat-card h-100">
                    <div class="d-flex align-items-center justify-content-between mb-3">
                        <div>
                            <div class="text-uppercase small text-muted mb-1">Ödeme dağılımı</div>
                            <h4 class="h6 mb-0">Bütçe ve nakit çıkışı</h4>
                        </div>
                        <span class="badge text-bg-light">Örnek</span>
                    </div>
                    <div class="mb-3">
                        <div class="sparkline w-100"></div>
                    </div>
                    <div class="d-flex gap-3">
                        <div class="flex-grow-1">
                            <div class="text-muted small text-uppercase">Ödenecek</div>
                            <div class="fw-semibold">₺ 940.000</div>
                            <div class="text-warning small">₺ 180.000 gecikmiş</div>
                        </div>
                        <div class="flex-grow-1">
                            <div class="text-muted small text-uppercase">Planlanmamış</div>
                            <div class="fw-semibold">₺ 60.000</div>
                            <div class="text-muted small">Takvim bekliyor</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row g-4 mb-4">
    <div class="col-12 col-lg-4">
        <div class="card h-100">
            <div class="card-body">
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <div class="pill-icon"><i class="bi bi-box-seam"></i></div>
                    <span class="badge text-bg-light">Ürünler</span>
                </div>
                <div class="text-uppercase small text-muted mb-1">Stok ve aktif ürün</div>
                <h3 class="display-6 fw-semibold mb-2">124</h3>
                <p class="text-muted small mb-3">Aktif ürün, kampanya dışı stoklarla birlikte.</p>
                <div class="mini-chart"></div>
            </div>
        </div>
    </div>
    <div class="col-12 col-lg-4">
        <div class="card h-100">
            <div class="card-body">
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <div class="pill-icon"><i class="bi bi-file-earmark-text"></i></div>
                    <span class="badge text-bg-light">Teklif</span>
                </div>
                <div class="text-uppercase small text-muted mb-1">Onay bekleyen</div>
                <h3 class="display-6 fw-semibold mb-2">36</h3>
                <p class="text-muted small mb-3">PDF gönderilmiş, müşteri geri dönüşü bekleniyor.</p>
                <div class="mini-chart"></div>
            </div>
        </div>
    </div>
    <div class="col-12 col-lg-4">
        <div class="card h-100">
            <div class="card-body">
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <div class="pill-icon"><i class="bi bi-bag-check"></i></div>
                    <span class="badge text-bg-light">Satış</span>
                </div>
                <div class="text-uppercase small text-muted mb-1">Kapalı kazanılan</div>
                <h3 class="display-6 fw-semibold mb-2">18</h3>
                <p class="text-muted small mb-3">Son 30 gün içinde tamamlanan satışlar.</p>
                <div class="mini-chart"></div>
            </div>
        </div>
    </div>
</div>

<?php
$content = ob_get_clean();

ob_start();
?>
<div class="side-panel-header d-flex align-items-center justify-content-between mb-3">
    <div>
        <div class="text-uppercase small text-muted mb-1">Yaklaşan işler</div>
        <h3 class="h6 mb-0">Zaman Çizelgesi</h3>
    </div>
    <span class="badge text-bg-light">Statik</span>
</div>
<div class="timeline">
    <div class="timeline-group">
        <div class="timeline-title">Bugün</div>
        <div class="timeline-item">
            <div class="timeline-dot bg-success"></div>
            <div>
                <div class="fw-semibold">Yurtiçi fatura tahsilatı</div>
                <div class="text-muted small">₺ 55.000 · 14:00 · Otomatik provizyon</div>
            </div>
        </div>
        <div class="timeline-item">
            <div class="timeline-dot bg-primary"></div>
            <div>
                <div class="fw-semibold">Yeni teklif paylaşıldı</div>
                <div class="text-muted small">Teklif #3845 · PDF gönderildi</div>
            </div>
        </div>
    </div>
    <div class="timeline-group">
        <div class="timeline-title">1 gün sonra</div>
        <div class="timeline-item">
            <div class="timeline-dot bg-warning"></div>
            <div>
                <div class="fw-semibold">Abonelik yenilemesi</div>
                <div class="text-muted small">₺ 12.500 · Otomatik çekim planlandı</div>
            </div>
        </div>
    </div>
    <div class="timeline-group">
        <div class="timeline-title text-danger">Gecikmiş</div>
        <div class="timeline-item">
            <div class="timeline-dot bg-danger"></div>
            <div>
                <div class="fw-semibold">Cari ödeme gecikmesi</div>
                <div class="text-muted small">₺ 28.000 · 5 gündür bekliyor</div>
            </div>
        </div>
    </div>
</div>
<?php
$sidePanel = ob_get_clean();

include __DIR__ . '/layout.php';
