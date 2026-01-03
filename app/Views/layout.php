<?php

use App\Core\Auth;

$user = Auth::user();
$companyName = $user['company_name'] ?? 'Şirket';
$userName = $user['name'] ?? 'Kullanıcı';
$isSuperAdmin = !empty($user['is_super_admin']);
$activeModules = navigationModules();
$actingCompanyMissing = $isSuperAdmin && Auth::actingCompanyId() === null;
$sidePanel = $sidePanel ?? null;
$flashMessages = isset($flash) && is_array($flash) ? $flash : [];
$pageTitle = $title ?? 'CRM';
$appName = 'FM TRAFIK CRM';
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($pageTitle, ENT_QUOTES, 'UTF-8'); ?></title>
    <link rel="stylesheet" href="https://unpkg.com/@tabler/core@latest/dist/css/tabler.min.css">
    <link rel="stylesheet" href="https://unpkg.com/@tabler/icons-webfont@latest/tabler-icons.min.css">
    <link rel="stylesheet" href="/assets/css/app.css">
</head>
<body class="layout-fluid">
<div class="page">
    <aside class="navbar navbar-vertical navbar-expand-md navbar-light">
        <div class="container-fluid">
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#sidebar-menu" aria-controls="sidebar-menu" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <h1 class="navbar-brand navbar-brand-autodark">
                <span class="navbar-brand-text"><?php echo htmlspecialchars($appName, ENT_QUOTES, 'UTF-8'); ?></span>
            </h1>
            <div class="navbar-nav flex-row d-md-none">
                <div class="nav-item">
                    <div class="nav-link px-0">
                        <div class="d-flex flex-column text-end">
                            <span class="fw-semibold"><?php echo htmlspecialchars($userName, ENT_QUOTES, 'UTF-8'); ?></span>
                            <span class="text-muted small"><?php echo htmlspecialchars($companyName, ENT_QUOTES, 'UTF-8'); ?></span>
                        </div>
                    </div>
                </div>
                <div class="nav-item px-2">
                    <form action="/logout" method="POST" class="mb-0">
                        <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars(csrf_token(), ENT_QUOTES, 'UTF-8'); ?>">
                        <button type="submit" class="btn btn-outline-primary btn-sm" title="Çıkış">
                            <span class="ti ti-logout"></span>
                        </button>
                    </form>
                </div>
            </div>
            <div class="collapse navbar-collapse" id="sidebar-menu">
                <ul class="navbar-nav pt-lg-3">
                    <?php foreach ($activeModules as $module): ?>
                        <li class="nav-item">
                            <a class="nav-link <?php echo $module['is_active'] ? 'active' : ''; ?>"
                               href="<?php echo htmlspecialchars($module['route'], ENT_QUOTES, 'UTF-8'); ?>">
                                <span class="nav-link-icon d-md-none d-lg-inline-block">
                                    <?php echo htmlspecialchars($module['icon'] ?? '', ENT_QUOTES, 'UTF-8'); ?>
                                </span>
                                <span class="nav-link-title">
                                    <?php echo htmlspecialchars($module['label'], ENT_QUOTES, 'UTF-8'); ?>
                                </span>
                            </a>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </div>
        </div>
    </aside>

    <div class="page-wrapper">
        <header class="navbar navbar-expand-md navbar-light d-print-none">
            <div class="container-xl">
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#sidebar-menu" aria-controls="sidebar-menu" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="navbar-brand d-none-navbar-horizontal pe-0 pe-md-3">
                    <span class="navbar-brand-text"><?php echo htmlspecialchars($appName, ENT_QUOTES, 'UTF-8'); ?></span>
                </div>
                <div class="navbar-nav flex-row order-md-last">
                    <div class="d-none d-md-flex flex-column text-end me-3">
                        <span class="fw-semibold"><?php echo htmlspecialchars($userName, ENT_QUOTES, 'UTF-8'); ?></span>
                        <span class="text-muted small"><?php echo htmlspecialchars($companyName, ENT_QUOTES, 'UTF-8'); ?></span>
                    </div>
                    <div class="nav-item">
                        <form action="/logout" method="POST" class="mb-0">
                            <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars(csrf_token(), ENT_QUOTES, 'UTF-8'); ?>">
                            <button type="submit" class="btn btn-outline-primary d-flex align-items-center gap-2">
                                <span class="ti ti-logout"></span>
                                <span>Çıkış</span>
                            </button>
                        </form>
                    </div>
                </div>
                <div class="collapse navbar-collapse" id="navbar-menu">
                    <div class="navbar-nav">
                        <div class="nav-link">
                            <span class="fw-semibold"><?php echo htmlspecialchars($appName, ENT_QUOTES, 'UTF-8'); ?></span>
                        </div>
                    </div>
                </div>
            </div>
        </header>

        <div class="page-header d-print-none">
            <div class="container-xl">
                <div class="row g-2 align-items-center">
                    <div class="col">
                        <h2 class="page-title mb-0"><?php echo htmlspecialchars($pageTitle, ENT_QUOTES, 'UTF-8'); ?></h2>
                    </div>
                </div>
            </div>
        </div>

        <div class="page-body">
            <div class="container-xl">
                <?php if ($actingCompanyMissing): ?>
                    <div class="alert alert-warning mb-3" role="alert">
                        <div class="d-flex flex-column">
                            <strong>Firma seçilmedi.</strong>
                            <span>Super Admin olarak ilerlemek için firma seçimi yapın.</span>
                        </div>
                    </div>
                <?php endif; ?>

                <?php if (!empty($flashMessages)): ?>
                    <?php foreach ($flashMessages as $type => $message): ?>
                        <?php
                        $variant = match ($type) {
                            'success' => 'success',
                            'error' => 'danger',
                            default => 'secondary',
                        };
                        ?>
                        <div class="alert alert-<?php echo $variant; ?> mb-3" role="alert">
                            <?php echo htmlspecialchars($message, ENT_QUOTES, 'UTF-8'); ?>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>

                <?php if ($sidePanel): ?>
                    <div class="row">
                        <div class="col-lg-8 col-xl-9">
                            <?php echo $content ?? ''; ?>
                        </div>
                        <div class="col-lg-4 col-xl-3">
                            <?php echo $sidePanel; ?>
                        </div>
                    </div>
                <?php else: ?>
                    <?php echo $content ?? ''; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
<script src="https://unpkg.com/@tabler/core@latest/dist/js/tabler.min.js"></script>
</body>
</html>
