@extends('layouts.app')
@section('title', 'User Management')
@section('content')
<div class="mb-5 flex flex-col gap-3 md:flex-row md:items-center md:justify-between">
    <div>
        <h1 class="text-2xl font-bold">User Management</h1>
        <p class="text-sm text-slate-500">Kelola akses tim, status akun, dan role operasional.</p>
    </div>
    <div class="flex flex-wrap gap-2">
        <a href="{{ route('roles.index') }}" class="rounded-lg bg-slate-100 px-4 py-2 text-center text-sm font-semibold text-slate-700">Role Management</a>
        <a href="{{ route('audit-logs.index') }}" class="rounded-lg bg-slate-100 px-4 py-2 text-center text-sm font-semibold text-slate-700">Audit Log</a>
        <a href="{{ route('users.create') }}" class="rounded-lg bg-slate-950 px-4 py-2 text-center text-sm font-semibold text-white">Tambah User</a>
    </div>
</div>

<div class="mb-5 grid gap-3 md:grid-cols-4">
    <div class="rounded-lg bg-white p-4 shadow-sm">
        <div class="text-xs font-semibold uppercase text-slate-500">Total User</div>
        <div class="mt-2 text-2xl font-bold">{{ $summary['total'] }}</div>
    </div>
    <div class="rounded-lg bg-white p-4 shadow-sm">
        <div class="text-xs font-semibold uppercase text-slate-500">Aktif</div>
        <div class="mt-2 text-2xl font-bold text-emerald-700">{{ $summary['active'] }}</div>
    </div>
    <div class="rounded-lg bg-white p-4 shadow-sm">
        <div class="text-xs font-semibold uppercase text-slate-500">Nonaktif</div>
        <div class="mt-2 text-2xl font-bold text-rose-700">{{ $summary['inactive'] }}</div>
    </div>
    <div class="rounded-lg bg-white p-4 shadow-sm">
        <div class="text-xs font-semibold uppercase text-slate-500">Admin</div>
        <div class="mt-2 text-2xl font-bold text-blue-700">{{ $summary['admins'] }}</div>
    </div>
</div>

<form method="GET" action="{{ route('users.index') }}" class="mb-4 rounded-lg bg-white p-4 shadow-sm">
    <div class="grid gap-3 md:grid-cols-[1fr_220px_180px_auto]">
        <input type="text" name="search" value="{{ $search }}" placeholder="Cari nama, email, atau role"
               class="w-full rounded-lg border-slate-300 shadow-sm focus:border-slate-900 focus:ring-slate-900">
        <select name="role" class="w-full rounded-lg border-slate-300 shadow-sm focus:border-slate-900 focus:ring-slate-900">
            <option value="">Semua role</option>
            @foreach($roleOptions as $value => $label)
                <option value="{{ $value }}" @selected($selectedRole === $value)>{{ $label }}</option>
            @endforeach
        </select>
        <select name="status" class="w-full rounded-lg border-slate-300 shadow-sm focus:border-slate-900 focus:ring-slate-900">
            <option value="">Semua status</option>
            <option value="active" @selected($selectedStatus === 'active')>Aktif</option>
            <option value="inactive" @selected($selectedStatus === 'inactive')>Nonaktif</option>
        </select>
        <div class="flex gap-2">
            <button class="rounded-lg bg-slate-950 px-4 py-2 text-white">Filter</button>
            @if($search || $selectedRole || $selectedStatus)
                <a href="{{ route('users.index') }}" class="rounded-lg bg-slate-100 px-4 py-2 text-slate-700">Reset</a>
            @endif
        </div>
    </div>
</form>

<div class="overflow-hidden rounded-lg bg-white shadow-sm">
    <table class="w-full min-w-[900px] text-sm">
        <thead class="bg-slate-50">
            <tr>
                <th class="p-3 text-left">Nama</th>
                <th class="p-3 text-left">Email</th>
                <th class="p-3 text-left">Role</th>
                <th class="p-3 text-left">Status</th>
                <th class="p-3 text-left">Login Terakhir</th>
                <th class="p-3 text-left">Dibuat</th>
                <th class="p-3">Aksi</th>
            </tr>
        </thead>
        <tbody>
        @forelse($users as $user)
            <tr class="border-t">
                <td class="p-3 font-medium">
                    {{ $user->name }}
                    @if(auth()->id() === $user->id)
                        <span class="ml-2 rounded-full bg-slate-100 px-2 py-0.5 text-xs text-slate-600">Anda</span>
                    @endif
                </td>
                <td class="p-3">{{ $user->email }}</td>
                <td class="p-3">
                    <span class="inline-flex rounded-full px-3 py-1 text-xs font-semibold {{ $user->roleBadgeClass() }}">
                        {{ $user->roleLabel() }}
                    </span>
                </td>
                <td class="p-3">
                    <span class="inline-flex rounded-full px-3 py-1 text-xs font-semibold {{ $user->is_active ? 'bg-emerald-100 text-emerald-800' : 'bg-rose-100 text-rose-800' }}">
                        {{ $user->is_active ? 'Aktif' : 'Nonaktif' }}
                    </span>
                </td>
                <td class="p-3 text-slate-600">{{ $user->last_login_at?->format('d M Y H:i') ?? '-' }}</td>
                <td class="p-3">{{ $user->created_at?->format('d M Y') }}</td>
                <td class="p-3">
                    <div class="flex items-center justify-center gap-3">
                        <a class="text-yellow-600" href="{{ route('users.edit', $user) }}">Edit</a>
                        @if(auth()->id() !== $user->id)
                            <form method="POST" action="{{ route('users.destroy', $user) }}" onsubmit="return confirm('Hapus user ini?')">
                                @csrf
                                @method('DELETE')
                                <button class="text-red-600">Hapus</button>
                            </form>
                        @else
                            <span class="text-slate-400">Sedang login</span>
                        @endif
                    </div>
                </td>
            </tr>
        @empty
            <tr class="border-t">
                <td class="p-6 text-center text-slate-500" colspan="7">Belum ada user sesuai filter.</td>
            </tr>
        @endforelse
        </tbody>
    </table>
</div>

<div class="mt-4">{{ $users->links() }}</div>
@endsection
