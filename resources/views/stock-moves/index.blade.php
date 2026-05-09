@extends('layouts.app')
@section('title', 'Mutasi Stok')
@section('content')
<div class="flex flex-wrap gap-2 justify-between mb-4">
    <h1 class="text-2xl font-bold">Mutasi Stok</h1>
    <div class="space-x-2">
        <a href="{{ route('stock-moves.in.create') }}" class="px-4 py-2 bg-green-700 text-white rounded-xl">Barang Masuk</a>
        <a href="{{ route('stock-moves.out.create') }}" class="px-4 py-2 bg-red-700 text-white rounded-xl">Barang Keluar</a>
        <a href="{{ route('stock-moves.transfer.create') }}" class="px-4 py-2 bg-blue-700 text-white rounded-xl">Transfer</a>
    </div>
</div>
<div class="bg-white rounded-2xl shadow overflow-hidden">
<table class="w-full text-sm">
<thead class="bg-slate-50"><tr><th class="p-3 text-left">No</th><th class="p-3 text-left">Type</th><th class="p-3 text-left">Produk</th><th class="p-3 text-left">Lokasi</th><th class="p-3 text-left">Qty</th><th class="p-3 text-left">Tanggal</th></tr></thead>
<tbody>
@foreach($moves as $move)
<tr class="border-t">
<td class="p-3">{{ $move->transaction_no }}</td>
<td class="p-3">{{ $move->transaction_type }}</td>
<td class="p-3">{{ $move->product->name ?? '-' }}</td>
<td class="p-3">{{ $move->location->code ?? '-' }}</td>
<td class="p-3">{{ $move->qty_in > 0 ? '+'.$move->qty_in : '-'.$move->qty_out }}</td>
<td class="p-3">{{ $move->created_at->format('d/m/Y H:i') }}</td>
</tr>
@endforeach
</tbody>
</table>
</div>
@endsection
