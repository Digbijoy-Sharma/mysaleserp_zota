@extends('layouts.superadmin')
@php $current = 'stores'; @endphp
@section('title', 'Stores — Dava India')

@section('content')
<div class="tw-min-h-screen tw-bg-gray-50 tw-p-6">
    <div class="tw-max-w-7xl tw-mx-auto">
        <div class="tw-flex tw-justify-between tw-items-center tw-mb-6">
            <div>
                <h1 class="tw-text-2xl tw-font-bold tw-text-gray-900">Stores</h1>
                <p class="tw-text-sm tw-text-gray-600">All 2800 Dava India stores in one view.</p>
            </div>
            <div class="tw-flex tw-gap-2">
                <a href="{{ route('super.stores.create') }}" class="tw-bg-emerald-600 tw-text-white tw-px-4 tw-py-2 tw-rounded">+ New Store</a>
                <a href="{{ route('super.stores.import') }}" class="tw-bg-blue-600 tw-text-white tw-px-4 tw-py-2 tw-rounded">Import CSV</a>
                <a href="{{ route('super.stores.export') }}" class="tw-bg-gray-600 tw-text-white tw-px-4 tw-py-2 tw-rounded">Export</a>
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
                <input type="text" name="search" value="{{ request('search') }}" class="tw-border tw-rounded tw-px-3 tw-py-2 tw-w-64" placeholder="Name / code / GSTIN">
            </div>
            <div>
                <label class="tw-text-xs tw-text-gray-500">Status</label>
                <select name="is_suspended" class="tw-border tw-rounded tw-px-3 tw-py-2">
                    <option value="">All</option>
                    <option value="0" @selected(request('is_suspended')==='0')>Active</option>
                    <option value="1" @selected(request('is_suspended')==='1')>Suspended</option>
                </select>
            </div>
            <button class="tw-bg-gray-800 tw-text-white tw-px-4 tw-py-2 tw-rounded">Filter</button>
        </form>

        <div class="tw-bg-white tw-rounded-lg tw-shadow tw-overflow-hidden">
            <table class="tw-min-w-full tw-text-sm">
                <thead class="tw-bg-gray-100 tw-text-left tw-text-gray-600">
                    <tr>
                        <th class="tw-px-4 tw-py-3">Code</th>
                        <th class="tw-px-4 tw-py-3">Name</th>
                        <th class="tw-px-4 tw-py-3">State</th>
                        <th class="tw-px-4 tw-py-3">GSTIN</th>
                        <th class="tw-px-4 tw-py-3">Drug Lic.</th>
                        <th class="tw-px-4 tw-py-3">Status</th>
                        <th class="tw-px-4 tw-py-3 tw-text-right">Actions</th>
                    </tr>
                </thead>
                <tbody>
                @forelse($stores as $s)
                    <tr class="tw-border-t">
                        <td class="tw-px-4 tw-py-2 tw-font-mono tw-text-xs">{{ $s->store_code ?? '—' }}</td>
                        <td class="tw-px-4 tw-py-2 tw-font-medium">{{ $s->name }}</td>
                        <td class="tw-px-4 tw-py-2">{{ $s->state ?? '—' }}</td>
                        <td class="tw-px-4 tw-py-2 tw-text-xs">{{ $s->gstin ?? '—' }}</td>
                        <td class="tw-px-4 tw-py-2 tw-text-xs">{{ $s->drug_license_no ?? '—' }}</td>
                        <td class="tw-px-4 tw-py-2">
                            @if($s->is_suspended) <span class="tw-bg-red-100 tw-text-red-700 tw-px-2 tw-py-1 tw-rounded tw-text-xs">Suspended</span>
                            @else <span class="tw-bg-green-100 tw-text-green-700 tw-px-2 tw-py-1 tw-rounded tw-text-xs">Active</span>
                            @endif
                        </td>
                        <td class="tw-px-4 tw-py-2 tw-text-right">
                            <a href="{{ route('super.stores.edit', $s->id) }}" class="tw-text-blue-600 hover:tw-underline">Edit</a>
                            @if(!$s->is_suspended)
                            <form method="post" action="{{ route('super.stores.suspend', $s->id) }}" class="tw-inline" onsubmit="return confirm('Suspend this store?')">
                                @csrf
                                <button class="tw-text-red-600 hover:tw-underline tw-ml-2">Suspend</button>
                            </form>
                            @else
                            <form method="post" action="{{ route('super.stores.activate', $s->id) }}" class="tw-inline">
                                @csrf
                                <button class="tw-text-green-600 hover:tw-underline tw-ml-2">Activate</button>
                            </form>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="7" class="tw-px-4 tw-py-8 tw-text-center tw-text-gray-500">No stores yet. Click "New Store" to create one.</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
        <div class="tw-mt-4">{{ $stores->links() }}</div>
    </div>
</div>
@endsection
