<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title') - {{ config('app.name', 'POS') }}</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    @include('layouts.partials.css')
    @include('layouts.partials.extracss_auth')

    <script src='https://www.google.com/recaptcha/api.js'></script>

    <style>
        :root {
            --rx-green-50:  #ecfdf5;
            --rx-green-100: #d1fae5;
            --rx-green-300: #6ee7b7;
            --rx-green-500: #10b981;
            --rx-green-600: #059669;
            --rx-green-700: #047857;
            --rx-green-800: #065f46;
            --rx-green-900: #064e3b;
            --rx-ink-900:    #0f172a;
            --rx-ink-700:    #334155;
            --rx-ink-500:    #64748b;
            --rx-ink-400:    #94a3b8;
            --rx-ink-300:    #cbd5e1;
            --rx-ink-200:    #e2e8f0;
            --rx-ink-100:    #f1f5f9;
            --rx-ink-50:     #f8fafc;
        }

        html, body { height: 100%; }
        body {
            font-family: 'Inter', system-ui, -apple-system, "Segoe UI", Roboto, sans-serif;
            background: var(--rx-ink-50);
            -webkit-font-smoothing: antialiased;
            -moz-osx-font-smoothing: grayscale;
        }

        .rx-login-shell { min-height: 100vh; }

        /* Left brand panel */
        .rx-brand {
            position: relative;
            background:
                radial-gradient(1200px 600px at 100% 0%, rgba(255,255,255,0.10), transparent 60%),
                radial-gradient(900px 500px at 0% 100%, rgba(16,185,129,0.35), transparent 60%),
                linear-gradient(135deg, #065f46 0%, #047857 45%, #059669 100%);
            color: #fff;
            overflow: hidden;
        }
        .rx-brand::before {
            content: "";
            position: absolute; inset: 0;
            background-image:
                linear-gradient(rgba(255,255,255,0.06) 1px, transparent 1px),
                linear-gradient(90deg, rgba(255,255,255,0.06) 1px, transparent 1px);
            background-size: 32px 32px;
            mask-image: radial-gradient(ellipse at center, black 40%, transparent 80%);
            -webkit-mask-image: radial-gradient(ellipse at center, black 40%, transparent 80%);
            pointer-events: none;
        }
        .rx-brand-blob {
            position: absolute;
            border-radius: 9999px;
            filter: blur(40px);
            opacity: 0.35;
            pointer-events: none;
        }
        .rx-brand-blob.b1 { width: 320px; height: 320px; background: #34d399; top: -80px; right: -60px; }
        .rx-brand-blob.b2 { width: 380px; height: 380px; background: #10b981; bottom: -120px; left: -100px; }

        .rx-logo-chip {
            width: 64px; height: 64px;
            display: inline-flex; align-items: center; justify-content: center;
            background: rgba(255,255,255,0.15);
            border: 1px solid rgba(255,255,255,0.25);
            border-radius: 16px;
            backdrop-filter: blur(8px);
            -webkit-backdrop-filter: blur(8px);
        }

        .rx-feature {
            display: flex; align-items: center; gap: 12px;
            padding: 12px 14px;
            border-radius: 12px;
            background: rgba(255,255,255,0.08);
            border: 1px solid rgba(255,255,255,0.12);
            backdrop-filter: blur(6px);
            -webkit-backdrop-filter: blur(6px);
        }
        .rx-feature .rx-dot {
            width: 32px; height: 32px;
            display: inline-flex; align-items: center; justify-content: center;
            background: rgba(255,255,255,0.18);
            border-radius: 10px;
            flex-shrink: 0;
        }

        /* Right form panel */
        .rx-form-card {
            background: #ffffff;
            border: 1px solid var(--rx-ink-200);
            border-radius: 20px;
            box-shadow:
                0 1px 2px rgba(15, 23, 42, 0.04),
                0 20px 50px -20px rgba(15, 23, 42, 0.18);
        }

        .rx-input {
            width: 100%;
            height: 48px;
            border: 1px solid var(--rx-ink-300);
            border-radius: 12px;
            padding: 0 44px 0 44px;
            font-size: 14px;
            color: var(--rx-ink-900);
            background: #fff;
            transition: border-color .15s, box-shadow .15s, background .15s;
        }
        .rx-input::placeholder { color: var(--rx-ink-400); }
        .rx-input:focus {
            outline: none;
            border-color: var(--rx-green-500);
            box-shadow: 0 0 0 4px rgba(16, 185, 129, 0.18);
            background: #fff;
        }
        .rx-input.is-invalid {
            border-color: #ef4444;
            box-shadow: 0 0 0 4px rgba(239, 68, 68, 0.15);
        }

        .rx-input-icon {
            position: absolute; left: 14px; top: 50%;
            transform: translateY(-50%);
            color: var(--rx-ink-400);
            pointer-events: none;
        }
        .rx-input-action {
            position: absolute; right: 8px; top: 50%;
            transform: translateY(-50%);
            background: transparent; border: 0; padding: 6px;
            color: var(--rx-ink-400);
            border-radius: 8px;
            cursor: pointer;
        }
        .rx-input-action:hover { color: var(--rx-green-700); background: var(--rx-green-50); }

        .rx-btn-primary {
            width: 100%;
            height: 48px;
            border: 0;
            border-radius: 12px;
            color: #fff;
            font-weight: 600;
            font-size: 15px;
            letter-spacing: 0.01em;
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
            box-shadow: 0 8px 20px -8px rgba(16, 185, 129, 0.55);
            cursor: pointer;
            transition: transform .05s, box-shadow .15s, filter .15s;
            display: inline-flex; align-items: center; justify-content: center; gap: 8px;
        }
        .rx-btn-primary:hover { filter: brightness(1.05); box-shadow: 0 12px 24px -8px rgba(16, 185, 129, 0.65); }
        .rx-btn-primary:active { transform: translateY(1px); }
        .rx-btn-primary:disabled { opacity: .7; cursor: not-allowed; }

        .rx-link {
            color: var(--rx-green-700);
            font-weight: 500;
            text-decoration: none;
        }
        .rx-link:hover { color: var(--rx-green-800); text-decoration: underline; }

        .rx-checkbox {
            width: 18px; height: 18px;
            border-radius: 5px;
            accent-color: var(--rx-green-600);
            cursor: pointer;
        }

        .rx-divider {
            display: flex; align-items: center; gap: 12px;
            color: var(--rx-ink-400);
            font-size: 12px;
        }
        .rx-divider::before, .rx-divider::after {
            content: ""; flex: 1; height: 1px; background: var(--rx-ink-200);
        }

        .rx-error-text { color: #b91c1c; font-size: 12px; margin-top: 6px; }

        .rx-row { display: flex; flex-wrap: wrap; }
        .rx-row > * { flex: 0 0 auto; }
        .rx-col-6 { width: 50%; }
        .rx-col-12 { width: 100%; }
        .rx-items-center { align-items: center; }
        .rx-justify-between { justify-content: space-between; }

        .rx-mobile-brand { display: block; }
        @media (min-width: 901px) {
            .rx-mobile-brand { display: none; }
        }

        @media (max-width: 900px) {
            .rx-brand { display: none !important; }
            .rx-col-6 { width: 100%; }
        }
    </style>
</head>

<body>
    @inject('request', 'Illuminate\Http\Request')
    @if (session('status') && session('status.success'))
        <input type="hidden" id="status_span" data-status="{{ session('status.success') }}"
            data-msg="{{ session('status.msg') }}">
    @endif

    <div class="rx-login-shell">
        @yield('content')
    </div>

    @include('layouts.partials.javascripts')
    @yield('javascript')
</body>

</html>
