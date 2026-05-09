@extends('layouts.app')
@section('title', $location->exists ? 'Edit Lokasi' : 'Tambah Lokasi')
@section('content')
<form method="POST" action="{{ $location->exists ? route('locations.update', $location) : route('locations.store') }}" class="space-y-5 rounded-lg bg-white p-6 shadow-sm">
@csrf
@if($location->exists) @method('PUT') @endif
<div>
    <h1 class="text-xl font-bold">{{ $location->exists ? 'Edit Lokasi' : 'Tambah Lokasi' }}</h1>
    <p class="text-sm text-slate-500">Kode lokasi otomatis menjadi payload QR dengan format LOCATION:KODE.</p>
</div>
<div class="grid gap-4 md:grid-cols-2">
    <label class="block md:col-span-2">
        <span class="text-sm font-medium">Warehouse</span>
        <select name="warehouse_id" class="mt-1 w-full rounded-lg border-slate-300 shadow-sm focus:border-slate-900 focus:ring-slate-900">
            @foreach($warehouses as $warehouse)
                <option value="{{ $warehouse->id }}" @selected(old('warehouse_id', $location->warehouse_id) == $warehouse->id)>{{ $warehouse->code }} - {{ $warehouse->name }}</option>
            @endforeach
        </select>
    </label>
    <x-form-input label="Kode Lokasi" name="code" :value="$location->code" />
    <x-form-input label="Nama Lokasi" name="name" :value="$location->name" />
    <x-form-input label="Area" name="area" :value="$location->area" />
    <x-form-input label="Rack" name="rack" :value="$location->rack" />
    <label class="block rounded-lg border border-slate-200 p-4 md:col-span-2">
        <span class="flex items-center gap-3">
            <input type="checkbox" name="is_active" value="1" @checked(old('is_active', $location->is_active ?? true))
                   class="rounded border-slate-300 text-slate-950 focus:ring-slate-900">
            <span><span class="block text-sm font-medium">Lokasi aktif</span><span class="text-xs text-slate-500">Lokasi nonaktif tidak muncul di form transaksi baru.</span></span>
        </span>
    </label>
</div>
<div class="flex gap-2">
    <button class="rounded-lg bg-slate-950 px-5 py-3 text-white">Simpan</button>
    <a href="{{ route('locations.index') }}" class="rounded-lg bg-slate-100 px-5 py-3 text-slate-700">Batal</a>
</div>
</form>
@endsection
