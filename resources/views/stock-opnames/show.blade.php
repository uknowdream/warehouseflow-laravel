@extends('layouts.app')
@section('title', 'Detail Stock Opname')
@section('content')
<div class="bg-white p-5 rounded-2xl shadow mb-6">
    <h1 class="text-2xl font-bold">{{ $session->opname_no }}</h1>
    <p>Warehouse: {{ $session->warehouse->name ?? '-' }}</p>
    <p>Status: {{ $session->status }}</p>
</div>

<div class="bg-white rounded-2xl shadow overflow-hidden">
<table class="w-full text-sm">
<thead class="bg-slate-50"><tr><th class="p-3 text-left">Produk</th><th class="p-3 text-left">Lokasi</th><th class="p-3 text-right">System</th><th class="p-3 text-right">Fisik</th><th class="p-3 text-right">Selisih</th></tr></thead>
<tbody>
@foreach($session->lines as $line)
<tr class="border-t">
<td class="p-3">{{ $line->product->code }} - {{ $line->product->name }}</td>
<td class="p-3">{{ $line->location->code }}</td>
<td class="p-3 text-right">{{ $line->system_qty }}</td>
<td class="p-3 text-right">{{ $line->physical_qty }}</td>
<td class="p-3 text-right font-semibold {{ $line->difference_qty < 0 ? 'text-red-600' : 'text-green-700' }}">{{ $line->difference_qty }}</td>
</tr>
@endforeach
</tbody>
</table>
</div>

@if($session->status === 'waiting_approval')
<div class="flex gap-3 mt-6">
    <form method="POST" action="{{ route('stock-opnames.approve', $session) }}">@csrf<button class="px-5 py-3 bg-green-700 text-white rounded-xl">Approve Adjustment</button></form>
    <form method="POST" action="{{ route('stock-opnames.reject', $session) }}">@csrf<button class="px-5 py-3 bg-red-700 text-white rounded-xl">Reject</button></form>
</div>
@endif
@endsection
