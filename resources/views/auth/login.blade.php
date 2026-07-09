@extends('layouts.auth2')
@section('title', __('lang_v1.login'))
@inject('request', 'Illuminate\Http\Request')
@section('content')

    @php
        $username = old('username');
        $password = null;
        if (config('app.env') == 'demo') {
            $username = 'admin';
            $password = '123456';

            $demo_types = [
                'all_in_one' => 'admin',
                'super_market' => 'admin',
                'pharmacy' => 'admin-pharmacy',
                'electronics' => 'admin-electronics',
                'services' => 'admin-services',
                'restaurant' => 'admin-restaurant',
                'superadmin' => 'superadmin',
                'woocommerce' => 'woocommerce_user',
                'essentials' => 'admin-essentials',
                'manufacturing' => 'manufacturer-demo',
            ];

            if (!empty($_GET['demo_type']) && array_key_exists($_GET['demo_type'], $demo_types)) {
                $username = $demo_types[$_GET['demo_type']];
            }
        }
    @endphp

    <div class="rx-row" style="min-height: 100vh; margin: 0;">

        {{-- LEFT: brand panel --}}
        <div class="rx-col-6 rx-brand" style="padding: 56px 56px;">
            <div class="rx-brand-blob b1"></div>
            <div class="rx-brand-blob b2"></div>

            <div class="rx-col-12" style="position: relative; z-index: 1; display: flex; flex-direction: column; justify-content: space-between; height: 100%;">
                <div class="rx-items-center" style="display: flex; align-items: center; gap: 12px;">
                    <div class="rx-logo-chip">
                        {{-- Pill / capsule icon --}}
                        <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 24 24"
                            fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                            stroke-linejoin="round" style="color:#fff;">
                            <path d="M10.5 20.5a7 7 0 0 1-7-7l7-7a7 7 0 0 1 7 7Z"
                                fill="rgba(255,255,255,0.18)" />
                            <path d="M8.5 8.5l7 7" />
                            <path d="M15.5 3.5a7 7 0 0 1 0 14" fill="rgba(255,255,255,0.10)" />
                        </svg>
                    </div>
                    <div>
                        <div style="font-size: 13px; font-weight: 500; opacity: .85; letter-spacing: .04em;">
                            {{ config('app.name', 'My Sales ERP') }}
                        </div>
                        <div style="font-size: 11px; opacity: .65;">Pharmacy Management Suite</div>
                    </div>
                </div>

                <div style="max-width: 460px;">
                    <h1 style="font-size: 38px; line-height: 1.15; font-weight: 700; letter-spacing: -0.02em; margin-bottom: 14px; margin-top: 0;">
                        Run your pharmacy<br>smarter, every day.
                    </h1>
                    <p style="font-size: 15px; line-height: 1.6; opacity: .85; margin-bottom: 28px; margin-top: 0;">
                        Inventory, prescriptions, billing and compliance — all in one
                        secure platform built for modern pharmacies.
                    </p>

                    <div style="display: flex; flex-direction: column; gap: 8px;">
                        <div class="rx-feature">
                            <div class="rx-dot">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24"
                                    fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"
                                    stroke-linejoin="round"><path d="M20 6 9 17l-5-5"/></svg>
                            </div>
                            <div>
                                <div style="font-weight: 600; font-size: 14px;">Batch &amp; expiry tracking</div>
                                <div style="font-size: 12px; opacity: .75;">FEFO-aware stock with low-stock alerts.</div>
                            </div>
                        </div>
                        <div class="rx-feature">
                            <div class="rx-dot">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24"
                                    fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"
                                    stroke-linejoin="round"><path d="M20 6 9 17l-5-5"/></svg>
                            </div>
                            <div>
                                <div style="font-weight: 600; font-size: 14px;">Prescription &amp; GST billing</div>
                                <div style="font-size: 12px; opacity: .75;">Fast checkout with audit-ready invoices.</div>
                            </div>
                        </div>
                        <div class="rx-feature">
                            <div class="rx-dot">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24"
                                    fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"
                                    stroke-linejoin="round"><path d="M20 6 9 17l-5-5"/></svg>
                            </div>
                            <div>
                                <div style="font-weight: 600; font-size: 14px;">Multi-branch, role-based access</div>
                                <div style="font-size: 12px; opacity: .75;">Centralised control for chains &amp; stores.</div>
                            </div>
                        </div>
                    </div>
                </div>

                <div style="font-size: 12px; opacity: .65;">
                    &copy; {{ date('Y') }} {{ config('app.name', 'My Sales ERP') }}. All rights reserved.
                </div>
            </div>
        </div>

        {{-- RIGHT: form panel --}}
        <div class="rx-col-6"
            style="background: var(--rx-ink-50); padding: 32px 20px; display: flex; align-items: center; justify-content: center;">
            <div style="width: 100%; max-width: 440px;">

                {{-- Mobile-only brand header (since left panel is hidden on small screens) --}}
                <div class="rx-mobile-brand rx-items-center" style="display: flex; align-items: center; gap: 12px; margin-bottom: 24px;">
                    <div class="rx-logo-chip" style="background: var(--rx-green-50); border-color: var(--rx-green-100);">
                        <svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" viewBox="0 0 24 24"
                            fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                            stroke-linejoin="round" style="color: var(--rx-green-700);">
                            <path d="M10.5 20.5a7 7 0 0 1-7-7l7-7a7 7 0 0 1 7 7Z"/>
                            <path d="M8.5 8.5l7 7"/>
                            <path d="M15.5 3.5a7 7 0 0 1 0 14"/>
                        </svg>
                    </div>
                    <div>
                        <div style="font-size: 14px; font-weight: 600; color: var(--rx-ink-900);">
                            {{ config('app.name', 'My Sales ERP') }}
                        </div>
                        <div style="font-size: 11px; color: var(--rx-ink-500);">Pharmacy Management Suite</div>
                    </div>
                </div>

                <div class="rx-form-card" style="padding: 32px;">
                    <div style="margin-bottom: 20px;">
                        <h2 style="font-size: 22px; font-weight: 700; color: var(--rx-ink-900); margin-bottom: 6px; margin-top: 0;">
                            @lang('lang_v1.welcome_back')
                        </h2>
                        <p style="font-size: 14px; color: var(--rx-ink-500); margin: 0;">
                            Sign in to your {{ config('app.name', 'My Sales ERP') }} account
                        </p>
                    </div>

                    <form method="POST" action="{{ route('login') }}" id="login-form" autocomplete="off">
                        {{ csrf_field() }}

                        {{-- Username --}}
                        <div style="margin-bottom: 16px;">
                            <label for="username"
                                style="display: block; font-size: 13px; font-weight: 500; color: var(--rx-ink-700); margin-bottom: 6px;">
                                @lang('lang_v1.username')
                            </label>
                            <div style="position: relative;">
                                <span class="rx-input-icon">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24"
                                        fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                        stroke-linejoin="round">
                                        <path d="M19 21v-2a4 4 0 0 0-4-4H9a4 4 0 0 0-4 4v2"/>
                                        <circle cx="12" cy="7" r="4"/>
                                    </svg>
                                </span>
                                <input id="username" name="username" type="text" required autofocus
                                    placeholder="Enter your username"
                                    value="{{ $username }}"
                                    class="rx-input {{ $errors->has('username') ? 'is-invalid' : '' }}">
                            </div>
                            @if ($errors->has('username'))
                                <div class="rx-error-text">{{ $errors->first('username') }}</div>
                            @endif
                        </div>

                        {{-- Password --}}
                        <div style="margin-bottom: 8px;">
                            <div class="rx-items-center rx-justify-between" style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 6px;">
                                <label for="password"
                                    style="font-size: 13px; font-weight: 500; color: var(--rx-ink-700);">
                                    @lang('lang_v1.password')
                                </label>
                                @if (config('app.env') != 'demo')
                                    <a href="{{ route('password.request') }}" tabindex="-1"
                                        style="font-size: 12px;" class="rx-link">
                                        @lang('lang_v1.forgot_your_password')
                                    </a>
                                @endif
                            </div>
                            <div style="position: relative;">
                                <span class="rx-input-icon">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24"
                                        fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                        stroke-linejoin="round">
                                        <rect width="18" height="11" x="3" y="11" rx="2" ry="2"/>
                                        <path d="M7 11V7a5 5 0 0 1 10 0v4"/>
                                    </svg>
                                </span>
                                <input id="password" name="password" type="password" required
                                    placeholder="Enter your password"
                                    value="{{ $password }}"
                                    class="rx-input {{ $errors->has('password') ? 'is-invalid' : '' }}">
                                <button type="button" id="show_hide_icon" class="rx-input-action" aria-label="Toggle password visibility">
                                    <svg id="rx-eye-open" xmlns="http://www.w3.org/2000/svg" width="20" height="20"
                                        viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                        stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M2 12s3-7 10-7 10 7 10 7-3 7-10 7-10-7-10-7Z"/>
                                        <circle cx="12" cy="12" r="3"/>
                                    </svg>
                                    <svg id="rx-eye-closed" xmlns="http://www.w3.org/2000/svg" width="20" height="20"
                                        viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                        stroke-linecap="round" stroke-linejoin="round" style="display:none;">
                                        <path d="M9.88 9.88a3 3 0 1 0 4.24 4.24"/>
                                        <path d="M10.73 5.08A10.43 10.43 0 0 1 12 5c7 0 10 7 10 7a13.16 13.16 0 0 1-1.67 2.68"/>
                                        <path d="M6.61 6.61A13.526 13.526 0 0 0 2 12s3 7 10 7a9.74 9.74 0 0 0 5.39-1.61"/>
                                        <line x1="2" x2="22" y1="2" y2="22"/>
                                    </svg>
                                </button>
                            </div>
                            @if ($errors->has('password'))
                                <div class="rx-error-text">{{ $errors->first('password') }}</div>
                            @endif
                        </div>

                        {{-- Remember me --}}
                        <div class="rx-items-center" style="display: flex; align-items: center; margin-top: 10px; margin-bottom: 16px;">
                            <label class="rx-items-center" style="display: flex; align-items: center; gap: 8px; cursor: pointer; user-select: none;">
                                <input type="checkbox" name="remember" {{ old('remember') ? 'checked' : '' }}
                                    class="rx-checkbox">
                                <span style="font-size: 13px; color: var(--rx-ink-700);">@lang('lang_v1.remember_me')</span>
                            </label>
                        </div>

                        {{-- reCAPTCHA --}}
                        @if(config('constants.enable_recaptcha'))
                            <div style="margin-bottom: 16px;">
                                <div class="g-recaptcha" data-sitekey="{{ config('constants.google_recaptcha_key') }}"></div>
                                @if ($errors->has('g-recaptcha-response'))
                                    <div class="rx-error-text">{{ $errors->first('g-recaptcha-response') }}</div>
                                @endif
                            </div>
                        @endif

                        {{-- Submit --}}
                        <button type="submit" id="login_button" class="rx-btn-primary">
                            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24"
                                fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                stroke-linejoin="round">
                                <path d="M15 3h4a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2h-4"/>
                                <polyline points="10 17 15 12 10 7"/>
                                <line x1="15" x2="3" y1="12" y2="12"/>
                            </svg>
                            <span>@lang('lang_v1.login')</span>
                        </button>
                    </form>

                    {{-- Register link --}}
                    @if (!($request->segment(1) == 'business' && $request->segment(2) == 'register'))
                        @if (config('constants.allow_registration'))
                            <div class="rx-divider" style="margin: 20px 0 14px;">
                                <span>OR</span>
                            </div>
                            <div class="text-center" style="font-size: 13px; color: var(--rx-ink-500); text-align: center;">
                                {{ __('business.not_yet_registered') }}
                                <a href="{{ route('business.getRegister') }}@if (!empty(request()->lang)) {{ '?lang=' . request()->lang }} @endif"
                                    class="rx-link">
                                    {{ __('business.register_now') }}
                                </a>
                            </div>
                        @endif
                    @endif
                </div>

                <div style="text-align: center; font-size: 12px; color: var(--rx-ink-400); margin-top: 18px;">
                    &copy; {{ date('Y') }} {{ config('app.name', 'My Sales ERP') }}. All rights reserved.
                </div>
            </div>
        </div>
    </div>

@stop

@section('javascript')
    <script type="text/javascript">
        $(document).ready(function() {
            $('.change_lang').click(function() {
                window.location = "{{ route('login') }}?lang=" + $(this).attr('value');
            });

            $('a.demo-login').click(function(e) {
                e.preventDefault();
                $('#username').val($(this).data('admin'));
                $('#password').val("{{ $password }}");
                $('form#login-form').submit();
            });

            // Show / hide password
            $('#show_hide_icon').on('click', function(e) {
                e.preventDefault();
                const pwd = $('#password');
                const isHidden = pwd.attr('type') === 'password';
                pwd.attr('type', isHidden ? 'text' : 'password');
                $('#rx-eye-open').toggle(!isHidden);
                $('#rx-eye-closed').toggle(isHidden);
            });

            // Disable button on submit
            $('form#login-form').on('submit', function() {
                $('#login_button').attr('disabled', true).css('opacity', .7);
            });
        });
    </script>
@stop
