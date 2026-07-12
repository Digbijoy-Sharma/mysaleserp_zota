@extends('layouts.app')
@section('title', 'Bulk Import Stores')

@section('content')
<div class="tw-min-h-screen tw-bg-gray-50 tw-p-6">
    <div class="tw-max-w-3xl tw-mx-auto">
        <h1 class="tw-text-2xl tw-font-bold tw-text-gray-900 tw-mb-1">Bulk Import Stores</h1>
        <p class="tw-text-sm tw-text-gray-600 tw-mb-4">Upload a CSV to create many stores at once. For 2800 stores, this is the recommended approach.</p>

        <div class="tw-bg-white tw-rounded-lg tw-shadow tw-p-6">
            <h2 class="tw-font-semibold tw-mb-2">Expected columns (header row)</h2>
            <pre class="tw-text-xs tw-bg-gray-50 tw-p-2 tw-rounded tw-overflow-x-auto">name,store_code,gstin,drug_license_no,state,district,city,pincode,country,admin_username,admin_email,admin_password</pre>

            <form method="post" action="{{ route('super.stores.processImport') }}" enctype="multipart/form-data">
                @csrf
                <input type="file" name="csv" accept=".csv,.txt" required class="tw-mb-4 tw-block">
                <button class="tw-bg-emerald-600 tw-text-white tw-px-5 tw-py-2 tw-rounded">Upload &amp; Import</button>
                <a href="{{ route('super.stores.index') }}" class="tw-ml-2 tw-text-gray-600 hover:tw-underline">Cancel</a>
            </form>
        </div>
    </div>
</div>
@endsection
