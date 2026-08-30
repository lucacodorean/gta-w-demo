<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Enums\HttpCodes;
use app\Exceptions\Auth\IncorrectCredentialsException;
use App\Helper\Logger;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\RegisterRequest;
use App\Models\User;
use App\Services\AuthValidator;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

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
        if (!$request->validated()) {
            $this->log('Invalid request.', $request, HttpCodes::HTTP_UNPROCESSABLE_ENTITY->value);
        }

        try {
            /**@var User $user **/
            $user = $this->authValidator->validateLogin($request['email'], $request['password']);

            return $this->executeLogin($user, $request);
        } catch (IncorrectCredentialsException $exception) {
            $this->log($exception->getMessage(), $request, $exception->getCode());

            return back()->withErrors([
                'general' => 'The provided credentials do not match our records.',
            ])->onlyInput('email');
        }
    }

    public function register(RegisterRequest $request): RedirectResponse {
        if (!$request->validated()) {
            $this->log('Invalid request.', $request, HttpCodes::HTTP_UNPROCESSABLE_ENTITY->value);
        }

        /**@var User $user **/
        $user = $this->authValidator->createNewUser($request['name'], $request['email'], $request['password']);

        return $this->executeLogin($user, $request);
    }

    public function logout(Request $request): RedirectResponse {
        $this->log(
            'Session ended by the user.',
            $request,
            HttpCodes::HTTP_OK->value,
        );
        $this->authValidator->logout();

        return redirect()->route('login');
    }

    private function executeLogin(User $user, Request $request): RedirectResponse
    {
        $this->authValidator->login($user);

        $this->log(
            'Login successfully made.',
            $request,
            HttpCodes::HTTP_REDIRECTED->value,
            [
                'redirect-url' => route('home')
            ]
        );

        return redirect()->route('home', [
            'user' => $user,
            'notes' => $user->notes()->orderBy('created_at', 'desc')->get()
        ]);
    }
}
