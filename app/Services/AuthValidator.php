<?php

declare(strict_types=1);

namespace App\Services;

use App\Exceptions\Auth\IncorrectCredentialsException;
use App\Models\User;
use Illuminate\Container\Attributes\Singleton;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Support\Facades\Auth;

#[Singleton]
final readonly class AuthValidator
{
    public function validateLogin(
        string $email,
        string $password,
    ): Authenticatable {
        if (!Auth::attempt(['email' => $email, 'password' => $password])) {
            throw new IncorrectCredentialsException();
        }

        return Auth::user();
    }

    public function createNewUser(
        string $name,
        string $email,
        string $password,
    ): Authenticatable {
        $user = User::create(['name' => $name, 'email' => $email, 'password' => $password]);

        Auth::login($user);

        return $user;
    }
}
