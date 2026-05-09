@extends('layouts.app')
@section('title', $location->exists ? 'Edit Lokasi' : 'Tambah Lokasi')
@section('content')
<form method="POST" action="{{ $location->exists ? route('locations.update', $location) : route('locations.store') }}" class="bg-white p-6 rounded-2xl shadow space-y-4">
@csrf
@if($location->exists) @method('PUT') @endif
<label><span class="text-sm font-medium">Warehouse</span>
<select name="warehouse_id" class="mt-1 w-full rounded-xl border-slate-300">
@foreach($warehouses as $warehouse)
<option value="{{ $warehouse->id }}" @selected(old('warehouse_id', $location->warehouse_id) == $warehouse->id)>{{ $warehouse->name }}</option>
@endforeach
</select></label>
<x-form-input label="Kode Lokasi" name="code" :value="$location->code" />
<x-form-input label="Nama Lokasi" name="name" :value="$location->name" />
<x-form-input label="Area" name="area" :value="$location->area" />
<x-form-input label="Rack" name="rack" :value="$location->rack" />
<input type="hidden" name="is_active" value="1">
<button class="px-5 py-3 bg-slate-950 text-white rounded-xl">Simpan</button>
</form>
@endsection
