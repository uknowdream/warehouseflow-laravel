@extends('layouts.app')
@section('title', 'Stock Opname')
@section('content')
<div class="flex justify-between mb-4">
    <h1 class="text-2xl font-bold">Stock Opname</h1>
    <a href="{{ route('stock-opnames.create') }}" class="px-4 py-2 bg-slate-950 text-white rounded-xl">Buat Opname</a>
</div>
<div class="bg-white rounded-2xl shadow overflow-hidden">
<table class="w-full text-sm">
<thead class="bg-slate-50"><tr><th class="p-3 text-left">No</th><th class="p-3 text-left">Warehouse</th><th class="p-3 text-left">Status</th><th class="p-3 text-left">Tanggal</th><th class="p-3">Aksi</th></tr></thead>
<tbody>
@foreach($sessions as $session)
<tr class="border-t">
<td class="p-3">{{ $session->opname_no }}</td>
<td class="p-3">{{ $session->warehouse->name ?? '-' }}</td>
<td class="p-3">{{ $session->status }}</td>
<td class="p-3">{{ $session->created_at->format('d/m/Y H:i') }}</td>
<td class="p-3 text-center space-x-2">
<a class="text-blue-600" href="{{ route('stock-opnames.scan', $session) }}">Scan</a>
<a class="text-slate-700" href="{{ route('stock-opnames.show', $session) }}">Detail</a>
</td>
</tr>
@endforeach
</tbody>
</table>
</div>
@endsection
