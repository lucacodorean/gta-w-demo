<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'Notes') &middot; {{ config('app.name', 'Laravel') }}</title>

    {{-- Vite assets are only pulled in once the front-end has been built (npm run build / npm run dev). --}}
    @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    @endif

    <style>
        :root {
            --bg: #ffffff;
            --chrome: #f6f5f3;
            --sidebar: #efeeea;
            --border: #dcdad4;
            --text: #1d1d1f;
            --text-muted: #86868b;
            --accent: #e6b422;
            --accent-soft: #f7e6a8;
            --danger: #d7263d;
        }

        @media (prefers-color-scheme: dark) {
            :root {
                --bg: #1e1e1e;
                --chrome: #2b2b2d;
                --sidebar: #252527;
                --border: #3a3a3c;
                --text: #f5f5f7;
                --text-muted: #98989d;
                --accent: #e6b422;
                --accent-soft: #4a3d16;
            }
        }

        * { box-sizing: border-box; }

        html, body {
            height: 100%;
            margin: 0;
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Instrument Sans', 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;
            font-size: 14px;
            color: var(--text);
            background: var(--bg);
            -webkit-font-smoothing: antialiased;
        }

        a { color: inherit; }

        /* ---------- page shell (shared: auth + dashboard) ---------- */

        .app-shell {
            display: flex;
            flex-direction: column;
            flex: 1 1 auto;
            min-width: 0;
            min-height: 100vh;
            background: var(--bg);
        }

        .topbar {
            display: flex;
            align-items: center;
            gap: 12px;
            flex: 0 0 auto;
            height: 52px;
            padding: 0 20px;
            background: var(--chrome);
            border-bottom: 1px solid var(--border);
        }

        .topbar form { display: flex; }

        .topbar-brand {
            font-size: 15px;
            font-weight: 600;
            letter-spacing: -.01em;
        }

        .topbar-spacer { flex: 1 1 auto; }

        .topbar-meta {
            font-size: 12px;
            color: var(--text-muted);
        }

        .topbar-link {
            font-size: 13px;
            font-weight: 500;
            color: var(--text-muted);
            text-decoration: none;
        }

        .topbar-link:hover { color: var(--text); }

        /* ---------- buttons & fields ---------- */

        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
            padding: 7px 14px;
            font: inherit;
            font-weight: 500;
            color: var(--text);
            background: var(--bg);
            border: 1px solid var(--border);
            border-radius: 8px;
            cursor: pointer;
            text-decoration: none;
        }

        .btn:hover { background: var(--sidebar); }

        .btn-primary {
            color: #1d1d1f;
            background: var(--accent);
            border-color: transparent;
        }

        .btn-primary:hover { filter: brightness(1.06); background: var(--accent); }

        .btn-block { width: 100%; }

        .btn-ghost {
            background: transparent;
            border-color: transparent;
            color: var(--text-muted);
            padding: 6px 8px;
        }

        .btn-ghost:hover { background: var(--sidebar); color: var(--text); }

        .field { margin-bottom: 14px; }

        .field label {
            display: block;
            margin-bottom: 6px;
            font-size: 12px;
            font-weight: 600;
            color: var(--text-muted);
            text-transform: uppercase;
            letter-spacing: .04em;
        }

        .field input {
            width: 100%;
            padding: 10px 12px;
            font: inherit;
            color: var(--text);
            background: var(--bg);
            border: 1px solid var(--border);
            border-radius: 8px;
        }

        .field input:focus {
            outline: none;
            border-color: var(--accent);
            box-shadow: 0 0 0 3px var(--accent-soft);
        }

        .field-error {
            margin: 6px 0 0;
            font-size: 12px;
            color: var(--danger);
        }

        .alert {
            margin-bottom: 16px;
            padding: 10px 12px;
            font-size: 13px;
            border: 1px solid var(--border);
            border-left: 3px solid var(--accent);
            border-radius: 8px;
            background: var(--sidebar);
        }

        .alert ul { margin: 6px 0 0; padding-left: 18px; }

        /* ---------- auth ---------- */

        body.auth {
            display: flex;
            min-height: 100vh;
        }

        .auth-main {
            display: flex;
            flex: 1 1 auto;
            align-items: center;
            justify-content: center;
            padding: 40px 24px 72px;
        }

        .auth-body {
            width: 100%;
            max-width: 340px;
        }

        .auth-body h1 {
            margin: 0 0 4px;
            font-size: 24px;
            letter-spacing: -.02em;
        }

        .auth-body .subtitle {
            margin: 0 0 24px;
            color: var(--text-muted);
        }

        .auth-footer {
            margin-top: 20px;
            text-align: center;
            font-size: 13px;
            color: var(--text-muted);
        }

        .auth-footer a {
            font-weight: 600;
            color: var(--text);
        }

    </style>

    @stack('styles')
</head>
<body class="@yield('body-class')">
@yield('content')
@stack('scripts')
</body>
</html>
