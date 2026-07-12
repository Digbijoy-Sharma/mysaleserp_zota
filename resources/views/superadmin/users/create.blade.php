@extends('layouts.superadmin')
@php $current = 'users'; @endphp
@section('title', 'New User')

@section('content')
<div class="tw-min-h-screen tw-bg-gray-50 tw-p-6">
    <div class="tw-max-w-3xl tw-mx-auto">
        <h1 class="tw-text-2xl tw-font-bold tw-text-gray-900 tw-mb-6">Create New User</h1>

        @if($errors->any())
        <div class="tw-bg-red-50 tw-border tw-border-red-300 tw-text-red-700 tw-px-4 tw-py-3 tw-rounded tw-mb-4">
            <ul class="tw-list-disc tw-pl-5">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
        </div>
        @endif

        <form method="post" action="{{ route('super.users.store') }}" class="tw-bg-white tw-rounded-lg tw-shadow tw-p-6">
            @csrf
            <div class="tw-grid tw-grid-cols-1 md:tw-grid-cols-2 tw-gap-4">
                <div>
                    <label class="tw-block tw-text-xs tw-text-gray-500 tw-mb-1">First name *</label>
                    <input name="first_name" value="{{ old('first_name') }}" required class="tw-border tw-rounded tw-px-3 tw-py-2 tw-w-full">
                </div>
                <div>
                    <label class="tw-block tw-text-xs tw-text-gray-500 tw-mb-1">Last name</label>
                    <input name="last_name" value="{{ old('last_name') }}" class="tw-border tw-rounded tw-px-3 tw-py-2 tw-w-full">
                </div>
                <div>
                    <label class="tw-block tw-text-xs tw-text-gray-500 tw-mb-1">Username *</label>
                    <input name="username" value="{{ old('username') }}" required class="tw-border tw-rounded tw-px-3 tw-py-2 tw-w-full">
                </div>
                <div>
                    <label class="tw-block tw-text-xs tw-text-gray-500 tw-mb-1">Email</label>
                    <input name="email" type="email" value="{{ old('email') }}" class="tw-border tw-rounded tw-px-3 tw-py-2 tw-w-full">
                </div>
                <div>
                    <label class="tw-block tw-text-xs tw-text-gray-500 tw-mb-1">Password *</label>
                    <input name="password" type="text" value="{{ old('password', 'Dava@User#2026') }}" required class="tw-border tw-rounded tw-px-3 tw-py-2 tw-w-full">
                </div>
                <div>
                    <label class="tw-block tw-text-xs tw-text-gray-500 tw-mb-1">Store *</label>
                    <select name="business_id" required class="tw-border tw-rounded tw-px-3 tw-py-2 tw-w-full">
                        <option value="">-- select store --</option>
                        @foreach($stores as $s)
                        <option value="{{ $s->id }}" @selected(old('business_id')==$s->id)>{{ $s->name }} {{ $s->store_code ? '('.$s->store_code.')' : '' }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="md:tw-col-span-2">
                    <label class="tw-block tw-text-xs tw-text-gray-500 tw-mb-1">Role *</label>
                    <select name="role" required class="tw-border tw-rounded tw-px-3 tw-py-2 tw-w-full">
                        <option value="">-- select role --</option>
                        @foreach($roles as $rname => $rlabel)
                        <option value="{{ $rname }}" @selected(old('role')==$rname)>{{ $rlabel }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="tw-mt-6 tw-flex tw-gap-2">
                <button class="tw-bg-emerald-600 tw-text-white tw-px-5 tw-py-2 tw-rounded">Create User</button>
                <a href="{{ route('super.users.index') }}" class="tw-bg-gray-200 tw-text-gray-800 tw-px-5 tw-py-2 tw-rounded">Cancel</a>
            </div>
        </form>
    </div>
</div>
@endsection
