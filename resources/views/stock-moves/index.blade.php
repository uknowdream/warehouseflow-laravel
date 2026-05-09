@extends('layouts.app')
@section('title', 'Mutasi Stok')
@section('content')
<div class="mb-5 flex flex-col gap-3 md:flex-row md:items-center md:justify-between">
    <div>
        <h1 class="text-2xl font-bold">Mutasi Stok</h1>
        <p class="text-sm text-slate-500">Riwayat immutable untuk barang masuk, keluar, transfer, dan adjustment.</p>
    </div>
    @if(auth()->user()?->canOperateStock())
        <div class="flex flex-wrap gap-2">
            <a href="{{ route('stock-moves.in.create') }}" class="rounded-lg bg-green-700 px-4 py-2 text-white">Barang Masuk</a>
            <a href="{{ route('stock-moves.out.create') }}" class="rounded-lg bg-red-700 px-4 py-2 text-white">Barang Keluar</a>
            <a href="{{ route('stock-moves.transfer.create') }}" class="rounded-lg bg-blue-700 px-4 py-2 text-white">Transfer</a>
        </div>
    @endif
</div>

<div class="mb-4 grid gap-3 md:grid-cols-4">
    <div class="rounded-lg bg-white p-4 shadow-sm"><div class="text-xs font-semibold uppercase text-slate-500">Total Mutasi</div><div class="mt-2 text-2xl font-bold">{{ $summary['total'] }}</div></div>
    <div class="rounded-lg bg-white p-4 shadow-sm"><div class="text-xs font-semibold uppercase text-slate-500">Hari Ini</div><div class="mt-2 text-2xl font-bold">{{ $summary['today'] }}</div></div>
    <div class="rounded-lg bg-white p-4 shadow-sm"><div class="text-xs font-semibold uppercase text-slate-500">Masuk Hari Ini</div><div class="mt-2 text-2xl font-bold text-emerald-700">+{{ number_format((float) $summary['inToday'], 2) }}</div></div>
    <div class="rounded-lg bg-white p-4 shadow-sm"><div class="text-xs font-semibold uppercase text-slate-500">Keluar Hari Ini</div><div class="mt-2 text-2xl font-bold text-rose-700">-{{ number_format((float) $summary['outToday'], 2) }}</div></div>
</div>

<form method="GET" action="{{ route('stock-moves.index') }}" class="mb-4 rounded-lg bg-white p-4 shadow-sm">
    <div class="grid gap-3 lg:grid-cols-[1fr_180px_220px_170px_auto]">
        <input type="text" name="search" value="{{ $search }}" placeholder="Cari no, produk, lokasi, catatan"
               class="rounded-lg border-slate-300 shadow-sm focus:border-slate-900 focus:ring-slate-900">
        <select name="type" class="rounded-lg border-slate-300 shadow-sm focus:border-slate-900 focus:ring-slate-900">
            <option value="">Semua type</option>
            @foreach($types as $value => $label)
                <option value="{{ $value }}" @selected($selectedType === $value)>{{ $label }}</option>
            @endforeach
        </select>
        <select name="warehouse_id" class="rounded-lg border-slate-300 shadow-sm focus:border-slate-900 focus:ring-slate-900">
            <option value="">Semua warehouse</option>
            @foreach($warehouses as $warehouse)
                <option value="{{ $warehouse->id }}" @selected((string) $selectedWarehouse === (string) $warehouse->id)>{{ $warehouse->name }}</option>
            @endforeach
        </select>
        <input type="date" name="date" value="{{ $selectedDate }}" class="rounded-lg border-slate-300 shadow-sm focus:border-slate-900 focus:ring-slate-900">
        <div class="flex gap-2">
            <button class="rounded-lg bg-slate-950 px-4 py-2 text-white">Filter</button>
            @if($search || $selectedType || $selectedWarehouse || $selectedDate)
                <a href="{{ route('stock-moves.index') }}" class="rounded-lg bg-slate-100 px-4 py-2 text-slate-700">Reset</a>
            @endif
        </div>
    </div>
</form>

<div class="overflow-hidden rounded-lg bg-white shadow-sm">
    <table class="w-full min-w-[1040px] text-sm">
        <thead class="bg-slate-50">
            <tr><th class="p-3 text-left">No</th><th class="p-3 text-left">Type</th><th class="p-3 text-left">Produk</th><th class="p-3 text-left">Warehouse/Lokasi</th><th class="p-3 text-right">Masuk</th><th class="p-3 text-right">Keluar</th><th class="p-3 text-left">User</th><th class="p-3 text-left">Tanggal</th></tr>
        </thead>
        <tbody>
        @forelse($moves as $move)
            <tr class="border-t">
                <td class="p-3 font-semibold">{{ $move->transaction_no }}</td>
                <td class="p-3">{{ $types[$move->transaction_type] ?? $move->transaction_type }}</td>
                <td class="p-3">{{ $move->product->code ?? '-' }} - {{ $move->product->name ?? '-' }}</td>
                <td class="p-3">{{ $move->warehouse->code ?? '-' }} / {{ $move->location->code ?? '-' }}</td>
                <td class="p-3 text-right font-semibold text-emerald-700">{{ $move->qty_in > 0 ? number_format((float) $move->qty_in, 2) : '-' }}</td>
                <td class="p-3 text-right font-semibold text-rose-700">{{ $move->qty_out > 0 ? number_format((float) $move->qty_out, 2) : '-' }}</td>
                <td class="p-3">{{ $move->user->name ?? 'System' }}</td>
                <td class="p-3">{{ $move->created_at->format('d/m/Y H:i') }}</td>
            </tr>
        @empty
            <tr class="border-t"><td colspan="8" class="p-6 text-center text-slate-500">Tidak ada mutasi sesuai filter.</td></tr>
        @endforelse
        </tbody>
    </table>
</div>
<div class="mt-4">{{ $moves->links() }}</div>
@endsection
