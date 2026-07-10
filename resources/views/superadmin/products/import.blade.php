@extends('layouts.app')
@section('title', 'Import Central Products')

@section('content')
<div class="tw-min-h-screen tw-bg-gray-50 tw-p-6">
    <div class="tw-max-w-3xl tw-mx-auto">
        <h1 class="tw-text-2xl tw-font-bold tw-text-gray-900 tw-mb-1">Import Central Products</h1>
        <p class="tw-text-sm tw-text-gray-600 tw-mb-4">Upload a CSV file. Products with existing SKUs will be updated.</p>

        <div class="tw-bg-white tw-rounded-lg tw-shadow tw-p-6">
            <h2 class="tw-font-semibold tw-mb-2">Expected columns</h2>
            <ul class="tw-text-sm tw-text-gray-600 tw-list-disc tw-pl-5 tw-mb-4">
                <li>name, sku, barcode, category, brand, unit</li>
                <li>default_mrp, default_sell_price, default_purchase_price, default_gst_percent, hsn_code</li>
                <li>composition, drug_schedule, prescription_required, manufacturer, package_form, default_alert_quantity</li>
            </ul>

            <form method="post" action="{{ route('super.products.processImport') }}" enctype="multipart/form-data">
                @csrf
                <input type="file" name="csv" accept=".csv,.txt" required class="tw-mb-4 tw-block">
                <button class="tw-bg-emerald-600 tw-text-white tw-px-5 tw-py-2 tw-rounded">Upload &amp; Import</button>
                <a href="{{ route('super.products.index') }}" class="tw-ml-2 tw-text-gray-600 hover:tw-underline">Cancel</a>
            </form>
        </div>
    </div>
</div>
@endsection
