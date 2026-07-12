@extends('layouts.app')
@section('title', 'Central Vendors — Dava India')

@section('content')
<div class="tw-min-h-screen tw-bg-gray-50 tw-p-6">
    <div class="tw-max-w-7xl tw-mx-auto">
        <div class="tw-flex tw-justify-between tw-items-center tw-mb-6">
            <div>
                <h1 class="tw-text-2xl tw-font-bold tw-text-gray-900">Central Vendor Master</h1>
                <p class="tw-text-sm tw-text-gray-600">Manage the global supplier catalog for all stores.</p>
            </div>
            <a href="{{ route('super.vendors.create') }}" class="tw-bg-emerald-600 tw-text-white tw-px-4 tw-py-2 tw-rounded hover:tw-bg-emerald-700">
                + Add Vendor
            </a>
        </div>

        @if(session('status'))
        <div class="tw-bg-green-100 tw-border tw-border-green-400 tw-text-green-700 tw-px-4 tw-py-3 tw-rounded tw-mb-4">
            {{ session('status.msg') }}
        </div>
        @endif

        <form method="get" class="tw-bg-white tw-rounded-lg tw-shadow tw-p-4 tw-mb-4 tw-flex tw-gap-3 tw-flex-wrap tw-items-end">
            <div>
                <label class="tw-text-xs tw-text-gray-500">Search</label>
                <input type="text" name="search" value="{{ request('search') }}" class="tw-border tw-rounded tw-px-3 tw-py-2 tw-w-64" placeholder="Name / GSTIN / mobile">
            </div>
            <button class="tw-bg-gray-800 tw-text-white tw-px-4 tw-py-2 tw-rounded">Filter</button>
        </form>

        <div class="tw-bg-white tw-rounded-lg tw-shadow tw-overflow-hidden">
            <table class="tw-min-w-full tw-text-sm">
                <thead class="tw-bg-gray-100 tw-text-left tw-text-gray-600">
                    <tr>
                        <th class="tw-px-4 tw-py-3">Code</th>
                        <th class="tw-px-4 tw-py-3">Name</th>
                        <th class="tw-px-4 tw-py-3">GSTIN</th>
                        <th class="tw-px-4 tw-py-3">Drug Lic.</th>
                        <th class="tw-px-4 tw-py-3">Contact</th>
                        <th class="tw-px-4 tw-py-3">City</th>
                        <th class="tw-px-4 tw-py-3">Status</th>
                        <th class="tw-px-4 tw-py-3 tw-text-right">Actions</th>
                    </tr>
                </thead>
                <tbody>
                @forelse($suppliers as $s)
                    <tr class="tw-border-t">
                        <td class="tw-px-4 tw-py-2 tw-font-mono tw-text-xs">{{ $s->code }}</td>
                        <td class="tw-px-4 tw-py-2 tw-font-medium">{{ $s->name }}</td>
                        <td class="tw-px-4 tw-py-2 tw-text-xs">{{ $s->gstin ?? '—' }}</td>
                        <td class="tw-px-4 tw-py-2 tw-text-xs">{{ $s->drug_license_no ?? '—' }}</td>
                        <td class="tw-px-4 tw-py-2">
                            <div>{{ $s->contact_person ?? '—' }}</div>
                            <div class="tw-text-xs tw-text-gray-500">{{ $s->mobile ?? '' }} {{ $s->email ?? '' }}</div>
                        </td>
                        <td class="tw-px-4 tw-py-2">{{ $s->city ?? '—' }}</td>
                        <td class="tw-px-4 tw-py-2">
                            @if($s->is_active) <span class="tw-text-green-600">Active</span>
                            @else <span class="tw-text-gray-500">Inactive</span> @endif
                        </td>
                        <td class="tw-px-4 tw-py-2 tw-text-right">
                            <a href="{{ route('super.vendors.edit', $s->id) }}" class="tw-text-blue-600 hover:tw-underline">Edit</a>
                            <form method="post" action="{{ route('super.vendors.destroy', $s->id) }}" class="tw-inline" onsubmit="return confirm('Delete this central vendor?')">
                                @csrf @method('DELETE')
                                <button class="tw-text-red-600 hover:tw-underline tw-ml-2">Delete</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="8" class="tw-px-4 tw-py-8 tw-text-center tw-text-gray-500">No central vendors yet.</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
        <div class="tw-mt-4">{{ $suppliers->links() }}</div>
    </div>
</div>
@endsection
