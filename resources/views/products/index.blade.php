@extends('layouts.app')
@section('title', 'Produk')
@section('content')
<div class="mb-5 flex flex-col gap-3 md:flex-row md:items-center md:justify-between">
    <div>
        <h1 class="text-2xl font-bold">Produk</h1>
        <p class="text-sm text-slate-500">Master produk, kategori, satuan, QR, dan status pemakaian.</p>
    </div>
    @if(auth()->user()?->canManageMasterData())
        <a href="{{ route('products.create') }}" class="rounded-lg bg-slate-950 px-4 py-2 text-white">Tambah Produk</a>
    @endif
</div>

<div class="mb-4 grid gap-3 md:grid-cols-3">
    <div class="rounded-lg bg-white p-4 shadow-sm"><div class="text-xs font-semibold uppercase text-slate-500">Total Produk</div><div class="mt-2 text-2xl font-bold">{{ $summary['total'] }}</div></div>
    <div class="rounded-lg bg-white p-4 shadow-sm"><div class="text-xs font-semibold uppercase text-slate-500">Aktif</div><div class="mt-2 text-2xl font-bold text-emerald-700">{{ $summary['active'] }}</div></div>
    <div class="rounded-lg bg-white p-4 shadow-sm"><div class="text-xs font-semibold uppercase text-slate-500">Nonaktif</div><div class="mt-2 text-2xl font-bold text-rose-700">{{ $summary['inactive'] }}</div></div>
</div>

<form method="GET" action="{{ route('products.index') }}" class="mb-4 rounded-lg bg-white p-4 shadow-sm">
    <div class="grid gap-3 lg:grid-cols-[1fr_180px_160px_160px_auto]">
        <input type="text" name="search" value="{{ $search }}" placeholder="Cari kode, nama, atau QR"
               class="rounded-lg border-slate-300 shadow-sm focus:border-slate-900 focus:ring-slate-900">
        <select name="category_id" class="rounded-lg border-slate-300 shadow-sm focus:border-slate-900 focus:ring-slate-900">
            <option value="">Semua kategori</option>
            @foreach($categories as $category)
                <option value="{{ $category->id }}" @selected((string) $selectedCategory === (string) $category->id)>{{ $category->name }}</option>
            @endforeach
        </select>
        <select name="unit_id" class="rounded-lg border-slate-300 shadow-sm focus:border-slate-900 focus:ring-slate-900">
            <option value="">Semua satuan</option>
            @foreach($units as $unit)
                <option value="{{ $unit->id }}" @selected((string) $selectedUnit === (string) $unit->id)>{{ $unit->symbol }}</option>
            @endforeach
        </select>
        <select name="status" class="rounded-lg border-slate-300 shadow-sm focus:border-slate-900 focus:ring-slate-900">
            <option value="">Semua status</option>
            <option value="active" @selected($selectedStatus === 'active')>Aktif</option>
            <option value="inactive" @selected($selectedStatus === 'inactive')>Nonaktif</option>
        </select>
        <div class="flex gap-2">
            <button class="rounded-lg bg-slate-950 px-4 py-2 text-white">Filter</button>
            @if($search || $selectedCategory || $selectedUnit || $selectedStatus)
                <a href="{{ route('products.index') }}" class="rounded-lg bg-slate-100 px-4 py-2 text-slate-700">Reset</a>
            @endif
        </div>
    </div>
</form>

<div class="overflow-hidden rounded-lg bg-white shadow-sm">
    <table class="w-full min-w-[940px] text-sm">
        <thead class="bg-slate-50">
            <tr>
                <th class="p-3 text-left">Kode</th>
                <th class="p-3 text-left">Nama</th>
                <th class="p-3 text-left">Kategori</th>
                <th class="p-3 text-left">Satuan</th>
                <th class="p-3 text-right">Stok</th>
                <th class="p-3 text-left">Status</th>
                <th class="p-3 text-center">Aksi</th>
            </tr>
        </thead>
        <tbody>
        @forelse($products as $product)
            <tr class="border-t">
                <td class="p-3 font-semibold">{{ $product->code }}</td>
                <td class="p-3">{{ $product->name }}</td>
                <td class="p-3">{{ $product->category->name ?? '-' }}</td>
                <td class="p-3">{{ $product->unit->symbol ?? '-' }}</td>
                <td class="p-3 text-right font-semibold">{{ number_format((float) ($product->total_stock ?? 0), 2) }}</td>
                <td class="p-3">
                    <span class="rounded-full px-3 py-1 text-xs font-semibold {{ $product->is_active ? 'bg-emerald-100 text-emerald-800' : 'bg-rose-100 text-rose-800' }}">
                        {{ $product->is_active ? 'Aktif' : 'Nonaktif' }}
                    </span>
                </td>
                <td class="p-3">
                    <div class="flex items-center justify-center gap-3">
                        <a class="text-blue-600" href="{{ route('products.qr', $product) }}">QR</a>
                        @if(auth()->user()?->canManageMasterData())
                            <a class="text-yellow-600" href="{{ route('products.edit', $product) }}">Edit</a>
                            <form method="POST" action="{{ route('products.destroy', $product) }}" onsubmit="return confirm('Hapus produk ini?')">
                                @csrf
                                @method('DELETE')
                                <button class="text-red-600">Hapus</button>
                            </form>
                        @endif
                    </div>
                </td>
            </tr>
        @empty
            <tr class="border-t">
                <td colspan="7" class="p-6 text-center text-slate-500">Tidak ada produk sesuai filter.</td>
            </tr>
        @endforelse
        </tbody>
    </table>
</div>
<div class="mt-4">{{ $products->links() }}</div>
@endsection
