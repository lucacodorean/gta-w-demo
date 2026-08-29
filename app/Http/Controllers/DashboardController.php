<?php

declare(strict_types=1);

namespace App\Http\Controllers;

class DashboardController extends Controller
{
    public function showHome() {
        return view('dashboard.home', [
            'user' => auth()->user(),
        ]);
    }
}
