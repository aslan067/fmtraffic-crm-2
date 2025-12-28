<?php

namespace App\Controllers;

use App\Core\Auth;
use App\Core\ModuleRegistry;

class DashboardController
{
    public function index(): void
    {
        $user = Auth::user();
        $modules = ModuleRegistry::all();
        view('dashboard', ['user' => $user, 'modules' => $modules]);
    }
}
