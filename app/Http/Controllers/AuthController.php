<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Enums\HttpCodes;
use App\Enums\LogEvents;
use App\Exceptions\Auth\IncorrectCredentialsException;
use App\Helper\Logger;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\RegisterRequest;
use App\Services\AuthValidator;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    use Logger;

    public function __construct(
        private readonly AuthValidator $authValidator
    ) {
        // Empty on purpose
    }

    public function showLogin() {
        return view('auth.login');
    }

    public function showRegister() {
        return view('auth.register');
    }


    public function login(LoginRequest $request): RedirectResponse
    {
        try {
            $this->authValidator->validateLogin(
                $request->validated('email'),
                $request->validated('password'),
            );

            return $this->executeLogin($request);
        } catch (IncorrectCredentialsException $exception) {
            // The event carries the wording; the exception message stays as context.
            $this->log(
                LogEvents::LOGIN_FAILED,
                $request,
                $exception->getCode(),
                ['reason' => $exception->getMessage()],
                'warning',
            );

            return back()->withErrors([
                'general' => 'The provided credentials do not match our records.',
            ])->onlyInput('email');
        }
    }

    public function register(RegisterRequest $request): RedirectResponse {
        $this->authValidator->createNewUser(
            $request->validated('name'),
            $request->validated('email'),
            $request->validated('password'),
        );

        return $this->executeLogin($request);
    }

    public function logout(Request $request): RedirectResponse {
        $this->log(
            LogEvents::LOGOUT,
            $request,
            HttpCodes::HTTP_OK->value,
        );

        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }

    private function executeLogin(Request $request): RedirectResponse
    {
        $this->log(
            LogEvents::LOGIN_SUCCEEDED,
            $request,
            HttpCodes::HTTP_REDIRECTED->value,
            [
                'redirect-url' => route('home')
            ]
        );

        return redirect()->route('home');
    }
}
