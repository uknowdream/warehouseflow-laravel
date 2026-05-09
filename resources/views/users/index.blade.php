@extends('layouts.app')
@section('title', 'User Management')
@section('content')
<div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between mb-4">
    <h1 class="text-2xl font-bold">User Management</h1>
    <a href="{{ route('users.create') }}" class="px-4 py-2 bg-slate-950 text-white rounded-xl text-center">Tambah User</a>
</div>

<form method="GET" action="{{ route('users.index') }}" class="mb-4 bg-white p-4 rounded-2xl shadow">
    <div class="flex flex-col gap-3 md:flex-row">
        <input type="text" name="search" value="{{ $search }}" placeholder="Cari nama, email, atau role"
               class="w-full rounded-xl border-slate-300 shadow-sm focus:border-slate-900 focus:ring-slate-900">
        <div class="flex gap-2">
            <button class="px-4 py-2 bg-slate-950 text-white rounded-xl">Cari</button>
            @if($search)
                <a href="{{ route('users.index') }}" class="px-4 py-2 bg-slate-100 text-slate-700 rounded-xl">Reset</a>
            @endif
        </div>
    </div>
</form>

<div class="bg-white rounded-2xl shadow overflow-hidden">
    <table class="w-full text-sm">
        <thead class="bg-slate-50">
            <tr>
                <th class="p-3 text-left">Nama</th>
                <th class="p-3 text-left">Email</th>
                <th class="p-3 text-left">Role</th>
                <th class="p-3 text-left">Dibuat</th>
                <th class="p-3">Aksi</th>
            </tr>
        </thead>
        <tbody>
        @forelse($users as $user)
            <tr class="border-t">
                <td class="p-3 font-medium">{{ $user->name }}</td>
                <td class="p-3">{{ $user->email }}</td>
                <td class="p-3">
                    <span class="inline-flex rounded-full px-3 py-1 text-xs font-semibold {{ $user->role === \App\Models\User::ROLE_ADMIN ? 'bg-blue-100 text-blue-800' : 'bg-slate-100 text-slate-700' }}">
                        {{ $roleOptions[$user->role] ?? ucfirst($user->role) }}
                    </span>
                </td>
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
                <td class="p-6 text-center text-slate-500" colspan="5">Belum ada user.</td>
            </tr>
        @endforelse
        </tbody>
    </table>
</div>

<div class="mt-4">{{ $users->links() }}</div>
@endsection
