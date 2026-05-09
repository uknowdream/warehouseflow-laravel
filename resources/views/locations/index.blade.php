@extends('layouts.app')
@section('title', 'Lokasi Rak')
@section('content')
<div class="flex justify-between mb-4">
    <h1 class="text-2xl font-bold">Lokasi Rak</h1>
    <a href="{{ route('locations.create') }}" class="px-4 py-2 bg-slate-950 text-white rounded-xl">Tambah Lokasi</a>
</div>
<div class="bg-white rounded-2xl shadow overflow-hidden">
<table class="w-full text-sm">
<thead class="bg-slate-50"><tr><th class="p-3 text-left">Kode</th><th class="p-3 text-left">Nama</th><th class="p-3 text-left">Warehouse</th><th class="p-3">Aksi</th></tr></thead>
<tbody>
@foreach($locations as $location)
<tr class="border-t">
<td class="p-3">{{ $location->code }}</td>
<td class="p-3">{{ $location->name }}</td>
<td class="p-3">{{ $location->warehouse->name ?? '-' }}</td>
<td class="p-3 text-center space-x-2"><a class="text-blue-600" href="{{ route('locations.qr', $location) }}">QR</a><a class="text-yellow-600" href="{{ route('locations.edit', $location) }}">Edit</a></td>
</tr>
@endforeach
</tbody>
</table>
</div>
@endsection
