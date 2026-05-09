@extends('layouts.app')
@section('title', $user->exists ? 'Edit User' : 'Tambah User')
@section('content')
<form method="POST" action="{{ $user->exists ? route('users.update', $user) : route('users.store') }}" class="bg-white p-6 rounded-2xl shadow space-y-4">
@csrf
@if($user->exists) @method('PUT') @endif

<div class="grid md:grid-cols-2 gap-4">
    <x-form-input label="Nama" name="name" :value="$user->name" />
    <x-form-input label="Email" name="email" type="email" :value="$user->email" />

    <label class="block">
        <span class="text-sm font-medium">Role</span>
        <select name="role" class="mt-1 w-full rounded-xl border-slate-300 shadow-sm focus:border-slate-900 focus:ring-slate-900">
            @foreach($roleOptions as $value => $label)
                <option value="{{ $value }}" @selected(old('role', $user->role) === $value)>{{ $label }}</option>
            @endforeach
        </select>
    </label>

    <div></div>

    <label class="block">
        <span class="text-sm font-medium">Password</span>
        <input type="password" name="password"
               class="mt-1 w-full rounded-xl border-slate-300 shadow-sm focus:border-slate-900 focus:ring-slate-900">
        @if($user->exists)
            <span class="mt-1 block text-xs text-slate-500">Kosongkan jika tidak ingin mengubah password.</span>
        @endif
    </label>

    <label class="block">
        <span class="text-sm font-medium">Konfirmasi Password</span>
        <input type="password" name="password_confirmation"
               class="mt-1 w-full rounded-xl border-slate-300 shadow-sm focus:border-slate-900 focus:ring-slate-900">
    </label>
</div>

<div class="flex gap-2">
    <button class="px-5 py-3 bg-slate-950 text-white rounded-xl">Simpan</button>
    <a href="{{ route('users.index') }}" class="px-5 py-3 bg-slate-100 text-slate-700 rounded-xl">Batal</a>
</div>
</form>
@endsection
