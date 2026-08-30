<?php

declare(strict_types=1);

namespace App\Http\Controllers;

class DashboardController extends Controller
{
    public function showHome() {

        // TODO: This should be CSRF protected.

        return view('dashboard.home', [
            'user' => auth()->user(),
        ]);
    }
}
