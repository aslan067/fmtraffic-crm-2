<?php
$title = 'Güncel Durum';
$companyName = $user['company_name'] ?? 'Şirket';

ob_start();
?>
<div class="page-hero mb-4">
    <div>
        <p class="eyebrow text-uppercase mb-1">Finansal Özet</p>
        <h2 class="h3 mb-2">Güncel Durum</h2>
        <p class="text-muted mb-0"><?php echo htmlspecialchars($companyName, ENT_QUOTES, 'UTF-8'); ?> için tahsilat ve ödeme görünümü.</p>
    </div>
    <div class="hero-actions d-flex align-items-center gap-2">
        <?php if (can('users.create')): ?>
            <a href="/users/create" class="btn btn-outline-primary btn-sm d-flex align-items-center gap-2">
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

<div class="card dashboard-slab mb-4">
    <div class="card-body d-flex justify-content-between align-items-center flex-wrap gap-3">
        <div>
            <div class="text-uppercase small text-muted mb-1">Finans & CRM</div>
            <h3 class="h5 mb-1">Paraşüt benzeri özet</h3>
            <p class="text-muted mb-0">Tahsilatlar, ödemeler ve KPI kartları tek bakışta.</p>
        </div>
        <div class="d-flex align-items-center gap-2">
            <span class="badge text-bg-light d-inline-flex align-items-center gap-1"><i class="bi bi-lightning-charge"></i> Anlık görünüm</span>
            <span class="badge text-bg-light d-inline-flex align-items-center gap-1"><i class="bi bi-calendar-week"></i> Bu ay</span>
        </div>
    </div>
</div>

