@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
<div class="grid grid-cols-1 md:grid-cols-4 gap-4">
    <div class="bg-white p-5 rounded-2xl shadow"><div class="text-sm text-slate-500">Total Produk</div><div class="text-3xl font-bold">{{ $totalProducts }}</div></div>
    <div class="bg-white p-5 rounded-2xl shadow"><div class="text-sm text-slate-500">Warehouse</div><div class="text-3xl font-bold">{{ $totalWarehouses }}</div></div>
    <div class="bg-white p-5 rounded-2xl shadow"><div class="text-sm text-slate-500">Total Stok</div><div class="text-3xl font-bold">{{ number_format($totalStock) }}</div></div>
    <div class="bg-white p-5 rounded-2xl shadow"><div class="text-sm text-slate-500">Selisih Opname</div><div class="text-3xl font-bold">{{ $opnameDifferences }}</div></div>
</div>

<div class="mt-6 bg-white rounded-2xl shadow overflow-hidden">
    <div class="p-5 font-semibold border-b">Mutasi Hari Ini</div>
    <table class="w-full text-sm">
        <thead class="bg-slate-50">
            <tr><th class="p-3 text-left">No</th><th class="p-3 text-left">Type</th><th class="p-3 text-left">Produk</th><th class="p-3 text-left">Qty</th></tr>
        </thead>
        <tbody>
        @foreach($todayMoves as $move)
            <tr class="border-t">
                <td class="p-3">{{ $move->transaction_no }}</td>
                <td class="p-3">{{ $move->transaction_type }}</td>
                <td class="p-3">{{ $move->product->name ?? '-' }}</td>
                <td class="p-3">{{ $move->qty_in > 0 ? '+'.$move->qty_in : '-'.$move->qty_out }}</td>
            </tr>
        @endforeach
        </tbody>
    </table>
</div>
@endsection
