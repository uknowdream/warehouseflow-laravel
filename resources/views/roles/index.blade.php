@extends('layouts.app')

@section('title', 'Role Management')

@section('content')
<div class="mb-5 flex flex-col gap-3 md:flex-row md:items-center md:justify-between">
    <div>
        <h1 class="text-2xl font-bold">Role Management</h1>
        <p class="text-sm text-slate-500">Matrix akses untuk setiap role. Ubah role user dari halaman User Management.</p>
    </div>
    <div class="flex gap-2">
        <a href="{{ route('users.index') }}" class="rounded-lg bg-slate-950 px-4 py-2 text-sm font-semibold text-white">User Management</a>
        <a href="{{ route('audit-logs.index') }}" class="rounded-lg bg-slate-100 px-4 py-2 text-sm font-semibold text-slate-700">Audit Log</a>
    </div>
</div>

<div class="grid gap-4 xl:grid-cols-4">
    @foreach($roles as $role)
        <div class="rounded-lg bg-white p-5 shadow-sm">
            <div class="flex items-start justify-between gap-3">
                <div>
                    <div class="text-lg font-bold">{{ $role['label'] }}</div>
                    <div class="mt-1 text-sm text-slate-500">{{ $role['description'] }}</div>
                </div>
                <span class="rounded-full bg-slate-100 px-3 py-1 text-xs font-semibold text-slate-700">{{ $role['total'] }} user</span>
            </div>
            <div class="mt-4 grid grid-cols-2 gap-3 text-sm">
                <div class="rounded-lg bg-emerald-50 p-3 text-emerald-800">
                    <div class="text-xs uppercase">Aktif</div>
                    <div class="text-xl font-bold">{{ $role['active'] }}</div>
                </div>
                <div class="rounded-lg bg-rose-50 p-3 text-rose-800">
                    <div class="text-xs uppercase">Nonaktif</div>
                    <div class="text-xl font-bold">{{ $role['inactive'] }}</div>
                </div>
            </div>
            <a href="{{ route('users.index', ['role' => $role['key']]) }}" class="mt-4 block rounded-lg bg-slate-950 px-4 py-2 text-center text-sm font-semibold text-white">Lihat User</a>
        </div>
    @endforeach
</div>

<div class="mt-6 overflow-hidden rounded-lg bg-white shadow-sm">
    <table class="w-full min-w-[920px] text-sm">
        <thead class="bg-slate-50">
            <tr>
                <th class="p-3 text-left">Role</th>
                @foreach(array_keys($roles->first()['permissions']) as $permission)
                    <th class="p-3 text-center">{{ $permission }}</th>
                @endforeach
            </tr>
        </thead>
        <tbody>
        @foreach($roles as $role)
            <tr class="border-t">
                <td class="p-3 font-semibold">{{ $role['label'] }}</td>
                @foreach($role['permissions'] as $allowed)
                    <td class="p-3 text-center">
                        <span class="inline-flex h-7 w-7 items-center justify-center rounded-full {{ $allowed ? 'bg-emerald-100 text-emerald-800' : 'bg-slate-100 text-slate-400' }}">
                            {{ $allowed ? 'Y' : '-' }}
                        </span>
                    </td>
                @endforeach
            </tr>
        @endforeach
        </tbody>
    </table>
</div>
@endsection
