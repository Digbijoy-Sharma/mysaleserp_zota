@php
    $isSuper = auth()->check()
        && method_exists(auth()->user(), 'isSuperadmin')
        && auth()->user()->isSuperadmin();

    $current = $current ?? '';
@endphp

<!-- Left side column. contains the logo and sidebar -->
<aside class="side-bar tw-relative tw-hidden tw-h-full tw-bg-white tw-w-64 xl:tw-w-64 lg:tw-flex lg:tw-flex-col tw-shrink-0">

    <a href="{{ route('super.dashboard') }}"
       class="tw-flex tw-items-center tw-justify-center tw-w-full tw-border-r tw-h-15 theme-logo-bg tw-shrink-0 tw-border-primary-500/30">
        <p class="tw-text-lg tw-font-medium tw-text-white side-bar-heading tw-text-center">
            Dava India
            <span class="tw-inline-block tw-w-3 tw-h-3 tw-bg-emerald-400 tw-rounded-full tw-ml-1" title="Super Admin"></span>
        </p>
    </a>

    <div class="tw-flex-1 tw-overflow-y-auto tw-border-r tw-border-gray-200 tw-py-3 tw-px-2">
        <ul class="tw-space-y-0.5 tw-text-sm">
            {{-- Dashboard --}}
            <li>
                <a href="{{ route('super.dashboard') }}"
                   class="tw-flex tw-items-center tw-gap-2.5 tw-px-3 tw-py-2 tw-rounded-md tw-text-gray-700 hover:tw-bg-emerald-50 hover:tw-text-emerald-700 {{ $current === 'dashboard' ? 'tw-bg-emerald-50 tw-text-emerald-700 tw-font-semibold' : '' }}">
                    <i class="fas fa-tachometer-alt tw-w-4 tw-text-center"></i>
                    <span>Central Dashboard</span>
                </a>
            </li>

            <li class="tw-px-3 tw-pt-4 tw-pb-1 tw-text-[10px] tw-uppercase tw-tracking-wider tw-text-gray-400 tw-font-semibold">
                Master Data
            </li>

            <li>
                <a href="{{ route('super.products.index') }}"
                   class="tw-flex tw-items-center tw-gap-2.5 tw-px-3 tw-py-2 tw-rounded-md tw-text-gray-700 hover:tw-bg-emerald-50 hover:tw-text-emerald-700 {{ $current === 'products' ? 'tw-bg-emerald-50 tw-text-emerald-700 tw-font-semibold' : '' }}">
                    <i class="fas fa-pills tw-w-4 tw-text-center"></i>
                    <span>Central Products</span>
                </a>
            </li>

            <li>
                <a href="{{ route('super.vendors.index') }}"
                   class="tw-flex tw-items-center tw-gap-2.5 tw-px-3 tw-py-2 tw-rounded-md tw-text-gray-700 hover:tw-bg-emerald-50 hover:tw-text-emerald-700 {{ $current === 'vendors' ? 'tw-bg-emerald-50 tw-text-emerald-700 tw-font-semibold' : '' }}">
                    <i class="fas fa-truck tw-w-4 tw-text-center"></i>
                    <span>Central Vendors</span>
                </a>
            </li>

            <li>
                <a href="{{ route('super.stores.index') }}"
                   class="tw-flex tw-items-center tw-gap-2.5 tw-px-3 tw-py-2 tw-rounded-md tw-text-gray-700 hover:tw-bg-emerald-50 hover:tw-text-emerald-700 {{ $current === 'stores' ? 'tw-bg-emerald-50 tw-text-emerald-700 tw-font-semibold' : '' }}">
                    <i class="fas fa-store tw-w-4 tw-text-center"></i>
                    <span>Stores</span>
                </a>
            </li>

            <li>
                <a href="{{ route('super.users.index') }}"
                   class="tw-flex tw-items-center tw-gap-2.5 tw-px-3 tw-py-2 tw-rounded-md tw-text-gray-700 hover:tw-bg-emerald-50 hover:tw-text-emerald-700 {{ $current === 'users' ? 'tw-bg-emerald-50 tw-text-emerald-700 tw-font-semibold' : '' }}">
                    <i class="fas fa-users tw-w-4 tw-text-center"></i>
                    <span>Users</span>
                </a>
            </li>

            <li class="tw-px-3 tw-pt-4 tw-pb-1 tw-text-[10px] tw-uppercase tw-tracking-wider tw-text-gray-400 tw-font-semibold">
                Reports
            </li>

            <li>
                <a href="{{ route('super.reports.stock') }}"
                   class="tw-flex tw-items-center tw-gap-2.5 tw-px-3 tw-py-2 tw-rounded-md tw-text-gray-700 hover:tw-bg-emerald-50 hover:tw-text-emerald-700 {{ $current === 'stock' ? 'tw-bg-emerald-50 tw-text-emerald-700 tw-font-semibold' : '' }}">
                    <i class="fas fa-boxes tw-w-4 tw-text-center"></i>
                    <span>Cross-Store Stock</span>
                </a>
            </li>

            <li>
                <a href="{{ route('super.reports.stockAlerts') }}"
                   class="tw-flex tw-items-center tw-gap-2.5 tw-px-3 tw-py-2 tw-rounded-md tw-text-gray-700 hover:tw-bg-emerald-50 hover:tw-text-emerald-700 {{ $current === 'stock-alerts' ? 'tw-bg-emerald-50 tw-text-emerald-700 tw-font-semibold' : '' }}">
                    <i class="fas fa-bell tw-w-4 tw-text-center"></i>
                    <span>Stock Alerts</span>
                </a>
            </li>

            <li class="tw-px-3 tw-pt-4 tw-pb-1 tw-text-[10px] tw-uppercase tw-tracking-wider tw-text-gray-400 tw-font-semibold">
                Configuration
            </li>

            <li>
                <a href="{{ route('super.settings.pos') }}"
                   class="tw-flex tw-items-center tw-gap-2.5 tw-px-3 tw-py-2 tw-rounded-md tw-text-gray-700 hover:tw-bg-emerald-50 hover:tw-text-emerald-700 {{ $current === 'pos-settings' ? 'tw-bg-emerald-50 tw-text-emerald-700 tw-font-semibold' : '' }}">
                    <i class="fas fa-sliders-h tw-w-4 tw-text-center"></i>
                    <span>POS Settings</span>
                </a>
            </li>

            @if(config('dava.pharmacy_enabled', true))
                <li class="tw-px-3 tw-pt-4 tw-pb-1 tw-text-[10px] tw-uppercase tw-tracking-wider tw-text-gray-400 tw-font-semibold">
                    Pharmacy
                </li>
                <li>
                    <a href="{{ route('super.products.index') }}?drug_schedule=H"
                       class="tw-flex tw-items-center tw-gap-2.5 tw-px-3 tw-py-2 tw-rounded-md tw-text-gray-700 hover:tw-bg-rose-50 hover:tw-text-rose-700">
                        <i class="fas fa-prescription-bottle-alt tw-w-4 tw-text-center"></i>
                        <span>Schedule H Drugs</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('super.products.index') }}?drug_schedule=H1"
                       class="tw-flex tw-items-center tw-gap-2.5 tw-px-3 tw-py-2 tw-rounded-md tw-text-gray-700 hover:tw-bg-rose-50 hover:tw-text-rose-700">
                        <i class="fas fa-capsules tw-w-4 tw-text-center"></i>
                        <span>Schedule H1 Drugs</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('super.products.index') }}?drug_schedule=X"
                       class="tw-flex tw-items-center tw-gap-2.5 tw-px-3 tw-py-2 tw-rounded-md tw-text-gray-700 hover:tw-bg-rose-50 hover:tw-text-rose-700">
                        <i class="fas fa-skull-crossbones tw-w-4 tw-text-center"></i>
                        <span>Schedule X Drugs</span>
                    </a>
                </li>
            @endif
        </ul>
    </div>

    <div class="tw-border-r tw-border-gray-200 tw-px-3 tw-py-3 tw-text-center tw-text-[10px] tw-text-gray-400 tw-shrink-0">
        v1.0 · Dava India Super Panel
    </div>
</aside>
