@extends('layouts.app')
@section('title', 'Edit Store')

@section('content')
<div class="tw-min-h-screen tw-bg-gray-50 tw-p-6">
    <div class="tw-max-w-3xl tw-mx-auto">
        <h1 class="tw-text-2xl tw-font-bold tw-text-gray-900 tw-mb-1">Edit Store</h1>
        <p class="tw-text-sm tw-text-gray-600 tw-mb-6">Store ID: <span class="tw-font-mono">{{ $store->id }}</span></p>

        @if($errors->any())
        <div class="tw-bg-red-50 tw-border tw-border-red-300 tw-text-red-700 tw-px-4 tw-py-3 tw-rounded tw-mb-4">
            <ul class="tw-list-disc tw-pl-5">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
        </div>
        @endif

        <form method="post" action="{{ route('super.stores.update', $store->id) }}" class="tw-bg-white tw-rounded-lg tw-shadow tw-p-6">
            @csrf @method('PUT')
            <div class="tw-grid tw-grid-cols-1 md:tw-grid-cols-2 tw-gap-4">
                <div>
                    <label class="tw-block tw-text-xs tw-text-gray-500 tw-mb-1">Store name *</label>
                    <input name="name" value="{{ old('name', $store->name) }}" required class="tw-border tw-rounded tw-px-3 tw-py-2 tw-w-full">
                </div>
                <div>
                    <label class="tw-block tw-text-xs tw-text-gray-500 tw-mb-1">Store code</label>
                    <input name="store_code" value="{{ old('store_code', $store->store_code) }}" class="tw-border tw-rounded tw-px-3 tw-py-2 tw-w-full">
                </div>
                <div>
                    <label class="tw-block tw-text-xs tw-text-gray-500 tw-mb-1">State</label>
                    <input name="state" value="{{ old('state', $store->state) }}" class="tw-border tw-rounded tw-px-3 tw-py-2 tw-w-full">
                </div>
                <div>
                    <label class="tw-block tw-text-xs tw-text-gray-500 tw-mb-1">District</label>
                    <input name="district" value="{{ old('district', $store->district) }}" class="tw-border tw-rounded tw-px-3 tw-py-2 tw-w-full">
                </div>
                <div>
                    <label class="tw-block tw-text-xs tw-text-gray-500 tw-mb-1">Pincode</label>
                    <input name="pincode" value="{{ old('pincode', $store->pincode) }}" class="tw-border tw-rounded tw-px-3 tw-py-2 tw-w-full">
                </div>
                <div>
                    <label class="tw-block tw-text-xs tw-text-gray-500 tw-mb-1">GSTIN</label>
                    <input name="gstin" value="{{ old('gstin', $store->gstin) }}" class="tw-border tw-rounded tw-px-3 tw-py-2 tw-w-full">
                </div>
                <div>
                    <label class="tw-block tw-text-xs tw-text-gray-500 tw-mb-1">Drug license no</label>
                    <input name="drug_license_no" value="{{ old('drug_license_no', $store->drug_license_no) }}" class="tw-border tw-rounded tw-px-3 tw-py-2 tw-w-full">
                </div>
            </div>

            @if($admin)
            <hr class="tw-my-6">
            <h3 class="tw-text-lg tw-font-semibold tw-mb-3">Store admin</h3>
            <p class="tw-text-sm tw-text-gray-600">
                Admin username: <span class="tw-font-mono">{{ $admin->username }}</span> · email: {{ $admin->email ?? '—' }}
            </p>
            @endif

            <div class="tw-mt-6 tw-flex tw-gap-2">
                <button class="tw-bg-emerald-600 tw-text-white tw-px-5 tw-py-2 tw-rounded">Save</button>
                <a href="{{ route('super.stores.index') }}" class="tw-bg-gray-200 tw-text-gray-800 tw-px-5 tw-py-2 tw-rounded">Cancel</a>
            </div>
        </form>
    </div>
</div>
@endsection
