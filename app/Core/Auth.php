<?php

namespace App\Core;

use App\Models\User;
use App\Models\Company;
use App\Models\Permission;
use App\Models\Role;
use App\Services\FeatureService;
use App\Core\Exceptions\AuthException;
use App\Core\Exceptions\DatabaseConnectionException;
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

        return [
            'id' => (int) $_SESSION['user_id'],
            'company_id' => $_SESSION['company_id'] !== null ? (int) $_SESSION['company_id'] : null,
            'name' => (string) ($_SESSION['user_name'] ?? ''),
            'company_name' => (string) ($_SESSION['company_name'] ?? ''),
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
        $companyId = (int) $_SESSION['company_id'];

        return Permission::userHasPermission($userId, $companyId, $permissionKey);
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
}
