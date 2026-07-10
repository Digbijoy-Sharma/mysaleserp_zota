@extends('layouts.app')
@section('title', 'View Central Vendor')

@section('content')
<div class="tw-min-h-screen tw-bg-gray-50 tw-p-6">
    <div class="tw-max-w-4xl tw-mx-auto">
        <h1 class="tw-text-2xl tw-font-bold tw-text-gray-900 tw-mb-1">{{ $supplier->name }}</h1>
        <p class="tw-text-sm tw-text-gray-600 tw-mb-6">Code: <span class="tw-font-mono">{{ $supplier->code ?? '—' }}</span></p>

        <div class="tw-bg-white tw-rounded-lg tw-shadow tw-p-6 tw-grid tw-grid-cols-1 md:tw-grid-cols-2 tw-gap-4 tw-text-sm">
            <div><strong>GSTIN:</strong> {{ $supplier->gstin ?? '—' }}</div>
            <div><strong>Drug license:</strong> {{ $supplier->drug_license_no ?? '—' }}</div>
            <div><strong>FSSAI:</strong> {{ $supplier->fssai_no ?? '—' }}</div>
            <div><strong>Lead time:</strong> {{ $supplier->lead_time_days }} days</div>
            <div><strong>Contact:</strong> {{ $supplier->contact_person ?? '—' }} · {{ $supplier->mobile ?? '' }} · {{ $supplier->email ?? '' }}</div>
            <div><strong>Address:</strong> {{ $supplier->address_line1 }} {{ $supplier->address_line2 }} {{ $supplier->city }} {{ $supplier->state }} {{ $supplier->pincode }}</div>
            <div class="md:tw-col-span-2"><strong>Payment terms:</strong> {{ $supplier->payment_terms ?? '—' }}</div>
            <div class="md:tw-col-span-2"><strong>Notes:</strong> {{ $supplier->notes ?? '—' }}</div>
        </div>

        <div class="tw-mt-4">
            <a href="{{ route('super.vendors.edit', $supplier->id) }}" class="tw-bg-blue-600 tw-text-white tw-px-4 tw-py-2 tw-rounded">Edit</a>
            <a href="{{ route('super.vendors.index') }}" class="tw-ml-2 tw-text-gray-600 hover:tw-underline">Back</a>
        </div>
    </div>
</div>
@endsection
