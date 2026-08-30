<?php

namespace App\Providers;

use App\Models\Note;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Gate::define('operate-note', fn (Authenticatable $user, Note $noteId) => $user->id === $noteId->user_id);
    }

}
