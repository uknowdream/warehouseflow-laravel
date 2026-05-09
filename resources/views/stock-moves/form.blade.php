@extends('layouts.app')
@section('title', $type === 'IN' ? 'Barang Masuk' : 'Barang Keluar')
@section('content')
<form method="POST" action="{{ $type === 'IN' ? route('stock-moves.in.store') : route('stock-moves.out.store') }}" class="space-y-5 rounded-lg bg-white p-6 shadow-sm">
@csrf
<div>
    <h1 class="text-xl font-bold">{{ $type === 'IN' ? 'Barang Masuk' : 'Barang Keluar' }}</h1>
    <p class="text-sm text-slate-500">Mutasi stok akan tersimpan sebagai log transaksi yang tidak diedit.</p>
</div>
<div class="grid gap-4 md:grid-cols-2">
    @include('stock-moves.partials.product-location-fields')
    <x-form-input label="Qty" name="qty" type="number" value="1" />
    <label class="block md:col-span-2">
        <span class="text-sm font-medium">Catatan</span>
        <textarea name="note" rows="4" class="mt-1 w-full rounded-lg border-slate-300 shadow-sm focus:border-slate-900 focus:ring-slate-900">{{ old('note') }}</textarea>
    </label>
</div>
<div class="flex gap-2">
    <button class="rounded-lg bg-slate-950 px-5 py-3 text-white">Simpan</button>
    <a href="{{ route('stock-moves.index') }}" class="rounded-lg bg-slate-100 px-5 py-3 text-slate-700">Batal</a>
</div>
</form>
@endsection
