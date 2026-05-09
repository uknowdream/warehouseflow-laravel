@extends('layouts.app')
@section('title', 'Stock Opname')
@section('content')
<div class="mb-5 flex flex-col gap-3 md:flex-row md:items-center md:justify-between">
    <div>
        <h1 class="text-2xl font-bold">Stock Opname</h1>
        <p class="text-sm text-slate-500">Siklus hitung fisik, approval, dan adjustment stok.</p>
    </div>
    @if(auth()->user()?->canOperateStock())
        <a href="{{ route('stock-opnames.create') }}" class="rounded-lg bg-slate-950 px-4 py-2 text-white">Buat Opname</a>
    @endif
</div>

<div class="mb-4 grid gap-3 md:grid-cols-4">
    <div class="rounded-lg bg-white p-4 shadow-sm"><div class="text-xs font-semibold uppercase text-slate-500">Total Sesi</div><div class="mt-2 text-2xl font-bold">{{ $summary['total'] }}</div></div>
    <div class="rounded-lg bg-white p-4 shadow-sm"><div class="text-xs font-semibold uppercase text-slate-500">Berjalan</div><div class="mt-2 text-2xl font-bold text-blue-700">{{ $summary['running'] }}</div></div>
    <div class="rounded-lg bg-white p-4 shadow-sm"><div class="text-xs font-semibold uppercase text-slate-500">Menunggu</div><div class="mt-2 text-2xl font-bold text-amber-700">{{ $summary['waiting'] }}</div></div>
    <div class="rounded-lg bg-white p-4 shadow-sm"><div class="text-xs font-semibold uppercase text-slate-500">Selesai</div><div class="mt-2 text-2xl font-bold text-emerald-700">{{ $summary['done'] }}</div></div>
</div>

<form method="GET" action="{{ route('stock-opnames.index') }}" class="mb-4 rounded-lg bg-white p-4 shadow-sm">
    <div class="grid gap-3 lg:grid-cols-[1fr_220px_220px_auto]">
        <input type="text" name="search" value="{{ $search }}" placeholder="Cari nomor opname atau catatan"
               class="rounded-lg border-slate-300 shadow-sm focus:border-slate-900 focus:ring-slate-900">
        <select name="warehouse_id" class="rounded-lg border-slate-300 shadow-sm focus:border-slate-900 focus:ring-slate-900">
            <option value="">Semua warehouse</option>
            @foreach($warehouses as $warehouse)
                <option value="{{ $warehouse->id }}" @selected((string) $selectedWarehouse === (string) $warehouse->id)>{{ $warehouse->name }}</option>
            @endforeach
        </select>
        <select name="status" class="rounded-lg border-slate-300 shadow-sm focus:border-slate-900 focus:ring-slate-900">
            <option value="">Semua status</option>
            @foreach($statusOptions as $value => $label)
                <option value="{{ $value }}" @selected($selectedStatus === $value)>{{ $label }}</option>
            @endforeach
        </select>
        <div class="flex gap-2">
            <button class="rounded-lg bg-slate-950 px-4 py-2 text-white">Filter</button>
            @if($search || $selectedWarehouse || $selectedStatus)
                <a href="{{ route('stock-opnames.index') }}" class="rounded-lg bg-slate-100 px-4 py-2 text-slate-700">Reset</a>
            @endif
        </div>
    </div>
</form>

<div class="overflow-hidden rounded-lg bg-white shadow-sm">
    <table class="w-full min-w-[920px] text-sm">
        <thead class="bg-slate-50">
            <tr><th class="p-3 text-left">No</th><th class="p-3 text-left">Warehouse</th><th class="p-3 text-left">Status</th><th class="p-3 text-right">Line</th><th class="p-3 text-left">Dibuat Oleh</th><th class="p-3 text-left">Tanggal</th><th class="p-3 text-center">Aksi</th></tr>
        </thead>
        <tbody>
        @forelse($sessions as $session)
            <tr class="border-t">
                <td class="p-3 font-semibold">{{ $session->opname_no }}</td>
                <td class="p-3">{{ $session->warehouse->name ?? '-' }}</td>
                <td class="p-3">{{ $statusOptions[$session->status] ?? $session->status }}</td>
                <td class="p-3 text-right">{{ $session->lines_count }}</td>
                <td class="p-3">{{ $session->creator->name ?? '-' }}</td>
                <td class="p-3">{{ $session->created_at->format('d/m/Y H:i') }}</td>
                <td class="p-3">
                    <div class="flex items-center justify-center gap-3">
                        @if(auth()->user()?->canOperateStock() && in_array($session->status, ['draft', 'running'], true))
                            <a class="text-blue-600" href="{{ route('stock-opnames.scan', $session) }}">Scan</a>
                        @endif
                        <a class="text-slate-700" href="{{ route('stock-opnames.show', $session) }}">Detail</a>
                    </div>
                </td>
            </tr>
        @empty
            <tr class="border-t"><td colspan="7" class="p-6 text-center text-slate-500">Tidak ada opname sesuai filter.</td></tr>
        @endforelse
        </tbody>
    </table>
</div>
<div class="mt-4">{{ $sessions->links() }}</div>
@endsection
