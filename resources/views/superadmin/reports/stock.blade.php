@extends('layouts.app')
@section('title', 'Stock Report — Dava India')

@section('content')
<div class="tw-min-h-screen tw-bg-gray-50 tw-p-6">
    <div class="tw-max-w-7xl tw-mx-auto">
        <h1 class="tw-text-2xl tw-font-bold tw-text-gray-900 tw-mb-1">Cross-Store Stock Report</h1>
        <p class="tw-text-sm tw-text-gray-600 tw-mb-4">All stocks aggregated across 2800 stores.</p>

        <div class="tw-bg-white tw-rounded-lg tw-shadow tw-p-4 tw-mb-4 tw-flex tw-gap-4 tw-flex-wrap tw-text-sm">
            <div><span class="tw-text-gray-500">Total line count:</span> <strong>{{ number_format($totals->line_count ?? 0) }}</strong></div>
            <div><span class="tw-text-gray-500">Total quantity:</span> <strong>{{ number_format($totals->total_qty ?? 0, 2) }}</strong></div>
            <a href="{{ route('super.reports.stockAlerts') }}" class="tw-text-blue-600 hover:tw-underline">→ Stock alerts</a>
            <a href="{{ route('super.reports.stockExport', request()->all()) }}" class="tw-ml-auto tw-bg-emerald-600 tw-text-white tw-px-3 tw-py-1 tw-rounded">Export CSV</a>
        </div>

        <form method="get" class="tw-bg-white tw-rounded-lg tw-shadow tw-p-4 tw-mb-4 tw-flex tw-gap-3 tw-flex-wrap tw-items-end">
            <div>
                <label class="tw-text-xs tw-text-gray-500">Store</label>
                <select name="business_id" class="tw-border tw-rounded tw-px-3 tw-py-2">
                    <option value="">All stores</option>
                    @foreach($stores as $s)
                    <option value="{{ $s->id }}" @selected(request('business_id')==$s->id)>{{ $s->name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="tw-text-xs tw-text-gray-500">State</label>
                <input name="state" value="{{ request('state') }}" class="tw-border tw-rounded tw-px-3 tw-py-2">
            </div>
            <div>
                <label class="tw-text-xs tw-text-gray-500">SKU</label>
                <input name="sku" value="{{ request('sku') }}" class="tw-border tw-rounded tw-px-3 tw-py-2">
            </div>
            <div>
                <label class="tw-text-xs tw-text-gray-500">Product name</label>
                <input name="product_name" value="{{ request('product_name') }}" class="tw-border tw-rounded tw-px-3 tw-py-2 tw-w-56">
            </div>
            <label class="tw-flex tw-items-center tw-gap-1">
                <input type="checkbox" name="low_stock" value="1" @checked(request('low_stock')==='1')> Low stock only
            </label>
            <label class="tw-flex tw-items-center tw-gap-1">
                <input type="checkbox" name="out_of_stock" value="1" @checked(request('out_of_stock')==='1')> Out of stock only
            </label>
            <button class="tw-bg-gray-800 tw-text-white tw-px-4 tw-py-2 tw-rounded">Filter</button>
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
                        <th class="tw-px-3 tw-py-2 tw-text-right">Sell Price (₹)</th>
                        <th class="tw-px-3 tw-py-2">Status</th>
                    </tr>
                </thead>
                <tbody>
                @forelse($rows as $r)
                    <tr class="tw-border-t">
                        <td class="tw-px-3 tw-py-2">{{ $r->store_name }}<br><span class="tw-text-xs tw-text-gray-400">{{ $r->store_code }}</span></td>
                        <td class="tw-px-3 tw-py-2">{{ $r->state ?? '—' }}</td>
                        <td class="tw-px-3 tw-py-2 tw-font-mono tw-text-xs">{{ $r->sku }}</td>
                        <td class="tw-px-3 tw-py-2">{{ $r->product_name }}</td>
                        <td class="tw-px-3 tw-py-2 tw-text-right">{{ number_format($r->qty_available, 2) }}</td>
                        <td class="tw-px-3 tw-py-2 tw-text-right">{{ number_format($r->alert_quantity, 2) }}</td>
                        <td class="tw-px-3 tw-py-2 tw-text-right">{{ number_format($r->selling_price ?? 0, 2) }}</td>
                        <td class="tw-px-3 tw-py-2">
                            @if($r->qty_available <= 0)
                                <span class="tw-bg-red-100 tw-text-red-700 tw-px-2 tw-py-1 tw-rounded tw-text-xs">Out of stock</span>
                            @elseif($r->qty_available <= $r->alert_quantity)
                                <span class="tw-bg-amber-100 tw-text-amber-700 tw-px-2 tw-py-1 tw-rounded tw-text-xs">Low stock</span>
                            @else
                                <span class="tw-bg-green-100 tw-text-green-700 tw-px-2 tw-py-1 tw-rounded tw-text-xs">OK</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="8" class="tw-px-3 tw-py-8 tw-text-center tw-text-gray-500">No stock data matches the filters.</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
        <div class="tw-mt-4">{{ $rows->links() }}</div>
    </div>
</div>
@endsection
