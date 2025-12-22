<?php

namespace App\Core;

use App\Models\User;
use App\Models\Company;
use App\Models\Permission;
use App\Models\Role;
use Throwable;

class Auth
{
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
                return false;
            }

            if (!password_verify($password, $user['password_hash'])) {
                return false;
            }

            $company = Company::findById((int) $user['company_id']);

            $_SESSION['user_id'] = (int) $user['id'];
            $_SESSION['company_id'] = (int) $user['company_id'];
            $_SESSION['user_name'] = $user['name'];
            $_SESSION['company_name'] = $company['name'] ?? '';

            return true;
        } catch (Throwable $e) {
            error_log('Auth error: ' . $e->getMessage());
            return false;
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
        return isset($_SESSION['user_id'], $_SESSION['company_id']);
    }

    public static function user(): ?array
    {
        if (!self::check()) {
            return null;
        }

        return [
            'id' => (int) $_SESSION['user_id'],
            'company_id' => (int) $_SESSION['company_id'],
            'name' => (string) ($_SESSION['user_name'] ?? ''),
            'company_name' => (string) ($_SESSION['company_name'] ?? ''),
        ];
    }

    public static function hasRole(string $roleName): bool
    {
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
        if (!self::check()) {
            return false;
        }

        $userId = (int) $_SESSION['user_id'];
        $companyId = (int) $_SESSION['company_id'];

        return Permission::userHasPermission($userId, $companyId, $permissionKey);
    }
}
