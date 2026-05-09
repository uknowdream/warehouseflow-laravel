@extends('layouts.app')
@section('title', $warehouse->exists ? 'Edit Warehouse' : 'Tambah Warehouse')
@section('content')
<form method="POST" action="{{ $warehouse->exists ? route('warehouses.update', $warehouse) : route('warehouses.store') }}" class="bg-white p-6 rounded-2xl shadow space-y-4">
@csrf
@if($warehouse->exists) @method('PUT') @endif
<x-form-input label="Kode Warehouse" name="code" :value="$warehouse->code" />
<x-form-input label="Nama Warehouse" name="name" :value="$warehouse->name" />
<label class="block"><span class="text-sm font-medium">Alamat</span><textarea name="address" class="mt-1 w-full rounded-xl border-slate-300">{{ old('address', $warehouse->address) }}</textarea></label>
<input type="hidden" name="is_active" value="1">
<button class="px-5 py-3 bg-slate-950 text-white rounded-xl">Simpan</button>
</form>
@endsection
