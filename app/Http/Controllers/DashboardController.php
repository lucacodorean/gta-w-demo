<?php

declare(strict_types=1);

namespace App\Http\Controllers;

class DashboardController extends Controller
{
    public function showHome() {

        // TODO: This should be CSRF protected.

        $user = auth()->user();

        return view('dashboard.home', [
            'user' => $user,
            'notes' => $user->notes()->orderBy('created_at', 'desc')->get()
        ]);
    }
}
