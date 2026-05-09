@extends('layouts.app')
@section('title', 'Produk')
@section('content')
<div class="flex justify-between mb-4">
    <h1 class="text-2xl font-bold">Produk</h1>
    @if(auth()->user()?->canManageMasterData())
        <a href="{{ route('products.create') }}" class="rounded-lg bg-slate-950 px-4 py-2 text-white">Tambah Produk</a>
    @endif
</div>
<div class="bg-white rounded-2xl shadow overflow-hidden">
<table class="w-full text-sm">
<thead class="bg-slate-50"><tr><th class="p-3 text-left">Kode</th><th class="p-3 text-left">Nama</th><th class="p-3 text-left">Kategori</th><th class="p-3 text-left">Satuan</th><th class="p-3">Aksi</th></tr></thead>
<tbody>
@foreach($products as $product)
<tr class="border-t">
<td class="p-3">{{ $product->code }}</td>
<td class="p-3">{{ $product->name }}</td>
<td class="p-3">{{ $product->category->name ?? '-' }}</td>
<td class="p-3">{{ $product->unit->symbol ?? '-' }}</td>
<td class="p-3 text-center space-x-2">
    <a class="text-blue-600" href="{{ route('products.qr', $product) }}">QR</a>
    @if(auth()->user()?->canManageMasterData())
        <a class="text-yellow-600" href="{{ route('products.edit', $product) }}">Edit</a>
    @endif
</td>
</tr>
@endforeach
</tbody>
</table>
</div>
<div class="mt-4">{{ $products->links() }}</div>
@endsection
