@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
@php
    $formatQty = fn ($value) => number_format((float) $value, 2);
    $opnameLabels = [
        'draft' => 'Draft',
        'running' => 'Berjalan',
        'waiting_approval' => 'Menunggu Approval',
        'done' => 'Selesai',
        'rejected' => 'Ditolak',
    ];
    $typeClasses = [
        'IN' => 'bg-emerald-100 text-emerald-800',
        'OUT' => 'bg-rose-100 text-rose-800',
        'TRANSFER_IN' => 'bg-blue-100 text-blue-800',
        'TRANSFER_OUT' => 'bg-amber-100 text-amber-800',
        'ADJUSTMENT' => 'bg-violet-100 text-violet-800',
    ];
@endphp

<div class="mb-6 flex flex-col gap-3 md:flex-row md:items-center md:justify-between">
    <div>
        <h1 class="text-2xl font-bold">Dashboard Operasional</h1>
        <p class="text-sm text-slate-500">Ringkasan stok, opname, dan aktivitas warehouse hari ini.</p>
    </div>
    @if(auth()->user()?->canOperateStock())
        <div class="flex flex-wrap gap-2">
            <a href="{{ route('stock-moves.in.create') }}" class="rounded-lg bg-emerald-700 px-4 py-2 text-sm font-semibold text-white">Barang Masuk</a>
            <a href="{{ route('stock-moves.out.create') }}" class="rounded-lg bg-rose-700 px-4 py-2 text-sm font-semibold text-white">Barang Keluar</a>
            <a href="{{ route('stock-opnames.create') }}" class="rounded-lg bg-slate-950 px-4 py-2 text-sm font-semibold text-white">Stock Opname</a>
        </div>
    @endif
</div>

<div class="grid gap-4 md:grid-cols-2 xl:grid-cols-4">
    <div class="rounded-lg bg-white p-5 shadow-sm">
        <div class="text-xs font-semibold uppercase text-slate-500">Total Produk</div>
        <div class="mt-2 text-3xl font-bold">{{ $totalProducts }}</div>
        <div class="mt-1 text-sm text-emerald-700">{{ $activeProducts }} aktif</div>
    </div>
    <div class="rounded-lg bg-white p-5 shadow-sm">
        <div class="text-xs font-semibold uppercase text-slate-500">Warehouse & Lokasi</div>
        <div class="mt-2 text-3xl font-bold">{{ $totalWarehouses }} / {{ $totalLocations }}</div>
        <div class="mt-1 text-sm text-slate-500">{{ $activeWarehouses }} warehouse aktif, {{ $activeLocations }} lokasi aktif</div>
    </div>
    <div class="rounded-lg bg-white p-5 shadow-sm">
        <div class="text-xs font-semibold uppercase text-slate-500">Total Stok</div>
        <div class="mt-2 text-3xl font-bold">{{ $formatQty($totalStock) }}</div>
        <div class="mt-1 text-sm text-slate-500">{{ $todayMoveCount }} mutasi hari ini</div>
    </div>
    <div class="rounded-lg bg-white p-5 shadow-sm">
        <div class="text-xs font-semibold uppercase text-slate-500">Opname Perlu Atensi</div>
        <div class="mt-2 text-3xl font-bold">{{ $pendingOpnames + $runningOpnames }}</div>
        <div class="mt-1 text-sm text-amber-700">{{ $pendingOpnames }} menunggu approval, {{ $runningOpnames }} berjalan</div>
    </div>
</div>

<div class="mt-4 grid gap-4 md:grid-cols-3">
    <div class="rounded-lg bg-white p-5 shadow-sm">
        <div class="text-xs font-semibold uppercase text-slate-500">Barang Masuk Hari Ini</div>
        <div class="mt-2 text-2xl font-bold text-emerald-700">+{{ $formatQty($stockInToday) }}</div>
    </div>
    <div class="rounded-lg bg-white p-5 shadow-sm">
        <div class="text-xs font-semibold uppercase text-slate-500">Barang Keluar Hari Ini</div>
        <div class="mt-2 text-2xl font-bold text-rose-700">-{{ $formatQty($stockOutToday) }}</div>
    </div>
    <div class="rounded-lg bg-white p-5 shadow-sm">
        <div class="text-xs font-semibold uppercase text-slate-500">Selisih Opname</div>
        <div class="mt-2 text-2xl font-bold text-amber-700">{{ $opnameDifferences }}</div>
    </div>
</div>

