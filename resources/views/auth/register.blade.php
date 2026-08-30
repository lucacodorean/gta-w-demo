{{-- Register page. Full bleed, sharing the page shell of dashboard/home.blade.php. --}}
@extends('layouts.app')

@section('title', 'Create account')
@section('body-class', 'auth')

@section('content')
    <div class="app-shell">
        <header class="topbar">
            <span class="topbar-brand">Notes</span>
            <div class="topbar-spacer"></div>
        </header>

        <main class="auth-main">
            <div class="auth-body">
                <h1>Create your account</h1>
                <p class="subtitle">All your notes, in one place.</p>

                @if (session('status'))
                    <div class="alert">{{ session('status') }}</div>
                @endif

                <form method="POST" action="{{ route('register') }}" novalidate>
                    {{-- CSRF protection: expands to a hidden _token field holding csrf_token(). --}}
                    @csrf

                    <div class="field">
                        <label for="name">Name</label>
                        <input id="name"
                               name="name"
                               value="{{ old('name') }}"
                               autocomplete="name"
                               autofocus
                               required>
                        @error('name')
                        <p class="field-error">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="field">
                        <label for="email">Email</label>
                        <input id="email"
                               type="email"
                               name="email"
                               value="{{ old('email') }}"
                               autocomplete="email"
                               autofocus
                               required>
                        @error('email')
                            <p class="field-error">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="field">
                        <label for="password">Password</label>
                        <input id="password"
                               type="password"
                               name="password"
                               autocomplete="new-password"
                               minlength="8"
                               required>
                        @error('password')
                            <p class="field-error">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="field">
                        <label for="password_confirmation">Confirm password</label>
                        <input id="password_confirmation"
                               type="password"
                               name="password_confirmation"
                               autocomplete="new-password"
                               minlength="8"
                               required>
                        @error('password_confirmation')
                            <p class="field-error">{{ $message }}</p>
                        @enderror
                    </div>

                    <button type="submit" class="btn btn-primary btn-block">Create account</button>
                </form>

                <p class="auth-footer">
                    Already have an account?
                    <a href="{{ route('login-form') }}">Sign in</a>
                </p>
            </div>
        </main>
    </div>
@endsection
