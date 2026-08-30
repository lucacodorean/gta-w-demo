<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\User;
use Illuminate\Container\Attributes\Singleton;
use Illuminate\Contracts\Auth\Authenticatable;

#[Singleton]
final readonly class AuthValidator
{
    public function validateLogin(
        string $email,
        string $password,
    ): Authenticatable {
        /// TODO: Validate the login credentials. Throw IncorrectCredentialsException if invalid.
        /// TODO: Remember that the password must be encrypted and it should not be decrypted.
        /// TODO: Validation can be down through password_hash function.
    }

    public function createNewUser(
        string $email,
        string $password,
    ): User {
        /// TODO: Based on RegisterRequest, the email should be already validated if it is unique or not.
        /// TODO: The password has been validated through the custom validation rules established.
        /// TODO: Remember that the password must be encrypted and it should not be decrypted.
        /// TODO: Validation can be down through password_hash function.
        /// TODO: The remaining assignment is to ensure that the user is properly stored.
        /// TODO: Throws InvalidRequestException with code HTTP_UNPROCESSABLE_ENTITY if something goes south.
    }

}
