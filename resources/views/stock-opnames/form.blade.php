@extends('layouts.app')
@section('title', 'Buat Stock Opname')
@section('content')
<form method="POST" action="{{ route('stock-opnames.store') }}" class="space-y-5 rounded-lg bg-white p-6 shadow-sm">
@csrf
<div>
    <h1 class="text-xl font-bold">Buat Stock Opname</h1>
    <p class="text-sm text-slate-500">Pilih warehouse, lalu scan produk dan lokasi untuk mencatat kuantitas fisik.</p>
</div>
<div class="grid gap-4 md:grid-cols-2">
    <label class="block">
        <span class="text-sm font-medium">Warehouse</span>
        <select name="warehouse_id" class="mt-1 w-full rounded-lg border-slate-300 shadow-sm focus:border-slate-900 focus:ring-slate-900">
            @foreach($warehouses as $warehouse)
                <option value="{{ $warehouse->id }}" @selected(old('warehouse_id') == $warehouse->id)>{{ $warehouse->code }} - {{ $warehouse->name }}</option>
            @endforeach
        </select>
    </label>
    <label class="block md:col-span-2">
        <span class="text-sm font-medium">Catatan</span>
        <textarea name="note" rows="4" class="mt-1 w-full rounded-lg border-slate-300 shadow-sm focus:border-slate-900 focus:ring-slate-900">{{ old('note') }}</textarea>
    </label>
</div>
<div class="flex gap-2">
    <button class="rounded-lg bg-slate-950 px-5 py-3 text-white">Mulai Opname</button>
    <a href="{{ route('stock-opnames.index') }}" class="rounded-lg bg-slate-100 px-5 py-3 text-slate-700">Batal</a>
</div>
</form>
@endsection
