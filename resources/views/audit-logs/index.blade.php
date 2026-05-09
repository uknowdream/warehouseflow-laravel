@extends('layouts.app')

@section('title', 'Audit Log')

@section('content')
<div class="mb-5 flex flex-col gap-3 md:flex-row md:items-center md:justify-between">
    <div>
        <h1 class="text-2xl font-bold">Audit Log</h1>
        <p class="text-sm text-slate-500">Jejak create, update, dan delete untuk data operasional warehouse.</p>
    </div>
</div>

<form method="GET" action="{{ route('audit-logs.index') }}" class="mb-4 rounded-lg bg-white p-4 shadow-sm">
    <div class="grid gap-3 md:grid-cols-[1fr_180px_240px_auto]">
        <input type="text" name="search" value="{{ $search }}" placeholder="Cari label, model, atau user"
               class="w-full rounded-lg border-slate-300 shadow-sm focus:border-slate-900 focus:ring-slate-900">
        <select name="event" class="w-full rounded-lg border-slate-300 shadow-sm focus:border-slate-900 focus:ring-slate-900">
            <option value="">Semua event</option>
            @foreach($events as $value => $label)
                <option value="{{ $value }}" @selected($selectedEvent === $value)>{{ $label }}</option>
            @endforeach
        </select>
        <select name="model" class="w-full rounded-lg border-slate-300 shadow-sm focus:border-slate-900 focus:ring-slate-900">
            <option value="">Semua model</option>
            @foreach($models as $model)
                <option value="{{ $model }}" @selected($selectedModel === $model)>{{ class_basename($model) }}</option>
            @endforeach
        </select>
        <div class="flex gap-2">
            <button class="rounded-lg bg-slate-950 px-4 py-2 text-white">Filter</button>
            @if($search || $selectedEvent || $selectedModel)
                <a href="{{ route('audit-logs.index') }}" class="rounded-lg bg-slate-100 px-4 py-2 text-slate-700">Reset</a>
            @endif
        </div>
    </div>
</form>

<div class="overflow-hidden rounded-lg bg-white shadow-sm">
    <table class="w-full min-w-[980px] text-sm">
        <thead class="bg-slate-50">
            <tr>
                <th class="p-3 text-left">Waktu</th>
                <th class="p-3 text-left">Event</th>
                <th class="p-3 text-left">Data</th>
                <th class="p-3 text-left">User</th>
                <th class="p-3 text-left">Perubahan</th>
            </tr>
        </thead>
        <tbody>
        @forelse($logs as $log)
            <tr class="border-t align-top">
                <td class="p-3 whitespace-nowrap">{{ $log->created_at?->format('d M Y H:i') }}</td>
                <td class="p-3">
                    <span class="rounded-full px-3 py-1 text-xs font-semibold {{ $log->event === 'deleted' ? 'bg-rose-100 text-rose-800' : ($log->event === 'updated' ? 'bg-amber-100 text-amber-800' : 'bg-emerald-100 text-emerald-800') }}">
                        {{ ucfirst($log->event) }}
                    </span>
                </td>
                <td class="p-3">
                    <div class="font-semibold">{{ class_basename($log->auditable_type) }}</div>
                    <div class="text-slate-500">{{ $log->label ?? '#'.$log->auditable_id }}</div>
                </td>
                <td class="p-3">
                    <div>{{ $log->user->name ?? 'System' }}</div>
                    <div class="text-xs text-slate-500">{{ $log->ip_address }}</div>
                </td>
                <td class="p-3">
                    @php
                        $newValues = collect($log->new_values ?? [])->take(5);
                        $oldValues = collect($log->old_values ?? []);
                    @endphp
                    @forelse($newValues as $key => $value)
                        <div class="mb-1">
                            <span class="font-semibold">{{ $key }}</span>:
                            @if($oldValues->has($key))
                                <span class="text-slate-500">{{ str($oldValues[$key])->limit(35) }}</span>
                                <span class="text-slate-400">-></span>
                            @endif
                            <span>{{ str($value)->limit(45) }}</span>
                        </div>
                    @empty
                        @foreach($oldValues->take(5) as $key => $value)
                            <div class="mb-1"><span class="font-semibold">{{ $key }}</span>: <span>{{ str($value)->limit(45) }}</span></div>
                        @endforeach
                    @endforelse
                </td>
            </tr>
        @empty
            <tr class="border-t">
                <td colspan="5" class="p-6 text-center text-slate-500">Belum ada log aktivitas.</td>
            </tr>
        @endforelse
        </tbody>
    </table>
</div>

<div class="mt-4">{{ $logs->links() }}</div>
@endsection
