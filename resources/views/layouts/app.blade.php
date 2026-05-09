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
<div class="min-h-screen flex">
    <aside class="w-72 bg-slate-950 text-white hidden md:block">
        <div class="p-6 text-2xl font-bold">WarehouseFlow</div>
        <nav class="px-4 space-y-2">
            <a class="block p-3 rounded hover:bg-slate-800" href="{{ route('dashboard') }}">Dashboard</a>
            <a class="block p-3 rounded hover:bg-slate-800" href="{{ route('products.index') }}">Produk</a>
            <a class="block p-3 rounded hover:bg-slate-800" href="{{ route('warehouses.index') }}">Warehouse</a>
            <a class="block p-3 rounded hover:bg-slate-800" href="{{ route('locations.index') }}">Lokasi Rak</a>
            @if(auth()->user()?->role === \App\Models\User::ROLE_ADMIN)
                <a class="block p-3 rounded hover:bg-slate-800" href="{{ route('users.index') }}">User Management</a>
            @endif
            <a class="block p-3 rounded hover:bg-slate-800" href="{{ route('stock-moves.index') }}">Mutasi Stok</a>
            <a class="block p-3 rounded hover:bg-slate-800" href="{{ route('stock-opnames.index') }}">Stock Opname</a>
        </nav>
    </aside>

    <main class="flex-1">
        <header class="bg-white border-b p-4 flex items-center justify-between">
            <div class="font-semibold">@yield('title', 'Dashboard')</div>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button class="text-sm text-red-600">Logout</button>
            </form>
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
