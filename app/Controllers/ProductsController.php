<?php

namespace App\Controllers;

use App\Core\Auth;

class ProductsController
{
    public function index(): void
    {
        $user = Auth::user();
        view('products', ['user' => $user]);
    }
}
