@extends('layouts.app')
@section('title', $product->exists ? 'Edit Produk' : 'Tambah Produk')
@section('content')
<form method="POST" action="{{ $product->exists ? route('products.update', $product) : route('products.store') }}" class="bg-white p-6 rounded-2xl shadow space-y-4">
@csrf
@if($product->exists) @method('PUT') @endif

<div class="grid md:grid-cols-2 gap-4">
    <x-form-input label="Kode Produk" name="code" :value="$product->code" />
    <x-form-input label="Nama Produk" name="name" :value="$product->name" />
    <label><span class="text-sm font-medium">Kategori</span>
        <select name="category_id" class="mt-1 w-full rounded-xl border-slate-300">
            <option value="">- Pilih -</option>
            @foreach($categories as $category)
                <option value="{{ $category->id }}" @selected(old('category_id', $product->category_id) == $category->id)>{{ $category->name }}</option>
            @endforeach
        </select>
    </label>
    <label><span class="text-sm font-medium">Satuan</span>
        <select name="unit_id" class="mt-1 w-full rounded-xl border-slate-300">
            <option value="">- Pilih -</option>
            @foreach($units as $unit)
                <option value="{{ $unit->id }}" @selected(old('unit_id', $product->unit_id) == $unit->id)>{{ $unit->name }}</option>
            @endforeach
        </select>
    </label>
</div>

<input type="hidden" name="is_active" value="1">
<button class="px-5 py-3 bg-slate-950 text-white rounded-xl">Simpan</button>
</form>
@endsection
