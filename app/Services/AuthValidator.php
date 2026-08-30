<?php

declare(strict_types=1);

namespace App\Services;

use app\Exceptions\Auth\IncorrectCredentialsException;
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
        $fetchedUser = User::where('email', $email)->first();

        if (!Auth::attempt(['email' => $email, 'password' => $password])) {
            throw new IncorrectCredentialsException();
        }

        return $fetchedUser;
    }

    public function createNewUser(
        string $name,
        string $email,
        string $password,
    ): Authenticatable {

        $user = User::create(['name' => $name, 'email' => $email, 'password' => $password]);
        $user->save();
        return $user;
    }


    public function login(User $user): void {
        Auth::login($user);
    }

    public function logout(): void {
        Auth::logout();
    }
}
