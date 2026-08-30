{{-- Login page. Full bleed, sharing the page shell of dashboard/home.blade.php. --}}
@extends('layouts.app')

@section('title', 'Sign in')
@section('body-class', 'auth')

@section('content')
    <div class="app-shell">
        <header class="topbar">
            <span class="topbar-brand">Notes</span>
            <div class="topbar-spacer"></div>
        </header>

        <main class="auth-main">
            <div class="auth-body">
                <h1>Welcome back</h1>
                <p class="subtitle">Sign in to open your notes.</p>

                @if (session('status'))
                    <div class="alert">{{ session('status') }}</div>
                @endif

                @if ($errors->any())
                    <div class="alert">
                        We couldn't sign you in:
                        <ul>
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form method="POST" action="{{ route('login') }}" novalidate>
                    {{-- CSRF protection: expands to a hidden _token field holding csrf_token(). --}}
                    @csrf

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
                               autocomplete="current-password"
                               required>
                        @error('password')
                            <p class="field-error">{{ $message }}</p>
                        @enderror
                    </div>

                    <button type="submit" class="btn btn-primary btn-block">Sign in</button>
                </form>

                <p class="auth-footer">
                    Don't have an account?
                    <a href="{{ route('register-form') }}">Create one</a>
                </p>
            </div>
        </main>
    </div>
@endsection
