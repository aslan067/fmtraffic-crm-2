<?php

namespace App\Core;

use App\Models\User;
use App\Models\Company;
use App\Models\Role;
use App\Models\Permission;
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

    /**
     * Check if the current user has the given role by name.
     */
    public static function hasRole(string $roleName): bool
    {
        if (!self::check()) {
            return false;
        }

        $pdo = DB::getConnection();
        $stmt = $pdo->prepare('SELECT COUNT(*) FROM user_roles ur INNER JOIN roles r ON ur.role_id = r.id WHERE ur.user_id = :user_id AND r.name = :role_name AND (r.company_id = :company_id OR r.company_id IS NULL)');
        $stmt->execute([
            ':user_id' => $_SESSION['user_id'],
            ':role_name' => $roleName,
            ':company_id' => $_SESSION['company_id'],
        ]);

        return (bool) $stmt->fetchColumn();
    }

    /**
     * Check if the current user has a permission via roles.
     */
    public static function hasPermission(string $permissionKey): bool
    {
        if (!self::check()) {
            return false;
        }

        $pdo = DB::getConnection();
        $sql = 'SELECT COUNT(*) 
                FROM user_roles ur 
                INNER JOIN roles r ON ur.role_id = r.id 
                INNER JOIN role_permissions rp ON rp.role_id = r.id 
                INNER JOIN permissions p ON p.id = rp.permission_id 
                WHERE ur.user_id = :user_id 
                  AND p.`key` = :permission_key 
                  AND (r.company_id = :company_id OR r.company_id IS NULL)';
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            ':user_id' => $_SESSION['user_id'],
            ':permission_key' => $permissionKey,
            ':company_id' => $_SESSION['company_id'],
        ]);

        return (bool) $stmt->fetchColumn();
    }
}
