<?php

use App\Core\Auth;
use App\Core\ModuleRegistry;

$user = Auth::user();
$modules = ModuleRegistry::all();

$companyName = $user['company_name'] ?? 'Şirket';
$userName = $user['name'] ?? 'Kullanıcı';
$isSuperAdmin = !empty($user['is_super_admin']);

$moduleIcons = [
    'products' => '📦',
    'caris' => '👥',
    'offers' => '📑',
    'sales' => '💰',
];
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($title ?? 'CRM', ENT_QUOTES, 'UTF-8'); ?></title>
    <link rel="stylesheet" href="/assets/css/app.css">
</head>
<body>
<div class="app-shell">
    <aside class="sidebar">
        <div class="sidebar__brand">
            <div class="brand-name"><?php echo htmlspecialchars($companyName, ENT_QUOTES, 'UTF-8'); ?></div>
            <div class="brand-sub">Kontrol Paneli</div>
        </div>

        <nav class="sidebar__nav">
            <div class="nav-label">Modüller</div>
            <ul>
                <?php foreach ($modules as $moduleKey => $moduleConfig): ?>
                    <?php
                    $route = (string) ($moduleConfig['route'] ?? '');
                    $label = (string) ($moduleConfig['label'] ?? '');
                    $icon = $moduleIcons[$moduleKey] ?? '📁';
                    ?>
                    <?php if ($route !== '' && $label !== '' && canAccessModule((string) $moduleKey)): ?>
                        <li>
                            <a class="nav-link" href="<?php echo htmlspecialchars($route, ENT_QUOTES, 'UTF-8'); ?>">
                                <span class="icon"><?php echo $icon; ?></span>
                                <span><?php echo htmlspecialchars($label, ENT_QUOTES, 'UTF-8'); ?></span>
                            </a>
                        </li>
                    <?php endif; ?>
                <?php endforeach; ?>
            </ul>
        </nav>

        <div class="sidebar__extras">
            <div class="nav-label">Aksiyonlar</div>
            <ul>
                <?php if (can('users.create')): ?>
                    <li><a class="nav-link" href="/users/create">👤 Kullanıcı Oluştur</a></li>
                <?php endif; ?>
                <?php if ($isSuperAdmin): ?>
                    <li><a class="nav-link" href="/super-admin/companies">🛠️ Super Admin</a></li>
                <?php endif; ?>
            </ul>
        </div>
    </aside>

    <div class="main">
        <header class="app-header">
            <div class="title"><?php echo htmlspecialchars($title ?? 'CRM', ENT_QUOTES, 'UTF-8'); ?></div>
            <div class="user-meta">
                <span class="user-name"><?php echo htmlspecialchars($userName, ENT_QUOTES, 'UTF-8'); ?></span>
                <span><?php echo htmlspecialchars($companyName, ENT_QUOTES, 'UTF-8'); ?></span>
                <form action="/logout" method="POST">
                    <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars(csrf_token(), ENT_QUOTES, 'UTF-8'); ?>">
                    <button type="submit" class="button ghost">Çıkış</button>
                </form>
            </div>
        </header>
        <main class="main-content">
            <?php echo $content ?? ''; ?>
        </main>
    </div>
</div>
</body>
</html>
