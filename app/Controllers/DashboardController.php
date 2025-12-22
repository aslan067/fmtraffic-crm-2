<?php

namespace App\Controllers;

use App\Core\Auth;

class DashboardController
{
    public function index(): void
    {
        $user = Auth::user();
        view('dashboard', ['user' => $user]);
    }
}
