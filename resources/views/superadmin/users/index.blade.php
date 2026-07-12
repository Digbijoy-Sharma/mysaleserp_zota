@extends('layouts.app')
@section('title', 'Users — Dava India')

@section('content')
<div class="tw-min-h-screen tw-bg-gray-50 tw-p-6">
    <div class="tw-max-w-7xl tw-mx-auto">
        <div class="tw-flex tw-justify-between tw-items-center tw-mb-6">
            <div>
                <h1 class="tw-text-2xl tw-font-bold tw-text-gray-900">Store Users</h1>
                <p class="tw-text-sm tw-text-gray-600">All users across all 2800 stores.</p>
            </div>
            <a href="{{ route('super.users.create') }}" class="tw-bg-emerald-600 tw-text-white tw-px-4 tw-py-2 tw-rounded">+ New User</a>
        </div>

        @if(session('status'))
        <div class="tw-bg-green-100 tw-border tw-border-green-400 tw-text-green-700 tw-px-4 tw-py-3 tw-rounded tw-mb-4">{{ session('status.msg') }}</div>
        @endif

        <form method="get" class="tw-bg-white tw-rounded-lg tw-shadow tw-p-4 tw-mb-4 tw-flex tw-gap-3 tw-flex-wrap tw-items-end">
            <div>
                <label class="tw-text-xs tw-text-gray-500">Search</label>
                <input type="text" name="search" value="{{ request('search') }}" class="tw-border tw-rounded tw-px-3 tw-py-2 tw-w-64" placeholder="Username / email / name">
            </div>
            <div>
                <label class="tw-text-xs tw-text-gray-500">Store</label>
                <select name="business_id" class="tw-border tw-rounded tw-px-3 tw-py-2">
                    <option value="">All stores</option>
                    @foreach($stores as $s)
                    <option value="{{ $s->id }}" @selected(request('business_id')==$s->id)>{{ $s->name }}</option>
                    @endforeach
                </select>
            </div>
            <button class="tw-bg-gray-800 tw-text-white tw-px-4 tw-py-2 tw-rounded">Filter</button>
        </form>

        <div class="tw-bg-white tw-rounded-lg tw-shadow tw-overflow-hidden">
            <table class="tw-min-w-full tw-text-sm">
                <thead class="tw-bg-gray-100 tw-text-left tw-text-gray-600">
                    <tr>
                        <th class="tw-px-4 tw-py-3">Username</th>
                        <th class="tw-px-4 tw-py-3">Name</th>
                        <th class="tw-px-4 tw-py-3">Email</th>
                        <th class="tw-px-4 tw-py-3">Store</th>
                        <th class="tw-px-4 tw-py-3">Roles</th>
                        <th class="tw-px-4 tw-py-3 tw-text-right">Actions</th>
                    </tr>
                </thead>
                <tbody>
                @forelse($users as $u)
                    <tr class="tw-border-t">
                        <td class="tw-px-4 tw-py-2 tw-font-mono tw-text-xs">{{ $u->username }}</td>
                        <td class="tw-px-4 tw-py-2">{{ $u->first_name }} {{ $u->last_name }}</td>
                        <td class="tw-px-4 tw-py-2">{{ $u->email ?? '—' }}</td>
                        <td class="tw-px-4 tw-py-2">{{ $u->business_id ? '#'.$u->business_id : '—' }}</td>
                        <td class="tw-px-4 tw-py-2 tw-text-xs">{{ $u->getRoleNames()->implode(', ') }}</td>
                        <td class="tw-px-4 tw-py-2 tw-text-right">
                            <a href="{{ route('super.users.edit', $u->id) }}" class="tw-text-blue-600 hover:tw-underline">Edit</a>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="tw-px-4 tw-py-8 tw-text-center tw-text-gray-500">No users yet.</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
        <div class="tw-mt-4">{{ $users->links() }}</div>
    </div>
</div>
@endsection
