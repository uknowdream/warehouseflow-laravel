<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Location;
use App\Models\Product;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class QrLookupController extends Controller
{
    public function lookup(Request $request): JsonResponse
    {
        $qr = $request->query('qr');

        if (!$qr) {
            return response()->json(['message' => 'QR kosong.'], 422);
        }

        if (str_starts_with($qr, 'PRODUCT:')) {
            $code = str_replace('PRODUCT:', '', $qr);
            $product = Product::with(['category', 'unit'])->where('code', $code)->first();

            return $product
                ? response()->json(['type' => 'product', 'data' => $product])
                : response()->json(['message' => 'Produk tidak ditemukan.'], 404);
        }

        if (str_starts_with($qr, 'LOCATION:')) {
            $code = str_replace('LOCATION:', '', $qr);
            $location = Location::with('warehouse')->where('code', $code)->first();

            return $location
                ? response()->json(['type' => 'location', 'data' => $location])
                : response()->json(['message' => 'Lokasi tidak ditemukan.'], 404);
        }

        return response()->json(['message' => 'Format QR tidak dikenali.'], 422);
    }
}
