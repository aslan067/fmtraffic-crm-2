<?php

use App\Core\Auth;

$user = Auth::user();
$companyName = $user['company_name'] ?? 'Şirket';
$userName = $user['name'] ?? 'Kullanıcı';
$isSuperAdmin = !empty($user['is_super_admin']);
$activeModules = navigationModules();
$actingCompanyMissing = $isSuperAdmin && Auth::actingCompanyId() === null;
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($title ?? 'CRM', ENT_QUOTES, 'UTF-8'); ?></title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="/assets/css/app.css">
</head>
<body class="bg-light">
<div class="container-fluid min-vh-100">
    <div class="row flex-nowrap">
        <aside class="col-12 col-md-3 col-lg-2 px-3 py-4 border-end bg-white">
            <div class="d-flex flex-column gap-3">
                <div>
                    <div class="fw-bold text-uppercase small text-muted mb-1">Firma</div>
                    <div class="fs-5 fw-semibold"><?php echo htmlspecialchars($companyName, ENT_QUOTES, 'UTF-8'); ?></div>
                    <div class="text-muted small">Kontrol Paneli</div>
                </div>

                <div>
                    <div class="fw-bold text-uppercase small text-muted mb-2">Modüller</div>
                    <div class="list-group">
                        <?php foreach ($activeModules as $module): ?>
                            <a class="list-group-item list-group-item-action d-flex justify-content-between align-items-center <?php echo $module['is_active'] ? 'active' : ''; ?>"
                               href="<?php echo htmlspecialchars($module['route'], ENT_QUOTES, 'UTF-8'); ?>">
                                <span><?php echo htmlspecialchars($module['label'], ENT_QUOTES, 'UTF-8'); ?></span>
                                <?php if (!empty($module['icon'])): ?>
                                    <span class="opacity-75"><?php echo $module['icon']; ?></span>
                                <?php endif; ?>
                            </a>
                        <?php endforeach; ?>
                    </div>
                </div>

                <div>
                    <div class="fw-bold text-uppercase small text-muted mb-2">Aksiyonlar</div>
                    <div class="list-group">
                        <?php if (can('users.create')): ?>
                            <a class="list-group-item list-group-item-action" href="/users/create">Kullanıcı Oluştur</a>
                        <?php endif; ?>
                        <?php if ($isSuperAdmin): ?>
                            <a class="list-group-item list-group-item-action" href="/super-admin/companies">Super Admin</a>
                        <?php endif; ?>
                        <form action="/logout" method="POST" class="d-grid mt-2">
                            <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars(csrf_token(), ENT_QUOTES, 'UTF-8'); ?>">
                            <button type="submit" class="btn btn-outline-secondary">Çıkış</button>
                        </form>
                    </div>
                </div>
            </div>
        </aside>
        <div class="col px-4 py-3">
            <header class="d-flex flex-wrap align-items-center justify-content-between gap-3 pb-3 mb-3 border-bottom">
                <div>
                    <div class="text-uppercase small text-muted">CRM</div>
                    <h1 class="h4 mb-0"><?php echo htmlspecialchars($title ?? 'CRM', ENT_QUOTES, 'UTF-8'); ?></h1>
                </div>
                <div class="text-end">
                    <div class="fw-semibold"><?php echo htmlspecialchars($userName, ENT_QUOTES, 'UTF-8'); ?></div>
                    <div class="text-muted small"><?php echo htmlspecialchars($companyName, ENT_QUOTES, 'UTF-8'); ?></div>
                </div>
            </header>

            <?php if ($actingCompanyMissing): ?>
                <div class="alert alert-warning d-flex align-items-center gap-2" role="alert">
                    <div class="fw-semibold">Firma seçilmedi.</div>
                    <div class="small mb-0">Super Admin olarak ilerlemek için firma seçimi yapın.</div>
                </div>
            <?php endif; ?>

            <?php echo $content ?? ''; ?>
        </div>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
</body>
</html>
