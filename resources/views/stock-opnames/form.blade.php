@extends('layouts.app')
@section('title', 'Buat Stock Opname')
@section('content')
<form method="POST" action="{{ route('stock-opnames.store') }}" class="bg-white p-6 rounded-2xl shadow space-y-4">
@csrf
<label><span class="text-sm font-medium">Warehouse</span>
<select name="warehouse_id" class="mt-1 w-full rounded-xl border-slate-300">
@foreach($warehouses as $warehouse)
<option value="{{ $warehouse->id }}">{{ $warehouse->name }}</option>
@endforeach
</select></label>
<label class="block"><span class="text-sm font-medium">Catatan</span><textarea name="note" class="mt-1 w-full rounded-xl border-slate-300">{{ old('note') }}</textarea></label>
<button class="px-5 py-3 bg-slate-950 text-white rounded-xl">Mulai Opname</button>
</form>
@endsection
