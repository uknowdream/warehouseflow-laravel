@extends('layouts.app')
@section('title', $type === 'IN' ? 'Barang Masuk' : 'Barang Keluar')
@section('content')
<form method="POST" action="{{ $type === 'IN' ? route('stock-moves.in.store') : route('stock-moves.out.store') }}" class="bg-white p-6 rounded-2xl shadow space-y-4">
@csrf
@include('stock-moves.partials.product-location-fields')
<x-form-input label="Qty" name="qty" type="number" value="1" />
<label class="block"><span class="text-sm font-medium">Catatan</span><textarea name="note" class="mt-1 w-full rounded-xl border-slate-300">{{ old('note') }}</textarea></label>
<button class="px-5 py-3 bg-slate-950 text-white rounded-xl">Simpan</button>
</form>
@endsection
