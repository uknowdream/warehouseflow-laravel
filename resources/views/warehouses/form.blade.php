@extends('layouts.app')
@section('title', $warehouse->exists ? 'Edit Warehouse' : 'Tambah Warehouse')
@section('content')
<form method="POST" action="{{ $warehouse->exists ? route('warehouses.update', $warehouse) : route('warehouses.store') }}" class="space-y-5 rounded-lg bg-white p-6 shadow-sm">
@csrf
@if($warehouse->exists) @method('PUT') @endif
<div>
    <h1 class="text-xl font-bold">{{ $warehouse->exists ? 'Edit Warehouse' : 'Tambah Warehouse' }}</h1>
    <p class="text-sm text-slate-500">Warehouse aktif dapat dipakai untuk lokasi, mutasi stok, dan opname.</p>
</div>
<div class="grid gap-4 md:grid-cols-2">
    <x-form-input label="Kode Warehouse" name="code" :value="$warehouse->code" />
    <x-form-input label="Nama Warehouse" name="name" :value="$warehouse->name" />
    <label class="block md:col-span-2">
        <span class="text-sm font-medium">Alamat</span>
        <textarea name="address" rows="4" class="mt-1 w-full rounded-lg border-slate-300 shadow-sm focus:border-slate-900 focus:ring-slate-900">{{ old('address', $warehouse->address) }}</textarea>
    </label>
    <label class="block rounded-lg border border-slate-200 p-4 md:col-span-2">
        <span class="flex items-center gap-3">
            <input type="checkbox" name="is_active" value="1" @checked(old('is_active', $warehouse->is_active ?? true))
                   class="rounded border-slate-300 text-slate-950 focus:ring-slate-900">
            <span><span class="block text-sm font-medium">Warehouse aktif</span><span class="text-xs text-slate-500">Warehouse nonaktif tidak muncul di form transaksi baru.</span></span>
        </span>
    </label>
</div>
<div class="flex gap-2">
    <button class="rounded-lg bg-slate-950 px-5 py-3 text-white">Simpan</button>
    <a href="{{ route('warehouses.index') }}" class="rounded-lg bg-slate-100 px-5 py-3 text-slate-700">Batal</a>
</div>
</form>
@endsection
