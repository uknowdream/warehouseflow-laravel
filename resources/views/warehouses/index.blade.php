@extends('layouts.app')
@section('title', 'Warehouse')
@section('content')
<div class="flex justify-between mb-4">
    <h1 class="text-2xl font-bold">Warehouse</h1>
    @if(auth()->user()?->canManageMasterData())
        <a href="{{ route('warehouses.create') }}" class="rounded-lg bg-slate-950 px-4 py-2 text-white">Tambah Warehouse</a>
    @endif
</div>
<div class="bg-white rounded-2xl shadow overflow-hidden">
<table class="w-full text-sm">
<thead class="bg-slate-50"><tr><th class="p-3 text-left">Kode</th><th class="p-3 text-left">Nama</th><th class="p-3 text-left">Alamat</th><th class="p-3">Aksi</th></tr></thead>
<tbody>
@foreach($warehouses as $warehouse)
<tr class="border-t">
<td class="p-3">{{ $warehouse->code }}</td>
<td class="p-3">{{ $warehouse->name }}</td>
<td class="p-3">{{ $warehouse->address }}</td>
<td class="p-3 text-center">
@if(auth()->user()?->canManageMasterData())
    <a class="text-yellow-600" href="{{ route('warehouses.edit', $warehouse) }}">Edit</a>
@else
    <span class="text-slate-400">Read only</span>
@endif
</td>
</tr>
@endforeach
</tbody>
</table>
</div>
@endsection