<div class="card mb-4">
    <div class="card-header bg-white d-flex align-items-center justify-content-between">
        <div class="d-flex align-items-center gap-2">
            <div class="pill-icon bg-primary-subtle text-primary"><i class="bi bi-cash-coin"></i></div>
            <div>
                <div class="text-uppercase small text-muted mb-0">Tahsilatlar</div>
                <h3 class="h6 mb-0">Planlanmış ve gecikmiş akış</h3>
            </div>
        </div>
        <span class="badge text-bg-light">Örnek veri</span>
    </div>
    <div class="card-body">
        <div class="row g-4">
            <div class="col-12 col-md-4">
                <div class="donut-card d-flex align-items-center gap-3">
                    <div class="donut" style="--donut-value:72; --donut-color:#4c7dff;">
                        <div class="donut-center">
                            <span class="fw-semibold">72%</span>
                            <small class="text-muted">Toplam</small>
                        </div>
                    </div>
                    <div>
                        <div class="text-uppercase small text-muted mb-1">Toplam Tahsil Edilecek</div>
                        <h4 class="h6 mb-1">₺ 1.250.000</h4>
                        <p class="small text-muted mb-0">Haftalık planlanan tahsilatlar.</p>
                    </div>
                </div>
            </div>
            <div class="col-12 col-md-4">
                <div class="donut-card d-flex align-items-center gap-3">
                    <div class="donut" style="--donut-value:38; --donut-color:#f59e0b;">
                        <div class="donut-center">
                            <span class="fw-semibold">38%</span>
                            <small class="text-muted">Gecikmiş</small>
                        </div>
                    </div>
                    <div>
                        <div class="text-uppercase small text-muted mb-1">Gecikmiş</div>
                        <h4 class="h6 mb-1">₺ 240.000</h4>
                        <p class="small text-muted mb-0">7 günü aşan gecikme placeholder.</p>
                    </div>
                </div>
            </div>
            <div class="col-12 col-md-4">
                <div class="donut-card d-flex align-items-center gap-3">
                    <div class="donut" style="--donut-value:18; --donut-color:#64748b;">
                        <div class="donut-center">
                            <span class="fw-semibold">18%</span>
                            <small class="text-muted">Plan yok</small>
                        </div>
                    </div>
                    <div>
                        <div class="text-uppercase small text-muted mb-1">Planlanmamış</div>
                        <h4 class="h6 mb-1">₺ 80.000</h4>
                        <p class="small text-muted mb-0">Plan ataması bekleyen tutarlar.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="card mb-4">
    <div class="card-header bg-white d-flex align-items-center justify-content-between">
        <div class="d-flex align-items-center gap-2">
            <div class="pill-icon bg-success-subtle text-success"><i class="bi bi-credit-card-2-front"></i></div>
            <div>
                <div class="text-uppercase small text-muted mb-0">Ödemeler</div>
                <h3 class="h6 mb-0">Bütçe ve nakit çıkışları</h3>
            </div>
        </div>
        <span class="badge text-bg-light">Örnek veri</span>
    </div>
    <div class="card-body">
        <div class="row g-4">
            <div class="col-12 col-md-4">
                <div class="donut-card d-flex align-items-center gap-3">
                    <div class="donut" style="--donut-value:64; --donut-color:#22c55e;">
                        <div class="donut-center">
                            <span class="fw-semibold">64%</span>
                            <small class="text-muted">Toplam</small>
                        </div>
                    </div>
                    <div>
                        <div class="text-uppercase small text-muted mb-1">Ödenecek</div>
                        <h4 class="h6 mb-1">₺ 940.000</h4>
                        <p class="small text-muted mb-0">Cari ve tedarikçi ödemeleri.</p>
                    </div>
                </div>
            </div>
            <div class="col-12 col-md-4">
                <div class="donut-card d-flex align-items-center gap-3">
                    <div class="donut" style="--donut-value:29; --donut-color:#ef4444;">
                        <div class="donut-center">
                            <span class="fw-semibold">29%</span>
                            <small class="text-muted">Gecikmiş</small>
                        </div>
                    </div>
                    <div>
                        <div class="text-uppercase small text-muted mb-1">Geciken Ödemeler</div>
                        <h4 class="h6 mb-1">₺ 180.000</h4>
                        <p class="small text-muted mb-0">Vadesi geçen faturalar.</p>
                    </div>
                </div>
            </div>
            <div class="col-12 col-md-4">
                <div class="donut-card d-flex align-items-center gap-3">
                    <div class="donut" style="--donut-value:14; --donut-color:#94a3b8;">
                        <div class="donut-center">
                            <span class="fw-semibold">14%</span>
                            <small class="text-muted">Plan yok</small>
                        </div>
                    </div>
                    <div>
                        <div class="text-uppercase small text-muted mb-1">Planlanmamış</div>
                        <h4 class="h6 mb-1">₺ 60.000</h4>
                        <p class="small text-muted mb-0">Henüz takvimlenmemiş ödemeler.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row g-4 mb-4">
    <div class="col-12 col-lg-4">
        <div class="kpi-card card h-100">
            <div class="card-body">
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <div class="pill-icon bg-info-subtle text-info"><i class="bi bi-printer"></i></div>
                    <span class="badge text-bg-light">Teklif</span>
                </div>
                <div class="text-uppercase small text-muted mb-1">Yazdırılmamış teklifler</div>
                <h3 class="display-6 fw-semibold mb-2">18</h3>
                <p class="text-muted small mb-0">Hazırlanıp paylaşılmamış taslaklar.</p>
            </div>
        </div>
    </div>
    <div class="col-12 col-lg-4">
        <div class="kpi-card card h-100">
            <div class="card-body">
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <div class="pill-icon bg-warning-subtle text-warning"><i class="bi bi-arrow-repeat"></i></div>
                    <span class="badge text-bg-light">Otomasyon</span>
                </div>
                <div class="text-uppercase small text-muted mb-1">Tekrarlayan işlemler</div>
                <h3 class="display-6 fw-semibold mb-2">32</h3>
                <p class="text-muted small mb-0">Aktif aidat ve abonelik işlemleri.</p>
            </div>
        </div>
    </div>
    <div class="col-12 col-lg-4">
        <div class="kpi-card card h-100">
            <div class="card-body">
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <div class="pill-icon bg-danger-subtle text-danger"><i class="bi bi-percent"></i></div>
                    <span class="badge text-bg-light">Vergi</span>
                </div>
                <div class="text-uppercase small text-muted mb-1">Bu ay oluşan KDV</div>
                <h3 class="display-6 fw-semibold mb-2">₺ 210.000</h3>
                <p class="text-muted small mb-0">Satış ve alış hareketlerinden.</p>
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
        <div class="text-uppercase small text-muted mb-1">Zaman Çizelgesi</div>
        <h3 class="h6 mb-0">Günlük akış</h3>
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
