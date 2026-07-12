@extends('layouts.superadmin')
@php $current = 'stock-alerts'; @endphp
@section('title', 'Stock Alerts — Dava India')

@section('content')
<div class="tw-min-h-screen tw-bg-gray-50 tw-p-6">
    <div class="tw-max-w-7xl tw-mx-auto">
        <h1 class="tw-text-2xl tw-font-bold tw-text-gray-900 tw-mb-1">Stock Alert Report</h1>
        <p class="tw-text-sm tw-text-gray-600 tw-mb-4">All products at or below alert quantity across all stores.</p>

        <form method="get" class="tw-bg-white tw-rounded-lg tw-shadow tw-p-4 tw-mb-4 tw-flex tw-gap-3 tw-flex-wrap tw-items-end">
            <div>
                <label class="tw-text-xs tw-text-gray-500">Store</label>
                <input type="number" name="business_id" value="{{ request('business_id') }}" class="tw-border tw-rounded tw-px-3 tw-py-2 tw-w-32" placeholder="ID">
            </div>
            <div>
                <label class="tw-text-xs tw-text-gray-500">State</label>
                <input name="state" value="{{ request('state') }}" class="tw-border tw-rounded tw-px-3 tw-py-2">
            </div>
            <button class="tw-bg-gray-800 tw-text-white tw-px-4 tw-py-2 tw-rounded">Filter</button>
            <a href="{{ route('super.reports.stockAlerts') }}" class="tw-text-gray-600 hover:tw-underline">Reset</a>
        </form>

        <div class="tw-bg-white tw-rounded-lg tw-shadow tw-overflow-x-auto">
            <table class="tw-min-w-full tw-text-sm">
                <thead class="tw-bg-gray-100 tw-text-left tw-text-gray-600">
                    <tr>
                        <th class="tw-px-3 tw-py-2">Store</th>
                        <th class="tw-px-3 tw-py-2">State</th>
                        <th class="tw-px-3 tw-py-2">SKU</th>
                        <th class="tw-px-3 tw-py-2">Product</th>
                        <th class="tw-px-3 tw-py-2 tw-text-right">Qty Available</th>
                        <th class="tw-px-3 tw-py-2 tw-text-right">Alert Qty</th>
                        <th class="tw-px-3 tw-py-2">Severity</th>
                    </tr>
                </thead>
                <tbody>
                @forelse($rows as $r)
                    <tr class="tw-border-t @if($r->qty_available <= 0) tw-bg-red-50 @endif">
                        <td class="tw-px-3 tw-py-2">{{ $r->store_name }}<br><span class="tw-text-xs tw-text-gray-400">{{ $r->store_code }}</span></td>
                        <td class="tw-px-3 tw-py-2">{{ $r->state ?? '—' }}</td>
                        <td class="tw-px-3 tw-py-2 tw-font-mono tw-text-xs">{{ $r->sku }}</td>
                        <td class="tw-px-3 tw-py-2">{{ $r->product_name }}</td>
                        <td class="tw-px-3 tw-py-2 tw-text-right tw-font-semibold">{{ number_format($r->qty_available, 2) }}</td>
                        <td class="tw-px-3 tw-py-2 tw-text-right">{{ number_format($r->alert_quantity, 2) }}</td>
                        <td class="tw-px-3 tw-py-2">
                            @if($r->qty_available <= 0)
                                <span class="tw-bg-red-100 tw-text-red-700 tw-px-2 tw-py-1 tw-rounded tw-text-xs tw-font-semibold">Out of stock</span>
                            @else
                                <span class="tw-bg-amber-100 tw-text-amber-700 tw-px-2 tw-py-1 tw-rounded tw-text-xs">Low</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="7" class="tw-px-3 tw-py-8 tw-text-center tw-text-green-600">All stores are above their alert quantities. ✓</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
        <div class="tw-mt-4">{{ $rows->links() }}</div>
    </div>
</div>
@endsection
