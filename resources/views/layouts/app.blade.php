<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>WarehouseFlow</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script src="https://unpkg.com/html5-qrcode"></script>
</head>
<body class="bg-slate-100 text-slate-900">
@php
    $currentUser = auth()->user();
    $navItems = [
        ['label' => 'Dashboard', 'route' => 'dashboard', 'active' => 'dashboard'],
        ['label' => 'Produk', 'route' => 'products.index', 'active' => 'products.*'],
        ['label' => 'Warehouse', 'route' => 'warehouses.index', 'active' => 'warehouses.*'],
        ['label' => 'Lokasi Rak', 'route' => 'locations.index', 'active' => 'locations.*'],
        ['label' => 'Mutasi Stok', 'route' => 'stock-moves.index', 'active' => 'stock-moves.*'],
        ['label' => 'Stock Opname', 'route' => 'stock-opnames.index', 'active' => 'stock-opnames.*'],
    ];

    if ($currentUser?->canManageUsers()) {
        $navItems[] = ['label' => 'User Management', 'route' => 'users.index', 'active' => 'users.*'];
        $navItems[] = ['label' => 'Audit Log', 'route' => 'audit-logs.index', 'active' => 'audit-logs.*'];
    }
@endphp
<div class="min-h-screen flex">
    <aside class="w-72 bg-slate-950 text-white hidden md:block">
        <div class="p-6 text-2xl font-bold">WarehouseFlow</div>
        <nav class="px-4 space-y-2">
            @foreach($navItems as $item)
                <a class="block rounded-lg p-3 text-sm font-medium {{ request()->routeIs($item['active']) ? 'bg-white text-slate-950' : 'text-slate-200 hover:bg-slate-800' }}"
                   href="{{ route($item['route']) }}">
                    {{ $item['label'] }}
                </a>
            @endforeach
        </nav>
    </aside>

    <main class="flex-1">
        <header class="border-b bg-white p-4">
            <div class="flex flex-col gap-3 md:flex-row md:items-center md:justify-between">
                <div>
                    <div class="font-semibold">@yield('title', 'Dashboard')</div>
                    <div class="text-xs text-slate-500">{{ $currentUser?->name }} · {{ $currentUser?->roleLabel() }}</div>
                </div>
                <div class="flex flex-wrap items-center gap-2">
                    <div class="flex gap-2 overflow-x-auto md:hidden">
                        @foreach($navItems as $item)
                            <a class="whitespace-nowrap rounded-lg px-3 py-2 text-sm {{ request()->routeIs($item['active']) ? 'bg-slate-950 text-white' : 'bg-slate-100 text-slate-700' }}"
                               href="{{ route($item['route']) }}">
                                {{ $item['label'] }}
                            </a>
                        @endforeach
                    </div>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button class="rounded-lg bg-rose-50 px-3 py-2 text-sm font-semibold text-rose-700">Logout</button>
                    </form>
                </div>
            </div>
        </header>

        <section class="p-4 md:p-8">
            @if(session('success'))
                <div class="mb-4 p-4 bg-green-100 text-green-800 rounded-xl">{{ session('success') }}</div>
            @endif

            @if($errors->any())
                <div class="mb-4 p-4 bg-red-100 text-red-800 rounded-xl">
                    @foreach($errors->all() as $error)
                        <div>{{ $error }}</div>
                    @endforeach
                </div>
            @endif

            @yield('content')
        </section>
    </main>
</div>
</body>
</html>
