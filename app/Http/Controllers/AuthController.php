<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Enums\HttpCodes;
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
            $this->log($exception->getMessage(), $request, $exception->getCode(), level: 'warning');

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
            'Session ended by the user.',
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
            'Login successfully made.',
            $request,
            HttpCodes::HTTP_REDIRECTED->value,
            [
                'redirect-url' => route('home')
            ]
        );

        return redirect()->route('home');
    }
}
