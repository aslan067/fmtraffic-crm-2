<?php

namespace App\Core;

use App\Models\User;
use App\Models\Company;
use App\Models\Permission;
use App\Models\Role;
use App\Services\FeatureService;
use App\Core\Exceptions\AuthException;
use App\Core\Exceptions\DatabaseConnectionException;
use App\Core\ModuleRegistry;
use App\Core\PermissionVersion;
use Throwable;

class Auth
{
    private static ?FeatureService $featureService = null;

    public static function startSession(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    /**
     * Attempt login with email and password.
     */
    public static function attempt(string $email, string $password): bool
    {
        try {
            $user = User::findByEmail($email);

            if (!$user) {
                throw new AuthException(
                    'user_not_found',
                    'Kullanıcı bulunamadı. E-posta adresini kontrol edin veya Super Admin henüz oluşturulmadıysa ekleyin.'
                );
            }

            if (!password_verify($password, $user['password_hash'])) {
                throw new AuthException('invalid_password', 'Şifre yanlış.');
            }

            if (($user['status'] ?? '') !== 'active') {
                throw new AuthException('user_inactive', 'Kullanıcı pasif. Lütfen yöneticinizle iletişime geçin.');
            }

            $companyId = $user['company_id'] !== null ? (int) $user['company_id'] : null;
            $company = $companyId ? Company::findById($companyId) : null;

            $_SESSION['user_id'] = (int) $user['id'];
            $_SESSION['company_id'] = $companyId;
            $_SESSION['user_name'] = $user['name'];
            $_SESSION['company_name'] = $company['name'] ?? '';
            $_SESSION['is_super_admin'] = (bool) $user['is_super_admin'];

            if ($companyId !== null) {
                self::setActingCompany($companyId, $company['name'] ?? '');
                self::reloadPermissionCache((int) $user['id'], $companyId);
            } else {
                self::clearPermissionCache();
                self::clearActingCompany();
            }

            return true;
        } catch (DatabaseConnectionException $e) {
            error_log('Database connection error during authentication: ' . $e->getMessage());
            throw new AuthException(
                'db_connection',
                'Sistem geçici olarak kullanılamıyor (DB bağlantısı yok).',
                0,
                $e
            );
        } catch (AuthException $e) {
            throw $e;
        } catch (Throwable $e) {
            error_log('Auth error: ' . $e->getMessage());
            throw new AuthException(
                'system_error',
                'Beklenmeyen bir sistem hatası oluştu. Lütfen tekrar deneyin.',
                0,
                $e
            );
        }
    }

    public static function logout(): void
    {
        self::clearPermissionCache();
        self::clearActingCompany();
        $_SESSION = [];
        if (ini_get('session.use_cookies')) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000, $params['path'], $params['domain'], $params['secure'], $params['httponly']);
        }
        session_destroy();
    }

    public static function check(): bool
    {
        if (!isset($_SESSION['user_id'])) {
            return false;
        }

        if (!empty($_SESSION['is_super_admin'])) {
            return true;
        }

        return array_key_exists('company_id', $_SESSION) && $_SESSION['company_id'] !== null;
    }

    public static function user(): ?array
    {
        if (!self::check()) {
            return null;
        }

        $actingCompanyId = self::actingCompanyId();

        return [
            'id' => (int) $_SESSION['user_id'],
            'company_id' => $actingCompanyId,
            'name' => (string) ($_SESSION['user_name'] ?? ''),
            'company_name' => self::actingCompanyName(),
            'is_super_admin' => (bool) ($_SESSION['is_super_admin'] ?? false),
        ];
    }

    public static function hasRole(string $roleName): bool
    {
        if (self::isSuperAdmin()) {
            return true;
        }

        if (!self::check()) {
            return false;
        }

        $userId = (int) $_SESSION['user_id'];
        $companyId = (int) $_SESSION['company_id'];

        $roles = Role::getRolesForUser($userId, $companyId);

        foreach ($roles as $role) {
            if (strcasecmp($role['name'], $roleName) === 0) {
                return true;
            }
        }

        return false;
    }

    public static function hasPermission(string $permissionKey): bool
    {
        if (self::isSuperAdmin()) {
            return true;
        }

        if (!self::check()) {
            return false;
        }

        $userId = (int) $_SESSION['user_id'];
        $companyId = (int) self::actingCompanyId();

        $permissions = self::ensurePermissionCache($userId, $companyId);
        $normalized = self::normalizePermissionKey($permissionKey);

        foreach ($permissions as $key) {
            if ($normalized === self::normalizePermissionKey($key)) {
                return true;
            }
        }

        // Cache is expected to be authoritative, but fall back to a direct check in case of
        // recent permission changes and refresh the cache when discovered.
        $hasPermission = Permission::userHasPermission($userId, $companyId, $permissionKey);

        if ($hasPermission) {
            $_SESSION['permissions'][] = $permissionKey;
        }

        return $hasPermission;
    }

    public static function can(string $permissionKey): bool
    {
        return self::hasPermission($permissionKey);
    }

    public static function hasFeature(string $featureKey): bool
    {
        if (self::isSuperAdmin()) {
            return true;
        }

        if (!self::check()) {
            return false;
        }

        $companyId = (int) $_SESSION['company_id'];

        return self::featureService()->companyHasFeature($companyId, $featureKey);
    }

    public static function canAccess(string $featureKey, string $permissionKey): bool
    {
        if (self::isSuperAdmin()) {
            return true;
        }

        return self::hasFeature($featureKey) && self::can($permissionKey);
    }

    public static function canAccessModule(string $moduleKey): bool
    {
        if (self::isSuperAdmin()) {
            return true;
        }

        if (!self::check()) {
            return false;
        }

        $module = ModuleRegistry::get($moduleKey);
        if ($module === null) {
            return false;
        }

        $featureKey = (string) ($module['feature'] ?? '');
        $permissionKey = (string) ($module['permission'] ?? '');

        if ($permissionKey !== '' && self::hasPermission($permissionKey)) {
            // Teklif modülü için permission yeterli olmalı.
            if ($moduleKey === 'offers') {
                return true;
            }
        }

        if ($featureKey !== '' && $permissionKey !== '') {
            return self::hasFeature($featureKey) && self::hasPermission($permissionKey);
        }

        return false;
    }

    public static function isSuperAdmin(): bool
    {
        return !empty($_SESSION['is_super_admin']);
    }

    private static function featureService(): FeatureService
    {
        if (!self::$featureService instanceof FeatureService) {
            self::$featureService = new FeatureService();
        }

        return self::$featureService;
    }

    public static function setActingCompany(?int $companyId, string $companyName = ''): void
    {
        if ($companyId === null) {
            self::clearActingCompany();
            return;
        }

        $_SESSION['acting_company_id'] = $companyId;
        $_SESSION['acting_company_name'] = $companyName;
    }

    public static function clearActingCompany(): void
    {
        unset($_SESSION['acting_company_id'], $_SESSION['acting_company_name']);
    }

    public static function actingCompanyId(): ?int
    {
        if (isset($_SESSION['acting_company_id'])) {
            return (int) $_SESSION['acting_company_id'];
        }

        if (isset($_SESSION['company_id']) && $_SESSION['company_id'] !== null) {
            return (int) $_SESSION['company_id'];
        }

        return null;
    }

    public static function actingCompanyName(): string
    {
        if (isset($_SESSION['acting_company_name'])) {
            return (string) $_SESSION['acting_company_name'];
        }

        return (string) ($_SESSION['company_name'] ?? '');
    }

    private static function ensurePermissionCache(int $userId, int $companyId): array
    {
        $cachedCompanyId = $_SESSION['permissions_company_id'] ?? null;
        $cachedPermissions = $_SESSION['permissions'] ?? null;
        $cachedVersion = $_SESSION['permissions_version'] ?? null;
        $currentVersion = PermissionVersion::current();

        if (
            !is_array($cachedPermissions)
            || $cachedCompanyId !== $companyId
            || $cachedVersion !== $currentVersion
        ) {
            return self::reloadPermissionCache($userId, $companyId);
        }

        return $cachedPermissions;
    }

    private static function reloadPermissionCache(int $userId, int $companyId): array
    {
        $permissions = self::fetchPermissions($userId, $companyId);

        $_SESSION['permissions'] = $permissions;
        $_SESSION['permissions_company_id'] = $companyId;
        $_SESSION['permissions_version'] = PermissionVersion::current();

        return $permissions;
    }

    private static function fetchPermissions(int $userId, int $companyId): array
    {
        $roles = Role::getRolesForUser($userId, $companyId);
        $permissions = [];

        foreach ($roles as $role) {
            $permissionKeys = Role::getPermissionKeys((int) $role['id']);
            foreach ($permissionKeys as $key) {
                $permissions[$key] = $key;
            }
        }

        // Failsafe: if no permissions were resolved for an active user, refresh directly from DB.
        if (empty($permissions)) {
            $directPermissions = Permission::getByUserAndCompany($userId, $companyId);
            foreach ($directPermissions as $permission) {
                $key = (string) ($permission['key'] ?? '');
                if ($key !== '') {
                    $permissions[$key] = $key;
                }
            }
        }

        return array_values($permissions);
    }

    private static function clearPermissionCache(): void
    {
        unset($_SESSION['permissions'], $_SESSION['permissions_company_id'], $_SESSION['permissions_version']);
    }

    private static function normalizePermissionKey(string $key): string
    {
        return strtolower(trim($key));
    }
}
