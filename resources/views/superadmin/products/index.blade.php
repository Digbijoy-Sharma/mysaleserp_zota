@extends('layouts.app')

@section('title', 'Central Products — Dava India')

@section('content')
<div class="tw-min-h-screen tw-bg-gray-50 tw-p-6">
    <div class="tw-max-w-7xl tw-mx-auto">
        <div class="tw-flex tw-justify-between tw-items-center tw-mb-6">
            <div>
                <h1 class="tw-text-2xl tw-font-bold tw-text-gray-900">Central Product Master</h1>
                <p class="tw-text-sm tw-text-gray-600">Manage the global product catalog for all 2800 stores.</p>
            </div>
            <div class="tw-flex tw-gap-2">
                <a href="{{ route('super.products.create') }}" class="tw-bg-emerald-600 tw-text-white tw-px-4 tw-py-2 tw-rounded hover:tw-bg-emerald-700">
                    + Add Product
                </a>
                <a href="{{ route('super.products.import') }}" class="tw-bg-blue-600 tw-text-white tw-px-4 tw-py-2 tw-rounded hover:tw-bg-blue-700">
                    Import CSV
                </a>
                <a href="{{ route('super.products.export') }}" class="tw-bg-gray-600 tw-text-white tw-px-4 tw-py-2 tw-rounded hover:tw-bg-gray-700">
                    Export CSV
                </a>
            </div>
        </div>

        @if(session('status'))
        <div class="tw-bg-green-100 tw-border tw-border-green-400 tw-text-green-700 tw-px-4 tw-py-3 tw-rounded tw-mb-4">
            {{ session('status.msg') }}
        </div>
        @endif

        <form method="get" class="tw-bg-white tw-rounded-lg tw-shadow tw-p-4 tw-mb-4 tw-flex tw-gap-3 tw-flex-wrap tw-items-end">
            <div>
                <label class="tw-text-xs tw-text-gray-500">Search</label>
                <input type="text" name="search" value="{{ request('search') }}" class="tw-border tw-rounded tw-px-3 tw-py-2 tw-w-64" placeholder="Name / SKU / barcode">
            </div>
            <div>
                <label class="tw-text-xs tw-text-gray-500">Drug schedule</label>
                <select name="drug_schedule" class="tw-border tw-rounded tw-px-3 tw-py-2">
                    <option value="">All</option>
                    @foreach(['none','OTC','H','H1','X'] as $s)
                    <option value="{{ $s }}" @selected(request('drug_schedule')==$s)>{{ $s }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="tw-text-xs tw-text-gray-500">Status</label>
                <select name="is_active" class="tw-border tw-rounded tw-px-3 tw-py-2">
                    <option value="">All</option>
                    <option value="1" @selected(request('is_active')==='1')>Active</option>
                    <option value="0" @selected(request('is_active')==='0')>Inactive</option>
                </select>
            </div>
            <button class="tw-bg-gray-800 tw-text-white tw-px-4 tw-py-2 tw-rounded">Filter</button>
        </form>

        <div class="tw-bg-white tw-rounded-lg tw-shadow tw-overflow-hidden">
            <table class="tw-min-w-full tw-text-sm">
                <thead class="tw-bg-gray-100 tw-text-left tw-text-gray-600">
                    <tr>
                        <th class="tw-px-4 tw-py-3">SKU</th>
                        <th class="tw-px-4 tw-py-3">Name</th>
                        <th class="tw-px-4 tw-py-3">Schedule</th>
                        <th class="tw-px-4 tw-py-3">Manufacturer</th>
                        <th class="tw-px-4 tw-py-3 tw-text-right">MRP</th>
                        <th class="tw-px-4 tw-py-3 tw-text-right">Sell</th>
                        <th class="tw-px-4 tw-py-3">Status</th>
                        <th class="tw-px-4 tw-py-3 tw-text-right">Actions</th>
                    </tr>
                </thead>
                <tbody>
                @forelse($products as $p)
                    <tr class="tw-border-t">
                        <td class="tw-px-4 tw-py-2 tw-font-mono tw-text-xs">{{ $p->sku }}</td>
                        <td class="tw-px-4 tw-py-2">{{ $p->name }}</td>
                        <td class="tw-px-4 tw-py-2">
                            <span class="tw-px-2 tw-py-1 tw-rounded tw-text-xs
                                @if($p->drug_schedule==='X') tw-bg-red-100 tw-text-red-800
                                @elseif($p->drug_schedule==='H1') tw-bg-orange-100 tw-text-orange-800
                                @elseif($p->drug_schedule==='H') tw-bg-yellow-100 tw-text-yellow-800
                                @else tw-bg-gray-100 tw-text-gray-800 @endif">
                                {{ $p->drug_schedule }}
                            </span>
                        </td>
                        <td class="tw-px-4 tw-py-2">{{ $p->manufacturer ?? '—' }}</td>
                        <td class="tw-px-4 tw-py-2 tw-text-right">{{ number_format($p->default_mrp, 2) }}</td>
                        <td class="tw-px-4 tw-py-2 tw-text-right">{{ number_format($p->default_sell_price, 2) }}</td>
                        <td class="tw-px-4 tw-py-2">
                            @if(!$p->is_active) <span class="tw-text-gray-500">Inactive</span>
                            @elseif($p->is_banned) <span class="tw-text-red-600">Banned</span>
                            @elseif($p->is_discontinued) <span class="tw-text-orange-600">Discontinued</span>
                            @else <span class="tw-text-green-600">Active</span>
                            @endif
                        </td>
                        <td class="tw-px-4 tw-py-2 tw-text-right">
                            <a href="{{ route('super.products.edit', $p->id) }}" class="tw-text-blue-600 hover:tw-underline">Edit</a>
                            <form method="post" action="{{ route('super.products.destroy', $p->id) }}" class="tw-inline" onsubmit="return confirm('Delete this central product?')">
                                @csrf @method('DELETE')
                                <button class="tw-text-red-600 hover:tw-underline tw-ml-2">Delete</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="8" class="tw-px-4 tw-py-8 tw-text-center tw-text-gray-500">No central products yet. Click "Add Product" to start.</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>

        <div class="tw-mt-4">{{ $products->links() }}</div>
    </div>
</div>
@endsection
