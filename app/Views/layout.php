<?php

use App\Core\Auth;

$user = Auth::user();
$companyName = $user['company_name'] ?? 'Şirket';
$userName = $user['name'] ?? 'Kullanıcı';
$isSuperAdmin = !empty($user['is_super_admin']);
$activeModules = navigationModules();
$actingCompanyMissing = $isSuperAdmin && Auth::actingCompanyId() === null;
$sidePanel = $sidePanel ?? null;
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($title ?? 'CRM', ENT_QUOTES, 'UTF-8'); ?></title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" integrity="sha384-3wobZ0J3LKH7X1LoE7jC/9JXe+0EB6e/8BiOuhSngQWhOQ/WyJgnsEZqZ8i+A0gD" crossorigin="anonymous">
    <link rel="stylesheet" href="/assets/css/app.css">
</head>
<body class="bg-surface">
<div class="layout d-flex">
    <aside class="sidebar">
        <div class="sidebar-inner h-100 d-flex flex-column">
            <div class="sidebar-brand d-flex align-items-center gap-3 mb-4">
                <div class="brand-icon d-inline-flex align-items-center justify-content-center rounded-3 text-white">
                    <i class="bi bi-activity"></i>
                </div>
                <div>
                    <div class="text-uppercase small text-muted mb-1">FMTraffic CRM</div>
                    <div class="fw-semibold text-dark"><?php echo htmlspecialchars($companyName, ENT_QUOTES, 'UTF-8'); ?></div>
                </div>
            </div>

            <div class="sidebar-section">
                <div class="sidebar-label text-uppercase small text-muted mb-2">Navigasyon</div>
                <nav class="nav flex-column nav-flush gap-1">
                    <?php foreach ($activeModules as $module): ?>
                        <a class="nav-link sidebar-link d-flex align-items-center gap-2 <?php echo $module['is_active'] ? 'active' : ''; ?>"
                           href="<?php echo htmlspecialchars($module['route'], ENT_QUOTES, 'UTF-8'); ?>">
                            <span class="sidebar-icon d-inline-flex align-items-center justify-content-center">
                                <?php echo !empty($module['icon']) ? $module['icon'] : '<i class="bi bi-circle"></i>'; ?>
                            </span>
                            <span class="flex-grow-1"><?php echo htmlspecialchars($module['label'], ENT_QUOTES, 'UTF-8'); ?></span>
                            <?php if ($module['is_active']): ?>
                                <i class="bi bi-chevron-right text-muted small"></i>
                            <?php endif; ?>
                        </a>
                    <?php endforeach; ?>
                </nav>
            </div>

            <div class="sidebar-section mt-4">
                <div class="sidebar-label text-uppercase small text-muted mb-2">Kısayollar</div>
                <div class="d-grid gap-2">
                    <?php if (can('users.create')): ?>
                        <a class="btn btn-ghost d-flex align-items-center justify-content-between" href="/users/create">
                            <span class="d-flex align-items-center gap-2">
                                <i class="bi bi-person-plus"></i>
                                <span>Kullanıcı Oluştur</span>
                            </span>
                            <i class="bi bi-arrow-up-right"></i>
                        </a>
                    <?php endif; ?>
                    <?php if ($isSuperAdmin): ?>
                        <a class="btn btn-ghost d-flex align-items-center justify-content-between" href="/super-admin/companies">
                            <span class="d-flex align-items-center gap-2">
                                <i class="bi bi-shield-lock"></i>
                                <span>Super Admin</span>
                            </span>
                            <i class="bi bi-arrow-up-right"></i>
                        </a>
                    <?php endif; ?>
                </div>
            </div>

            <div class="mt-auto pt-4">
                <div class="d-flex align-items-center gap-3 p-3 rounded-4 bg-cream border border-light">
                    <div class="avatar bg-dark text-white d-inline-flex align-items-center justify-content-center rounded-3">
                        <i class="bi bi-person-circle"></i>
                    </div>
                    <div>
                        <div class="fw-semibold text-dark"><?php echo htmlspecialchars($userName, ENT_QUOTES, 'UTF-8'); ?></div>
                        <div class="text-muted small"><?php echo htmlspecialchars($companyName, ENT_QUOTES, 'UTF-8'); ?></div>
                    </div>
                </div>
            </div>
        </div>
    </aside>

    <div class="content-wrapper flex-grow-1">
        <nav class="navbar app-navbar bg-white sticky-top shadow-sm">
            <div class="container-fluid">
                <div>
                    <div class="text-uppercase small text-muted mb-1">Güncel Finans</div>
                    <div class="d-flex align-items-center gap-2">
                        <h1 class="h5 mb-0"><?php echo htmlspecialchars($title ?? 'CRM', ENT_QUOTES, 'UTF-8'); ?></h1>
                        <span class="badge text-bg-light border">Canlı</span>
                    </div>
                </div>
                <div class="d-flex align-items-center gap-3">
                    <div class="text-end d-none d-md-block">
                        <div class="fw-semibold text-dark"><?php echo htmlspecialchars($userName, ENT_QUOTES, 'UTF-8'); ?></div>
                        <div class="text-muted small">Hesap · <?php echo htmlspecialchars($companyName, ENT_QUOTES, 'UTF-8'); ?></div>
                    </div>
                    <form action="/logout" method="POST" class="mb-0">
                        <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars(csrf_token(), ENT_QUOTES, 'UTF-8'); ?>">
                        <button type="submit" class="btn btn-outline-secondary btn-sm d-flex align-items-center gap-2">
                            <i class="bi bi-box-arrow-right"></i>
                            <span>Çıkış</span>
                        </button>
                    </form>
                </div>
            </div>
        </nav>

        <main class="main-content flex-grow-1">
            <div class="container-fluid py-4">
                <?php if ($actingCompanyMissing): ?>
                    <div class="alert alert-warning d-flex align-items-center gap-2 rounded-3 mb-3" role="alert">
                        <div class="fw-semibold">Firma seçilmedi.</div>
                        <div class="small mb-0">Super Admin olarak ilerlemek için firma seçimi yapın.</div>
                    </div>
                <?php endif; ?>

                <div class="content-grid<?php echo $sidePanel ? ' has-side-panel' : ''; ?>">
                    <div class="main-area">
                        <?php echo $content ?? ''; ?>
                    </div>
                    <?php if ($sidePanel): ?>
                        <aside class="side-panel bg-white shadow-sm rounded-4">
                            <?php echo $sidePanel; ?>
                        </aside>
                    <?php endif; ?>
                </div>
            </div>
        </main>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
</body>
</html>
