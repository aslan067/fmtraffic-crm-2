<?php
// Shared helper functions for the application

declare(strict_types=1);

/**
 * Load environment variables from a .env file if it exists.
 */
function loadEnv(string $path): void
{
    if (!file_exists($path)) {
        return;
    }

    $lines = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

    foreach ($lines as $line) {
        if (str_starts_with(trim($line), '#')) {
            continue;
        }

        [$name, $value] = array_map('trim', explode('=', $line, 2));
        $_ENV[$name] = $value;
        putenv(sprintf('%s=%s', $name, $value));
    }
}

/**
 * Render a view file with compacted data.
 */
function view(string $template, array $data = []): void
{
    extract($data, EXTR_OVERWRITE);
    require BASE_PATH . '/app/Views/' . $template . '.php';
}

/**
 * Redirect helper.
 */
function redirect(string $path): void
{
    header('Location: ' . $path);
    exit;
}

/**
 * Generate or fetch the CSRF token for the session.
 */
function csrf_token(): string
{
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }

    return $_SESSION['csrf_token'];
}

/**
 * Validate incoming CSRF token.
 */
function verify_csrf_token(?string $token): bool
{
    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], (string) $token);
}

/**
 * Flash message helpers.
 */
function setFlash(string $key, string $message): void
{
    if (!isset($_SESSION['flash']) || !is_array($_SESSION['flash'])) {
        $_SESSION['flash'] = [];
    }

    $_SESSION['flash'][$key] = $message;
}

/**
 * @return array<string, string>|string|null
 */
function getFlash(?string $key = null)
{
    if (!isset($_SESSION['flash']) || !is_array($_SESSION['flash'])) {
        return $key === null ? [] : null;
    }

    if ($key !== null) {
        if (!array_key_exists($key, $_SESSION['flash'])) {
            return null;
        }

        $message = $_SESSION['flash'][$key];
        unset($_SESSION['flash'][$key]);

        if ($_SESSION['flash'] === []) {
            unset($_SESSION['flash']);
        }

        return $message;
    }

    $messages = $_SESSION['flash'];
    unset($_SESSION['flash']);

    return $messages;
}

/**
 * View helper: check permission.
 */
function can(string $permission): bool
{
    return \App\Core\Auth::can($permission);
}

/**
 * View helper: check feature availability.
 */
function feature(string $feature): bool
{
    return \App\Core\Auth::hasFeature($feature);
}

/**
 * View helper: feature + permission chain.
 */
function canAccess(string $feature, string $permission): bool
{
    return \App\Core\Auth::canAccess($feature, $permission);
}

/**
 * View helper: module-based access control.
 */
function canAccessModule(string $moduleKey): bool
{
    return \App\Core\Auth::canAccessModule($moduleKey);
}

/**
 * Build navigation list from registered modules and permissions.
 *
 * @return array<int, array{key:string,label:string,route:string,icon:string,is_active:bool}>
 */
function navigationModules(): array
{
    $modules = \App\Core\ModuleRegistry::all();
    $currentPath = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/';
    $icons = [
        'products' => '📦',
        'caris' => '👥',
        'offers' => '📑',
        'sales' => '💰',
    ];

    $items = [];
    foreach ($modules as $moduleKey => $moduleConfig) {
        $route = (string) ($moduleConfig['route'] ?? '');
        $label = (string) ($moduleConfig['label'] ?? '');

        if ($route === '' || $label === '') {
            continue;
        }

        if (!canAccessModule((string) $moduleKey)) {
            continue;
        }

        $items[] = [
            'key' => (string) $moduleKey,
            'label' => $label,
            'route' => $route,
            'icon' => $icons[$moduleKey] ?? '📁',
            'is_active' => str_starts_with($currentPath, $route),
        ];
    }

    return $items;
}
