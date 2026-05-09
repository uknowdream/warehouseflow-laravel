@extends('layouts.app')
@section('title', 'Transfer Lokasi')
@section('content')
<form method="POST" action="{{ route('stock-moves.transfer.store') }}" class="space-y-5 rounded-lg bg-white p-6 shadow-sm">
@csrf
<div>
    <h1 class="text-xl font-bold">Transfer Lokasi</h1>
    <p class="text-sm text-slate-500">Transfer membuat dua mutasi otomatis: keluar dari lokasi asal dan masuk ke lokasi tujuan.</p>
</div>
<div class="grid gap-4 md:grid-cols-2">
    <label class="block"><span class="text-sm font-medium">Produk</span><select name="product_id" class="mt-1 w-full rounded-lg border-slate-300 shadow-sm focus:border-slate-900 focus:ring-slate-900">@foreach($products as $product)<option value="{{ $product->id }}" @selected(old('product_id') == $product->id)>{{ $product->code }} - {{ $product->name }}</option>@endforeach</select></label>
    <label class="block"><span class="text-sm font-medium">Warehouse</span><select name="warehouse_id" class="mt-1 w-full rounded-lg border-slate-300 shadow-sm focus:border-slate-900 focus:ring-slate-900">@foreach($warehouses as $warehouse)<option value="{{ $warehouse->id }}" @selected(old('warehouse_id') == $warehouse->id)>{{ $warehouse->name }}</option>@endforeach</select></label>
    <label class="block"><span class="text-sm font-medium">Dari Lokasi</span><select name="from_location_id" class="mt-1 w-full rounded-lg border-slate-300 shadow-sm focus:border-slate-900 focus:ring-slate-900">@foreach($locations as $location)<option value="{{ $location->id }}" @selected(old('from_location_id') == $location->id)>{{ $location->code }} - {{ $location->name }}</option>@endforeach</select></label>
    <label class="block"><span class="text-sm font-medium">Ke Lokasi</span><select name="to_location_id" class="mt-1 w-full rounded-lg border-slate-300 shadow-sm focus:border-slate-900 focus:ring-slate-900">@foreach($locations as $location)<option value="{{ $location->id }}" @selected(old('to_location_id') == $location->id)>{{ $location->code }} - {{ $location->name }}</option>@endforeach</select></label>
    <x-form-input label="Qty" name="qty" type="number" value="1" />
    <label class="block md:col-span-2"><span class="text-sm font-medium">Catatan</span><textarea name="note" rows="4" class="mt-1 w-full rounded-lg border-slate-300 shadow-sm focus:border-slate-900 focus:ring-slate-900">{{ old('note') }}</textarea></label>
</div>
<div class="flex gap-2">
    <button class="rounded-lg bg-slate-950 px-5 py-3 text-white">Simpan Transfer</button>
    <a href="{{ route('stock-moves.index') }}" class="rounded-lg bg-slate-100 px-5 py-3 text-slate-700">Batal</a>
</div>
</form>
@endsection
