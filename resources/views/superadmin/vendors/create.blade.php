@extends('layouts.superadmin')
@php $current = 'vendors'; @endphp
@section('title', 'Add Central Vendor')

@section('content')
<div class="tw-min-h-screen tw-bg-gray-50 tw-p-6">
    <div class="tw-max-w-5xl tw-mx-auto">
        <h1 class="tw-text-2xl tw-font-bold tw-text-gray-900 tw-mb-6">Add Central Vendor</h1>

        <form method="post" action="{{ route('super.vendors.store') }}" class="tw-bg-white tw-rounded-lg tw-shadow tw-p-6">
            @csrf
            @include('superadmin.vendors._form', ['supplier' => null, 'stores' => $stores, 'assigned' => []])
            <div class="tw-mt-6 tw-flex tw-gap-2">
                <button class="tw-bg-emerald-600 tw-text-white tw-px-5 tw-py-2 tw-rounded">Save</button>
                <a href="{{ route('super.vendors.index') }}" class="tw-bg-gray-200 tw-text-gray-800 tw-px-5 tw-py-2 tw-rounded">Cancel</a>
            </div>
        </form>
    </div>
</div>
@endsection
