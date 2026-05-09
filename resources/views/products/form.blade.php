@extends('layouts.app')
@section('title', $product->exists ? 'Edit Produk' : 'Tambah Produk')
@section('content')
<form method="POST" action="{{ $product->exists ? route('products.update', $product) : route('products.store') }}" class="space-y-5 rounded-lg bg-white p-6 shadow-sm">
@csrf
@if($product->exists) @method('PUT') @endif

<div>
    <h1 class="text-xl font-bold">{{ $product->exists ? 'Edit Produk' : 'Tambah Produk' }}</h1>
    <p class="text-sm text-slate-500">Kode produk otomatis menjadi payload QR dengan format PRODUCT:KODE.</p>
</div>

<div class="grid gap-4 md:grid-cols-2">
    <x-form-input label="Kode Produk" name="code" :value="$product->code" />
    <x-form-input label="Nama Produk" name="name" :value="$product->name" />
    <label class="block">
        <span class="text-sm font-medium">Kategori</span>
        <select name="category_id" class="mt-1 w-full rounded-lg border-slate-300 shadow-sm focus:border-slate-900 focus:ring-slate-900">
            <option value="">Pilih kategori</option>
            @foreach($categories as $category)
                <option value="{{ $category->id }}" @selected(old('category_id', $product->category_id) == $category->id)>{{ $category->name }}</option>
            @endforeach
        </select>
    </label>
    <label class="block">
        <span class="text-sm font-medium">Satuan</span>
        <select name="unit_id" class="mt-1 w-full rounded-lg border-slate-300 shadow-sm focus:border-slate-900 focus:ring-slate-900">
            <option value="">Pilih satuan</option>
            @foreach($units as $unit)
                <option value="{{ $unit->id }}" @selected(old('unit_id', $product->unit_id) == $unit->id)>{{ $unit->name }} ({{ $unit->symbol }})</option>
            @endforeach
        </select>
    </label>
    <label class="block rounded-lg border border-slate-200 p-4 md:col-span-2">
        <span class="flex items-center gap-3">
            <input type="checkbox" name="is_active" value="1" @checked(old('is_active', $product->is_active ?? true))
                   class="rounded border-slate-300 text-slate-950 focus:ring-slate-900">
            <span>
                <span class="block text-sm font-medium">Produk aktif</span>
                <span class="text-xs text-slate-500">Produk nonaktif disembunyikan dari form transaksi baru.</span>
            </span>
        </span>
    </label>
</div>

<div class="flex gap-2">
    <button class="rounded-lg bg-slate-950 px-5 py-3 text-white">Simpan</button>
    <a href="{{ route('products.index') }}" class="rounded-lg bg-slate-100 px-5 py-3 text-slate-700">Batal</a>
</div>
</form>
@endsection
