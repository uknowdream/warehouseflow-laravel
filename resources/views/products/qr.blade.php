@extends('layouts.app')
@section('title', 'QR Produk')
@section('content')
<div class="bg-white p-8 rounded-2xl shadow max-w-md">
    <h1 class="text-2xl font-bold">{{ $product->name }}</h1>
    <p class="text-slate-500 mb-4">{{ $product->code }}</p>
    <div class="p-4 bg-white border inline-block">
        {!! QrCode::size(250)->generate($product->qr_code) !!}
    </div>
    <p class="mt-4 font-mono">{{ $product->qr_code }}</p>
    <button onclick="window.print()" class="mt-4 px-4 py-2 bg-slate-950 text-white rounded-xl">Print QR</button>
</div>
@endsection
