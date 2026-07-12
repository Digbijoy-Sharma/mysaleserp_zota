@inject('request', 'Illuminate\Http\Request')

@php
    $pos_layout = false;
    $whitelist = ['127.0.0.1', '::1'];
@endphp

<!DOCTYPE html>
<html class="tw-bg-white tw-scroll-smooth" lang="{{ app()->getLocale() }}"
    dir="{{ in_array(session()->get('user.language', config('app.locale')), config('constants.langs_rtl')) ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no"
        name="viewport">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'Super Admin') - Dava India</title>

    @include('layouts.partials.css')
    @include('layouts.partials.extracss')
    @yield('css')
</head>
<body class="tw-font-sans tw-antialiased tw-text-gray-900 tw-bg-gray-100 hold-transition skin-blue-light sidebar-mini">
    <div class="tw-flex thetop">
        @include('layouts.partials.sidebar_superadmin')

        @if (in_array($_SERVER['REMOTE_ADDR'], $whitelist))
            <input type="hidden" id="__is_localhost" value="true">
        @endif

        <input type="hidden" id="__code" value="INR">
        <input type="hidden" id="__symbol" value="₹">
        <input type="hidden" id="__thousand" value=",">
        <input type="hidden" id="__decimal" value=".">
        <input type="hidden" id="__symbol_placement" value="before">
        <input type="hidden" id="__precision" value="2">
        <input type="hidden" id="__quantity_precision" value="2">

        <main class="tw-flex tw-flex-col tw-flex-1 tw-h-full tw-min-w-0 tw-bg-gray-100">
            {{-- Super Admin header --}}
            <div class="tw-transition-all tw-duration-5000 tw-border-b theme-header-bg tw-shrink-0 tw-border-primary-500/30 no-print">
                <div class="tw-px-5 tw-py-3">
                    <div class="tw-flex tw-items-start tw-justify-between tw-gap-6 lg:tw-items-center">
                        <div class="tw-flex tw-items-center tw-gap-3">
                            <h2 class="tw-text-white tw-text-lg tw-font-semibold tw-tracking-wide">
                                @yield('page_title', 'Dava India — Super Admin')
                            </h2>
                        </div>
                        <div class="tw-flex tw-items-center tw-gap-3">
                            <span class="tw-text-xs tw-text-white/70 tw-hidden md:tw-inline">
                                {{ auth()->user()->username ?? '' }}
                                @if(auth()->user() && method_exists(auth()->user(), 'isSuperadmin') && auth()->user()->isSuperadmin())
                                    <span class="tw-bg-emerald-500 tw-text-white tw-text-[10px] tw-font-semibold tw-px-2 tw-py-0.5 tw-rounded tw-ml-1">SUPER</span>
                                @endif
                            </span>
                            <a href="{{ route('super.dashboard') }}"
                               class="tw-text-xs tw-text-white/90 hover:tw-text-white tw-underline-offset-2 hover:tw-underline">
                                Dashboard
                            </a>
                            <a href="{{ url('/logout') }}"
                               onclick="event.preventDefault(); document.getElementById('super-logout-form').submit();"
                               class="tw-text-xs tw-text-white/90 hover:tw-text-white tw-underline-offset-2 hover:tw-underline">
                                Logout
                            </a>
                            <form id="super-logout-form" action="{{ url('/logout') }}" method="POST" class="tw-hidden">
                                {{ csrf_field() }}
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            <div id="app">
                @yield('vue')
            </div>

            <div class="tw-flex-1 tw-overflow-y-auto tw-h-screen" id="scrollable-container">
                @yield('content')
                @include('layouts.partials.footer')
            </div>

            <div class='scrolltop no-print'>
                <div class='scroll icon'><i class="fas fa-angle-up"></i></div>
            </div>
        </main>

        @include('layouts.partials.javascripts')
        <div class="modal fade view_modal" tabindex="-1" role="dialog" aria-labelledby="gridSystemModalLabel"></div>

        @if (!empty($__additional_views) && is_array($__additional_views))
            @foreach ($__additional_views as $additional_view)
                @includeIf($additional_view)
            @endforeach
        @endif
        <div>
            <div class="overlay tw-hidden"></div>
        </div>
</body>
<style>
    @media print {
        #scrollable-container {
            overflow: visible !important;
            height: auto !important;
        }
        .side-bar, .thetop > aside { display: none !important; }
    }
</style>
<style>
    .overlay {
        width: 100vw;
        height: 100vh;
        background: rgba(0, 0, 0, 0.8);
        position: fixed;
        top: 0;
        left: 0;
        display: none;
        z-index: 20;
    }
    #scrollable-container { position: relative; }
</style>
</html>
