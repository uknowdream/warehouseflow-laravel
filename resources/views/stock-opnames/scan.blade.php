@extends('layouts.app')
@section('title', 'Scan Stock Opname')
@section('content')
<div class="grid md:grid-cols-2 gap-6">
    <div class="bg-white p-5 rounded-2xl shadow">
        <h1 class="text-xl font-bold mb-2">{{ $session->opname_no }}</h1>
        <p class="text-sm text-slate-500 mb-4">Scan QR produk dan QR lokasi menggunakan kamera HP.</p>
        <div id="reader" class="rounded-xl overflow-hidden"></div>
        <button id="startBtn" class="mt-4 px-4 py-2 bg-slate-950 text-white rounded-xl">Mulai Scan</button>
        <button id="stopBtn" class="mt-4 px-4 py-2 bg-red-700 text-white rounded-xl">Stop</button>
        <div id="scanResult" class="mt-4 p-3 bg-slate-100 rounded-xl text-sm"></div>
    </div>

    <form method="POST" action="{{ route('stock-opnames.scan.submit', $session) }}" class="bg-white p-5 rounded-2xl shadow space-y-4">
        @csrf
        <label class="block"><span class="text-sm font-medium">QR Produk</span><input id="product_qr" name="product_qr" value="{{ old('product_qr') }}" class="mt-1 w-full rounded-xl border-slate-300"></label>
        <label class="block"><span class="text-sm font-medium">QR Lokasi</span><input id="location_qr" name="location_qr" value="{{ old('location_qr') }}" class="mt-1 w-full rounded-xl border-slate-300"></label>
        <x-form-input label="Stok Fisik" name="physical_qty" type="number" value="0" />
        <label class="block"><span class="text-sm font-medium">Catatan</span><textarea name="note" class="mt-1 w-full rounded-xl border-slate-300">{{ old('note') }}</textarea></label>
        <button class="px-5 py-3 bg-green-700 text-white rounded-xl">Simpan Hasil Hitung</button>
    </form>
</div>

<form method="POST" action="{{ route('stock-opnames.submit-approval', $session) }}" class="mt-6">
@csrf
<button class="px-5 py-3 bg-blue-700 text-white rounded-xl">Submit Untuk Approval</button>
</form>

<script>
let html5QrCode = null;
const resultBox = document.getElementById('scanResult');

function setQrValue(decodedText) {
    resultBox.innerText = 'QR terbaca: ' + decodedText;

    if (decodedText.startsWith('PRODUCT:')) {
        document.getElementById('product_qr').value = decodedText;
    } else if (decodedText.startsWith('LOCATION:')) {
        document.getElementById('location_qr').value = decodedText;
    } else {
        resultBox.innerText = 'Format QR tidak dikenali: ' + decodedText;
    }
}

document.getElementById('startBtn').addEventListener('click', async function () {
    html5QrCode = new Html5Qrcode("reader");
    const config = { fps: 10, qrbox: { width: 250, height: 250 } };

    try {
        await html5QrCode.start(
            { facingMode: "environment" },
            config,
            decodedText => setQrValue(decodedText),
            errorMessage => {}
        );
    } catch (err) {
        resultBox.innerText = 'Gagal membuka kamera. Gunakan HTTPS atau localhost, lalu izinkan kamera.';
    }
});

document.getElementById('stopBtn').addEventListener('click', async function () {
    if (html5QrCode) {
        await html5QrCode.stop();
        resultBox.innerText = 'Scanner berhenti.';
    }
});
</script>
@endsection
