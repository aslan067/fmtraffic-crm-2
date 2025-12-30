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
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" integrity="sha384-3wobZ0J3LKH7X1LoE7jC/9JXe+0EB6e/8BiOuhSngQWhOQ/WyJgnsEZqZ8i+A0gD" crossorigin="anonymous">
    <link rel="stylesheet" href="/assets/css/app.css">
</head>
<body class="bg-light">
<nav class="navbar navbar-expand-lg app-navbar shadow-sm fixed-top bg-white">
    <div class="container-fluid">
        <div class="d-flex align-items-center gap-3">
            <div class="brand-icon d-inline-flex align-items-center justify-content-center rounded-3 text-white">
                <i class="bi bi-activity"></i>
            </div>
            <div>
                <div class="text-uppercase small text-muted mb-1">FMTraffic CRM</div>
                <div class="fw-semibold text-dark"><?php echo htmlspecialchars($companyName, ENT_QUOTES, 'UTF-8'); ?></div>
            </div>
        </div>
        <div class="d-flex align-items-center gap-3 ms-auto">
            <div class="text-end">
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

<div class="layout d-flex">
    <aside class="sidebar bg-dark text-white">
        <div class="sidebar-inner px-3 py-4 h-100 d-flex flex-column">
            <div class="d-flex align-items-center gap-3 mb-4">
                <div class="avatar bg-white bg-opacity-10 text-white d-flex align-items-center justify-content-center rounded-3 fs-5">
                    <i class="bi bi-grid-1x2-fill"></i>
                </div>
                <div>
                    <div class="text-uppercase small text-white-50">Kontrol Paneli</div>
                    <div class="fw-semibold"><?php echo htmlspecialchars($companyName, ENT_QUOTES, 'UTF-8'); ?></div>
                </div>
            </div>
            <div class="small text-uppercase text-white-50 mb-2">Modüller</div>
            <nav class="nav flex-column nav-flush gap-1">
                <?php foreach ($activeModules as $module): ?>
                    <a class="nav-link sidebar-link d-flex align-items-center gap-2 text-white <?php echo $module['is_active'] ? 'active' : 'text-white-50'; ?>"
                       href="<?php echo htmlspecialchars($module['route'], ENT_QUOTES, 'UTF-8'); ?>">
                        <span class="sidebar-icon d-inline-flex align-items-center justify-content-center">
                            <?php echo !empty($module['icon']) ? $module['icon'] : '<i class="bi bi-circle"></i>'; ?>
                        </span>
                        <span class="flex-grow-1"><?php echo htmlspecialchars($module['label'], ENT_QUOTES, 'UTF-8'); ?></span>
                        <?php if ($module['is_active']): ?>
                            <i class="bi bi-chevron-right text-white-50 small"></i>
                        <?php endif; ?>
                    </a>
                <?php endforeach; ?>
            </nav>

            <div class="small text-uppercase text-white-50 mt-4 mb-2">Kısayollar</div>
            <div class="d-grid gap-2">
                <?php if (can('users.create')): ?>
                    <a class="btn btn-outline-light btn-sm d-flex align-items-center justify-content-center gap-2" href="/users/create">
                        <i class="bi bi-person-plus"></i>
                        <span>Kullanıcı Oluştur</span>
                    </a>
                <?php endif; ?>
                <?php if ($isSuperAdmin): ?>
                    <a class="btn btn-outline-light btn-sm d-flex align-items-center justify-content-center gap-2" href="/super-admin/companies">
                        <i class="bi bi-shield-lock"></i>
                        <span>Super Admin</span>
                    </a>
                <?php endif; ?>
            </div>

            <div class="mt-auto pt-4">
                <div class="text-white-50 small">Finans & CRM görünümü</div>
            </div>
        </div>
    </aside>

    <div class="content-wrapper flex-grow-1">
        <main class="main-content flex-grow-1">
            <div class="container-fluid py-4">
                <div class="page-header d-flex align-items-center justify-content-between flex-wrap gap-3 mb-4">
                    <div>
                        <p class="text-uppercase small text-muted mb-1">Güncel Finans</p>
                        <h1 class="h4 mb-0"><?php echo htmlspecialchars($title ?? 'CRM', ENT_QUOTES, 'UTF-8'); ?></h1>
                    </div>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb mb-0">
                            <li class="breadcrumb-item"><a href="/dashboard" class="text-decoration-none">Anasayfa</a></li>
                            <li class="breadcrumb-item active" aria-current="page"><?php echo htmlspecialchars($title ?? 'Sayfa', ENT_QUOTES, 'UTF-8'); ?></li>
                        </ol>
                    </nav>
                </div>

                <?php if ($actingCompanyMissing): ?>
                    <div class="alert alert-warning d-flex align-items-center gap-2" role="alert">
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
