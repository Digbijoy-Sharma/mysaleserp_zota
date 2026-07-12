@extends('layouts.superadmin')
@php $current = 'stores'; @endphp
@section('title', 'New Store')

@section('content')
<div class="tw-min-h-screen tw-bg-gray-50 tw-p-6">
    <div class="tw-max-w-3xl tw-mx-auto">
        <h1 class="tw-text-2xl tw-font-bold tw-text-gray-900 tw-mb-1">Create New Store</h1>
        <p class="tw-text-sm tw-text-gray-600 tw-mb-6">This will create a new business, auto-create 1 location, an Admin role, and a store admin user.</p>

        @if($errors->any())
        <div class="tw-bg-red-50 tw-border tw-border-red-300 tw-text-red-700 tw-px-4 tw-py-3 tw-rounded tw-mb-4">
            <ul class="tw-list-disc tw-pl-5">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
        </div>
        @endif

        <form method="post" action="{{ route('super.stores.store') }}" class="tw-bg-white tw-rounded-lg tw-shadow tw-p-6">
            @csrf
            <h3 class="tw-text-lg tw-font-semibold tw-mb-3">Store details</h3>
            <div class="tw-grid tw-grid-cols-1 md:tw-grid-cols-2 tw-gap-4">
                <div>
                    <label class="tw-block tw-text-xs tw-text-gray-500 tw-mb-1">Store name *</label>
                    <input name="name" value="{{ old('name') }}" required class="tw-border tw-rounded tw-px-3 tw-py-2 tw-w-full">
                </div>
                <div>
                    <label class="tw-block tw-text-xs tw-text-gray-500 tw-mb-1">Store code</label>
                    <input name="store_code" value="{{ old('store_code') }}" class="tw-border tw-rounded tw-px-3 tw-py-2 tw-w-full" placeholder="e.g. DAV-BLR-001">
                </div>
                <div>
                    <label class="tw-block tw-text-xs tw-text-gray-500 tw-mb-1">State</label>
                    <input name="state" value="{{ old('state') }}" class="tw-border tw-rounded tw-px-3 tw-py-2 tw-w-full">
                </div>
                <div>
                    <label class="tw-block tw-text-xs tw-text-gray-500 tw-mb-1">District / city</label>
                    <input name="city" value="{{ old('city') }}" class="tw-border tw-rounded tw-px-3 tw-py-2 tw-w-full">
                </div>
                <div>
                    <label class="tw-block tw-text-xs tw-text-gray-500 tw-mb-1">Pincode</label>
                    <input name="pincode" value="{{ old('pincode') }}" class="tw-border tw-rounded tw-px-3 tw-py-2 tw-w-full">
                </div>
                <div>
                    <label class="tw-block tw-text-xs tw-text-gray-500 tw-mb-1">GSTIN</label>
                    <input name="gstin" value="{{ old('gstin') }}" class="tw-border tw-rounded tw-px-3 tw-py-2 tw-w-full">
                </div>
                <div>
                    <label class="tw-block tw-text-xs tw-text-gray-500 tw-mb-1">Drug license no</label>
                    <input name="drug_license_no" value="{{ old('drug_license_no') }}" class="tw-border tw-rounded tw-px-3 tw-py-2 tw-w-full">
                </div>
                <div>
                    <label class="tw-block tw-text-xs tw-text-gray-500 tw-mb-1">Mobile</label>
                    <input name="mobile" value="{{ old('mobile') }}" class="tw-border tw-rounded tw-px-3 tw-py-2 tw-w-full">
                </div>
                <div class="md:tw-col-span-2">
                    <label class="tw-block tw-text-xs tw-text-gray-500 tw-mb-1">Address / landmark</label>
                    <input name="landmark" value="{{ old('landmark') }}" class="tw-border tw-rounded tw-px-3 tw-py-2 tw-w-full">
                </div>
            </div>

            <hr class="tw-my-6">
            <h3 class="tw-text-lg tw-font-semibold tw-mb-3">Store admin user</h3>
            <p class="tw-text-xs tw-text-gray-500 tw-mb-3">This user will own the new store and have access to its full panel.</p>
            <div class="tw-grid tw-grid-cols-1 md:tw-grid-cols-2 tw-gap-4">
                <div>
                    <label class="tw-block tw-text-xs tw-text-gray-500 tw-mb-1">First name</label>
                    <input name="admin_first_name" value="{{ old('admin_first_name', 'Store') }}" class="tw-border tw-rounded tw-px-3 tw-py-2 tw-w-full">
                </div>
                <div>
                    <label class="tw-block tw-text-xs tw-text-gray-500 tw-mb-1">Last name</label>
                    <input name="admin_last_name" value="{{ old('admin_last_name', 'Admin') }}" class="tw-border tw-rounded tw-px-3 tw-py-2 tw-w-full">
                </div>
                <div>
                    <label class="tw-block tw-text-xs tw-text-gray-500 tw-mb-1">Username</label>
                    <input name="admin_username" value="{{ old('admin_username') }}" class="tw-border tw-rounded tw-px-3 tw-py-2 tw-w-full" placeholder="auto-generated from store name if blank">
                </div>
                <div>
                    <label class="tw-block tw-text-xs tw-text-gray-500 tw-mb-1">Email</label>
                    <input name="admin_email" value="{{ old('admin_email') }}" class="tw-border tw-rounded tw-px-3 tw-py-2 tw-w-full">
                </div>
                <div class="md:tw-col-span-2">
                    <label class="tw-block tw-text-xs tw-text-gray-500 tw-mb-1">Password</label>
                    <input name="admin_password" value="{{ old('admin_password', 'Dava@Store#2026') }}" class="tw-border tw-rounded tw-px-3 tw-py-2 tw-w-full">
                    <p class="tw-text-xs tw-text-gray-400 tw-mt-1">Default: Dava@Store#2026 — please reset on first login.</p>
                </div>
            </div>

            <div class="tw-mt-6 tw-flex tw-gap-2">
                <button class="tw-bg-emerald-600 tw-text-white tw-px-5 tw-py-2 tw-rounded">Create Store</button>
                <a href="{{ route('super.stores.index') }}" class="tw-bg-gray-200 tw-text-gray-800 tw-px-5 tw-py-2 tw-rounded">Cancel</a>
            </div>
        </form>
    </div>
</div>
@endsection
