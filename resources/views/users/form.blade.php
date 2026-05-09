@extends('layouts.app')
@section('title', $user->exists ? 'Edit User' : 'Tambah User')
@section('content')
<form method="POST" action="{{ $user->exists ? route('users.update', $user) : route('users.store') }}" class="space-y-5 rounded-lg bg-white p-6 shadow-sm">
@csrf
@if($user->exists) @method('PUT') @endif

<div>
    <h1 class="text-xl font-bold">{{ $user->exists ? 'Edit User' : 'Tambah User' }}</h1>
    <p class="text-sm text-slate-500">Atur identitas, role, dan status akses user.</p>
</div>

<div class="grid gap-4 md:grid-cols-2">
    <x-form-input label="Nama" name="name" :value="$user->name" />
    <x-form-input label="Email" name="email" type="email" :value="$user->email" />

    <label class="block">
        <span class="text-sm font-medium">Role</span>
        <select name="role" class="mt-1 w-full rounded-lg border-slate-300 shadow-sm focus:border-slate-900 focus:ring-slate-900">
            @foreach($roleOptions as $value => $label)
                <option value="{{ $value }}" @selected(old('role', $user->role) === $value)>{{ $label }}</option>
            @endforeach
        </select>
        <div class="mt-2 space-y-1 text-xs text-slate-500">
            @foreach($roleDescriptions as $value => $description)
                <div><span class="font-semibold">{{ $roleOptions[$value] }}:</span> {{ $description }}</div>
            @endforeach
        </div>
    </label>

    <label class="block rounded-lg border border-slate-200 p-4">
        <span class="flex items-center gap-3">
            <input type="checkbox" name="is_active" value="1" @checked(old('is_active', $user->is_active ?? true))
                   class="rounded border-slate-300 text-slate-950 focus:ring-slate-900">
            <span>
                <span class="block text-sm font-medium">User aktif</span>
                <span class="text-xs text-slate-500">User nonaktif tidak bisa login ke aplikasi.</span>
            </span>
        </span>
    </label>

    <label class="block">
        <span class="text-sm font-medium">Password</span>
        <input type="password" name="password"
               class="mt-1 w-full rounded-lg border-slate-300 shadow-sm focus:border-slate-900 focus:ring-slate-900">
        @if($user->exists)
            <span class="mt-1 block text-xs text-slate-500">Kosongkan jika tidak ingin mengubah password.</span>
        @endif
    </label>

    <label class="block">
        <span class="text-sm font-medium">Konfirmasi Password</span>
        <input type="password" name="password_confirmation"
               class="mt-1 w-full rounded-lg border-slate-300 shadow-sm focus:border-slate-900 focus:ring-slate-900">
    </label>
</div>

<div class="flex gap-2">
    <button class="rounded-lg bg-slate-950 px-5 py-3 text-white">Simpan</button>
    <a href="{{ route('users.index') }}" class="rounded-lg bg-slate-100 px-5 py-3 text-slate-700">Batal</a>
</div>
</form>
@endsection
