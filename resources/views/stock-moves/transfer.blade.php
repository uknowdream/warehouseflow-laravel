@extends('layouts.app')
@section('title', 'Transfer Lokasi')
@section('content')
<form method="POST" action="{{ route('stock-moves.transfer.store') }}" class="bg-white p-6 rounded-2xl shadow space-y-4">
@csrf
<label><span class="text-sm font-medium">Produk</span><select name="product_id" class="mt-1 w-full rounded-xl border-slate-300">@foreach($products as $product)<option value="{{ $product->id }}">{{ $product->code }} - {{ $product->name }}</option>@endforeach</select></label>
<label><span class="text-sm font-medium">Warehouse</span><select name="warehouse_id" class="mt-1 w-full rounded-xl border-slate-300">@foreach($warehouses as $warehouse)<option value="{{ $warehouse->id }}">{{ $warehouse->name }}</option>@endforeach</select></label>
<label><span class="text-sm font-medium">Dari Lokasi</span><select name="from_location_id" class="mt-1 w-full rounded-xl border-slate-300">@foreach($locations as $location)<option value="{{ $location->id }}">{{ $location->code }} - {{ $location->name }}</option>@endforeach</select></label>
<label><span class="text-sm font-medium">Ke Lokasi</span><select name="to_location_id" class="mt-1 w-full rounded-xl border-slate-300">@foreach($locations as $location)<option value="{{ $location->id }}">{{ $location->code }} - {{ $location->name }}</option>@endforeach</select></label>
<x-form-input label="Qty" name="qty" type="number" value="1" />
<label class="block"><span class="text-sm font-medium">Catatan</span><textarea name="note" class="mt-1 w-full rounded-xl border-slate-300">{{ old('note') }}</textarea></label>
<button class="px-5 py-3 bg-slate-950 text-white rounded-xl">Simpan Transfer</button>
</form>
@endsection
