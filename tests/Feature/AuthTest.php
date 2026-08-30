<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;
use Tests\TestCase;

class AuthTest extends TestCase
{
    use RefreshDatabase;

    private function makeUser(string $password = 'password123'): User
    {
        return User::create([
            'name' => 'Test User',
            'email' => 'user@example.com',
            'password' => $password,
        ]);
    }

    private function authSessionKey(): string
    {
        return Auth::guard('web')->getName();
    }

    public function test_login_redirects_to_home_and_survives_into_the_session(): void
    {
        $user = $this->makeUser();

        $response = $this->post(route('login'), [
            'email' => 'user@example.com',
            'password' => 'password123',
        ]);

        $response->assertRedirect(route('home'));
        $this->assertAuthenticatedAs($user);

        // The guard keeps the user in memory for the rest of the request, so this
        // assertion -- not assertAuthenticatedAs -- is what proves the login is not
        // flushed before the redirect is followed.
        $this->assertSame($user->id, session($this->authSessionKey()));
    }

    public function test_dashboard_renders_for_an_authenticated_user(): void
    {
        // Note: this deliberately uses actingAs rather than posting the login form.
        // Within a single test the guard keeps state across requests, so a
        // post-then-get would still pass even if login never reached the session --
        // test_login_redirects_to_home_and_survives_into_the_session covers that.
        $user = $this->makeUser();

        $this->actingAs($user)->get(route('home'))->assertOk();
    }

    public function test_login_redirect_carries_no_model_data_in_the_query_string(): void
    {
        $this->makeUser();

        $location = $this->post(route('login'), [
            'email' => 'user@example.com',
            'password' => 'password123',
        ])->headers->get('Location');

        $this->assertStringNotContainsString('?', (string) $location);
    }

    public function test_login_with_a_wrong_password_fails_without_authenticating(): void
    {
        $this->makeUser();

        $response = $this->from(route('login-form'))->post(route('login'), [
            'email' => 'user@example.com',
            'password' => 'wrong-password',
        ]);

        $response->assertRedirect(route('login-form'));
        $response->assertSessionHasErrors('general');
        $this->assertGuest();
    }

    public function test_unknown_email_gives_the_same_generic_error_as_a_wrong_password(): void
    {
        $response = $this->from(route('login-form'))->post(route('login'), [
            'email' => 'nobody@example.com',
            'password' => 'password123',
        ]);

        // No "no account for this e-mail" branch: an unknown address must be
        // indistinguishable from a bad password, or the form enumerates users.
        $response->assertSessionHasErrors('general');
        $response->assertSessionDoesntHaveErrors('email');
        $this->assertGuest();
    }

    public function test_register_creates_the_user_and_logs_them_in(): void
    {
        $response = $this->post(route('register'), [
            'name' => 'New User',
            'email' => 'new@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $response->assertRedirect(route('home'));
        $this->assertDatabaseHas('users', ['email' => 'new@example.com']);

        $user = User::where('email', 'new@example.com')->firstOrFail();
        $this->assertSame($user->id, session($this->authSessionKey()));
        $this->assertNotSame('password123', $user->password);
    }

    public function test_register_rejects_a_duplicate_email(): void
    {
        $this->makeUser();

        $this->from(route('register-form'))->post(route('register'), [
            'name' => 'Impostor',
            'email' => 'user@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ])->assertSessionHasErrors('email');

        $this->assertGuest();
    }

    public function test_logout_clears_the_session_and_rotates_the_csrf_token(): void
    {
        $user = $this->makeUser();

        $this->actingAs($user)->get(route('home'))->assertOk();
        $tokenBefore = session()->token();

        $this->actingAs($user)->post(route('logout'))->assertRedirect(route('login'));

        $this->assertGuest();
        $this->assertNull(session($this->authSessionKey()));
        $this->assertNotSame($tokenBefore, session()->token());
    }

    public function test_guests_cannot_reach_the_dashboard(): void
    {
        $this->get(route('home'))->assertRedirect(route('login'));
    }

    public function test_authenticated_users_are_kept_off_the_auth_forms(): void
    {
        $user = $this->makeUser();

        $this->actingAs($user)->get(route('login-form'))->assertRedirect(route('home'));
        $this->actingAs($user)->get(route('register-form'))->assertRedirect(route('home'));
    }
}
