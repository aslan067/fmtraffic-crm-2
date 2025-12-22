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

        $error = getFlash('error');
        $success = getFlash('success');

        view('login', ['error' => $error, 'success' => $success]);
    }

    public function login(): void
    {
        $email = trim($_POST['email'] ?? '');
        $password = (string) ($_POST['password'] ?? '');
        $token = $_POST['csrf_token'] ?? '';

        if (!verify_csrf_token($token)) {
            setFlash('error', 'Geçersiz oturum doğrulaması. Lütfen tekrar deneyin.');
            redirect('/login');
        }

        if ($email === '' || $password === '') {
            setFlash('error', 'E-posta ve şifre gereklidir.');
            redirect('/login');
        }

        try {
            Auth::attempt($email, $password);
        } catch (\App\Core\Exceptions\AuthException $e) {
            setFlash('error', $e->getMessage());
            redirect('/login');
        } catch (\Throwable $e) {
            error_log('Login unexpected error: ' . $e->getMessage());
            setFlash('error', 'Beklenmeyen sistem hatası. Lütfen tekrar deneyin.');
            redirect('/login');
        }

        redirect('/dashboard');
    }

    public function logout(): void
    {
        $token = $_POST['csrf_token'] ?? '';
        if (!verify_csrf_token($token)) {
            setFlash('error', 'Geçersiz oturum doğrulaması.');
            redirect('/dashboard');
        }

        Auth::logout();
        setFlash('success', 'Oturum başarıyla kapatıldı.');
        redirect('/login');
    }
}