<div class="mt-6 grid gap-6 xl:grid-cols-[1.3fr_0.7fr]">
    <div class="overflow-hidden rounded-lg bg-white shadow-sm">
        <div class="flex items-center justify-between border-b p-4">
            <div class="font-semibold">Aktivitas Mutasi Terbaru</div>
            <a href="{{ route('stock-moves.index') }}" class="text-sm font-semibold text-slate-700">Lihat semua</a>
        </div>
        <table class="w-full min-w-[780px] text-sm">
            <thead class="bg-slate-50">
                <tr>
                    <th class="p-3 text-left">No</th>
                    <th class="p-3 text-left">Type</th>
                    <th class="p-3 text-left">Produk</th>
                    <th class="p-3 text-left">Lokasi</th>
                    <th class="p-3 text-right">Qty</th>
                    <th class="p-3 text-left">User</th>
                </tr>
            </thead>
            <tbody>
            @forelse($recentMoves as $move)
                <tr class="border-t">
                    <td class="p-3 font-medium">{{ $move->transaction_no }}</td>
                    <td class="p-3">
                        <span class="rounded-full px-3 py-1 text-xs font-semibold {{ $typeClasses[$move->transaction_type] ?? 'bg-slate-100 text-slate-700' }}">
                            {{ $move->transaction_type }}
                        </span>
                    </td>
                    <td class="p-3">{{ $move->product->name ?? '-' }}</td>
                    <td class="p-3">{{ $move->warehouse->code ?? '-' }} / {{ $move->location->code ?? '-' }}</td>
                    <td class="p-3 text-right font-semibold">
                        {{ $move->qty_in > 0 ? '+'.$formatQty($move->qty_in) : '-'.$formatQty($move->qty_out) }}
                    </td>
                    <td class="p-3">{{ $move->user->name ?? '-' }}</td>
                </tr>
            @empty
                <tr class="border-t">
                    <td colspan="6" class="p-6 text-center text-slate-500">Belum ada mutasi stok.</td>
                </tr>
            @endforelse
            </tbody>
        </table>
    </div>

    <div class="space-y-6">
        <div class="rounded-lg bg-white p-5 shadow-sm">
            <div class="mb-4 font-semibold">Status User</div>
            <div class="grid grid-cols-3 gap-3">
                <div>
                    <div class="text-xs uppercase text-slate-500">Total</div>
                    <div class="text-2xl font-bold">{{ $totalUsers }}</div>
                </div>
                <div>
                    <div class="text-xs uppercase text-slate-500">Aktif</div>
                    <div class="text-2xl font-bold text-emerald-700">{{ $activeUsers }}</div>
                </div>
                <div>
                    <div class="text-xs uppercase text-slate-500">Nonaktif</div>
                    <div class="text-2xl font-bold text-rose-700">{{ $inactiveUsers }}</div>
                </div>
            </div>
            <div class="mt-4 space-y-2">
                @foreach($roleOptions as $role => $label)
                    <div class="flex items-center justify-between text-sm">
                        <span>{{ $label }}</span>
                        <span class="font-semibold">{{ $roleCounts[$role] ?? 0 }}</span>
                    </div>
                @endforeach
            </div>
        </div>

        <div class="rounded-lg bg-white p-5 shadow-sm">
            <div class="mb-4 font-semibold">Status Opname</div>
            <div class="space-y-2">
                @foreach($opnameLabels as $status => $label)
                    <div class="flex items-center justify-between text-sm">
                        <span>{{ $label }}</span>
                        <span class="font-semibold">{{ $opnameStatusCounts[$status] ?? 0 }}</span>
                    </div>
                @endforeach
            </div>
        </div>

        <div class="rounded-lg bg-white p-5 shadow-sm">
            <div class="mb-1 flex items-center justify-between">
                <div class="font-semibold">Audit Log</div>
                @if(auth()->user()?->canManageUsers())
                    <a href="{{ route('audit-logs.index') }}" class="text-sm font-semibold text-slate-700">Lihat</a>
                @endif
            </div>
            <div class="mb-3 text-sm text-slate-500">{{ $auditLogCountToday }} aktivitas hari ini</div>
            <div class="space-y-3">
                @forelse($latestAuditLogs as $log)
                    <div class="border-b border-slate-100 pb-2 text-sm last:border-0 last:pb-0">
                        <div class="font-medium">{{ ucfirst($log->event) }} {{ class_basename($log->auditable_type) }}</div>
                        <div class="text-xs text-slate-500">{{ $log->label }} · {{ $log->user->name ?? 'System' }}</div>
                    </div>
                @empty
                    <div class="text-sm text-slate-500">Belum ada aktivitas.</div>
                @endforelse
            </div>
        </div>
    </div>
</div>

<div class="mt-6 grid gap-6 xl:grid-cols-2">
    <div class="rounded-lg bg-white p-5 shadow-sm">
        <div class="mb-4 font-semibold">Stok per Warehouse</div>
        <div class="space-y-3">
            @forelse($stockByWarehouse as $row)
                <div class="flex items-center justify-between gap-4 border-b border-slate-100 pb-3 text-sm last:border-0 last:pb-0">
                    <div>
                        <div class="font-medium">{{ $row->warehouse->name ?? 'Warehouse tidak ditemukan' }}</div>
                        <div class="text-xs text-slate-500">{{ $row->warehouse->code ?? '-' }}</div>
                    </div>
                    <div class="font-semibold">{{ $formatQty($row->total_qty) }}</div>
                </div>
            @empty
                <div class="text-sm text-slate-500">Belum ada stok tercatat.</div>
            @endforelse
        </div>
    </div>

    <div class="rounded-lg bg-white p-5 shadow-sm">
        <div class="mb-4 font-semibold">Produk dengan Stok Terbesar</div>
        <div class="space-y-3">
            @forelse($topProducts as $row)
                <div class="flex items-center justify-between gap-4 border-b border-slate-100 pb-3 text-sm last:border-0 last:pb-0">
                    <div>
                        <div class="font-medium">{{ $row->product->name ?? 'Produk tidak ditemukan' }}</div>
                        <div class="text-xs text-slate-500">{{ $row->product->code ?? '-' }}</div>
                    </div>
                    <div class="font-semibold">{{ $formatQty($row->total_qty) }} {{ $row->product->unit->symbol ?? '' }}</div>
                </div>
            @empty
                <div class="text-sm text-slate-500">Belum ada stok produk.</div>
            @endforelse
        </div>
    </div>
</div>
@endsection
