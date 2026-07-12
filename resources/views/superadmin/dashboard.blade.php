@extends('layouts.app')

@section('title', 'Dava India — Super Admin Dashboard')

@section('content')
<div class="tw-min-h-screen tw-bg-gray-50 tw-p-6">
    <div class="tw-max-w-7xl tw-mx-auto">
        {{-- Header --}}
        <div class="tw-mb-6">
            <h1 class="tw-text-3xl tw-font-bold tw-text-gray-900">Dava India — Central Dashboard</h1>
            <p class="tw-text-sm tw-text-gray-600 tw-mt-1">Cross-store view of all 2800 stores</p>
        </div>

        {{-- KPI cards --}}
        <div class="tw-grid tw-grid-cols-1 md:tw-grid-cols-2 lg:tw-grid-cols-4 tw-gap-4 tw-mb-6">
            <div class="tw-bg-white tw-rounded-lg tw-shadow tw-p-5">
                <div class="tw-text-xs tw-uppercase tw-text-gray-500">Total Stores</div>
                <div class="tw-text-3xl tw-font-bold tw-text-gray-900">{{ number_format($stats['total_stores']) }}</div>
                <div class="tw-text-xs tw-text-green-600 mt-1">
                    {{ $stats['active_stores'] }} active ·
                    {{ $stats['suspended_stores'] }} suspended
                </div>
            </div>

            <div class="tw-bg-white tw-rounded-lg tw-shadow tw-p-5">
                <div class="tw-text-xs tw-uppercase tw-text-gray-500">Central Products</div>
                <div class="tw-text-3xl tw-font-bold tw-text-gray-900">{{ number_format($stats['total_central_products']) }}</div>
                <div class="tw-text-xs tw-text-gray-500 mt-1">Master catalog</div>
            </div>

            <div class="tw-bg-white tw-rounded-lg tw-shadow tw-p-5">
                <div class="tw-text-xs tw-uppercase tw-text-gray-500">Store Users</div>
                <div class="tw-text-3xl tw-font-bold tw-text-gray-900">{{ number_format($stats['total_users']) }}</div>
                <div class="tw-text-xs tw-text-gray-500 mt-1">{{ $stats['total_superadmins'] }} super admins</div>
            </div>

            <div class="tw-bg-white tw-rounded-lg tw-shadow tw-p-5">
                <div class="tw-text-xs tw-uppercase tw-text-gray-500">Today's Sales (₹)</div>
                <div class="tw-text-3xl tw-font-bold tw-text-emerald-600">
                    {{ number_format($stats['today_sales_total'], 2) }}
                </div>
                <div class="tw-text-xs tw-text-gray-500 mt-1">All stores combined</div>
            </div>

            <div class="tw-bg-white tw-rounded-lg tw-shadow tw-p-5">
                <div class="tw-text-xs tw-uppercase tw-text-gray-500">Low Stock SKUs</div>
                <div class="tw-text-3xl tw-font-bold tw-text-amber-600">{{ number_format($stats['low_stock_count']) }}</div>
                <div class="tw-text-xs tw-text-gray-500 mt-1">Below alert quantity</div>
            </div>

            <div class="tw-bg-white tw-rounded-lg tw-shadow tw-p-5">
                <div class="tw-text-xs tw-uppercase tw-text-gray-500">Out of Stock</div>
                <div class="tw-text-3xl tw-font-bold tw-text-red-600">{{ number_format($stats['out_of_stock_count']) }}</div>
                <div class="tw-text-xs tw-text-gray-500 mt-1">All stores</div>
            </div>
        </div>

        {{-- Top 10 stores --}}
        <div class="tw-bg-white tw-rounded-lg tw-shadow">
            <div class="tw-px-5 tw-py-3 tw-border-b">
                <h2 class="tw-text-lg tw-font-semibold tw-text-gray-900">Top 10 Stores — Last 30 Days</h2>
            </div>
            <div class="tw-p-5">
                @if($topStores->isEmpty())
                    <p class="tw-text-gray-500">No sales data yet.</p>
                @else
                <table class="tw-min-w-full tw-text-sm">
                    <thead>
                        <tr class="tw-text-left tw-text-gray-500 tw-border-b">
                            <th class="tw-py-2">#</th>
                            <th class="tw-py-2">Store</th>
                            <th class="tw-py-2 tw-text-right">Bills</th>
                            <th class="tw-py-2 tw-text-right">Revenue (₹)</th>
                        </tr>
                    </thead>
                    <tbody>
                    @foreach($topStores as $i => $row)
                        <tr class="tw-border-b">
                            <td class="tw-py-2">{{ $i+1 }}</td>
                            <td class="tw-py-2">{{ $row->name }}</td>
                            <td class="tw-py-2 tw-text-right">{{ number_format($row->bill_count) }}</td>
                            <td class="tw-py-2 tw-text-right tw-font-medium">
                                {{ number_format($row->revenue, 2) }}
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
                @endif
            </div>
        </div>

        <div class="tw-mt-6 tw-text-center tw-text-xs tw-text-gray-400">
            Dava India · Super Admin Panel · v1.0
        </div>
    </div>
</div>
@endsection
