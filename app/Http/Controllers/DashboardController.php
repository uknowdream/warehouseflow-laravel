<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\StockBalance;
use App\Models\StockMove;
use App\Models\StockOpnameLine;
use App\Models\Warehouse;

class DashboardController extends Controller
{
    public function index()
    {
        return view('dashboard', [
            'totalProducts' => Product::count(),
            'totalWarehouses' => Warehouse::count(),
            'totalStock' => StockBalance::sum('qty'),
            'todayMoves' => StockMove::whereDate('created_at', today())->latest()->take(10)->get(),
            'opnameDifferences' => StockOpnameLine::where('difference_qty', '!=', 0)->count(),
        ]);
    }
}
