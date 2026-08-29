<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use app\Exceptions\Auth\IncorrectCredentialsException;
use App\Helper\Logger;
use App\Helper\NotificationSender;
use App\Http\Requests\Auth\LoginRequest;
use App\Services\AuthValidator;
use Illuminate\Http\RedirectResponse;

class AuthController extends Controller
{
    use NotificationSender, Logger;

    public function __construct(
        private readonly AuthValidator $authValidator
    ) {
        // Empty on purpose
    }

    public function showLogin() {

        // This needs to be CSRF protected.
        return view('auth.login', []);
    }



    public function login(LoginRequest $request): RedirectResponse
    {
        if (!$request->validated()) {
            // TODO: Send notification in notification bag regarding invalid request.
        }

        try {
            $user = $this->authValidator->validateLogin(
                email: $request['email'],
                password: $request['password']
            );

            auth()->login($user);

            return redirect()->route('home', [
                'user' => $user
            ]);
        } catch (IncorrectCredentialsException $exception) {
            // TODO Send notification in notification bag regarding incorrect credentials.
        }

        return redirect()->route('login');
    }
}
