<?php

namespace App\Controllers;

use App\Core\Auth;
use App\Models\User;
use App\Services\LimitService;

class UsersController
{
    private LimitService $limitService;

    public function __construct()
    {
        $this->limitService = new LimitService();
    }

    public function create(): void
    {
        $flash = $this->pullFlash();
        view('users/create', ['flash' => $flash]);
    }

    public function store(): void
    {
        $token = $_POST['csrf_token'] ?? '';
        if (!verify_csrf_token($token)) {
            $this->setFlash('error', 'Geçersiz oturum doğrulaması.');
            redirect('/users/create');
        }

        $currentUser = Auth::user();
        $companyId = $currentUser['company_id'] ?? null;

        if ($companyId === null) {
            $this->setFlash('error', 'Super Admin kullanıcı eklemek için bir firma bağlamı seçmelidir.');
            redirect('/users/create');
        }

        if (!$this->limitService->canAddUser((int) $companyId)) {
            $this->setFlash('error', 'Paket kullanıcı limitiniz dolmuştur. Paket yükseltiniz.');
            redirect('/users/create');
        }

        $name = trim($_POST['name'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $password = (string) ($_POST['password'] ?? '');

        if ($name === '' || $email === '' || $password === '') {
            $this->setFlash('error', 'Ad, e-posta ve şifre gereklidir.');
            redirect('/users/create');
        }

        $passwordHash = password_hash($password, PASSWORD_BCRYPT);

        User::create([
            'company_id' => $companyId,
            'name' => $name,
            'email' => $email,
            'password_hash' => $passwordHash,
            'status' => 'active',
            'is_super_admin' => 0,
        ]);

        $this->setFlash('success', 'Kullanıcı başarıyla oluşturuldu.');
        redirect('/users/create');
    }

    private function setFlash(string $key, string $message): void
    {
        $_SESSION['flash'][$key] = $message;
    }

    private function pullFlash(): array
    {
        $flash = $_SESSION['flash'] ?? [];
        unset($_SESSION['flash']);

        return $flash;
    }
}
