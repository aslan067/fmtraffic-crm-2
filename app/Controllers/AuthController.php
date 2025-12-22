<?php

namespace App\Controllers;

use App\Core\Auth;

class AuthController
{
    public function showLogin(): void
    {
        if (Auth::check()) {
            redirect('/dashboard');
        }

        $error = $_SESSION['error'] ?? null;
        unset($_SESSION['error']);

        view('login', ['error' => $error]);
    }

    public function login(): void
    {
        $email = trim($_POST['email'] ?? '');
        $password = (string) ($_POST['password'] ?? '');
        $token = $_POST['csrf_token'] ?? '';

        if (!verify_csrf_token($token)) {
            $_SESSION['error'] = 'Geçersiz oturum doğrulaması. Lütfen tekrar deneyin.';
            redirect('/login');
        }

        if ($email === '' || $password === '') {
            $_SESSION['error'] = 'E-posta ve şifre gereklidir.';
            redirect('/login');
        }

        $authenticated = Auth::attempt($email, $password);

        if (!$authenticated) {
            $_SESSION['error'] = 'Giriş başarısız. Bilgilerinizi kontrol edin.';
            redirect('/login');
        }

        redirect('/dashboard');
    }

    public function logout(): void
    {
        $token = $_POST['csrf_token'] ?? '';
        if (!verify_csrf_token($token)) {
            $_SESSION['error'] = 'Geçersiz oturum doğrulaması.';
            redirect('/dashboard');
        }

        Auth::logout();
        redirect('/login');
    }
}
