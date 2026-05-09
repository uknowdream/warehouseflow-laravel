<?php

namespace App\Http\Controllers;

use App\Models\Location;
use App\Models\AuditLog;
use App\Models\Product;
use App\Models\StockBalance;
use App\Models\StockMove;
use App\Models\StockOpnameLine;
use App\Models\StockOpnameSession;
use App\Models\User;
use App\Models\Warehouse;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $stockByWarehouse = StockBalance::query()
            ->select('warehouse_id', DB::raw('SUM(qty) as total_qty'))
            ->with('warehouse')
            ->groupBy('warehouse_id')
            ->orderByDesc('total_qty')
            ->take(5)
            ->get();

        $topProducts = StockBalance::query()
            ->select('product_id', DB::raw('SUM(qty) as total_qty'))
            ->with('product.unit')
            ->groupBy('product_id')
            ->orderByDesc('total_qty')
            ->take(5)
            ->get();

        $opnameStatusCounts = StockOpnameSession::query()
            ->select('status', DB::raw('COUNT(*) as total'))
            ->groupBy('status')
            ->pluck('total', 'status');

        $roleCounts = User::query()
            ->select('role', DB::raw('COUNT(*) as total'))
            ->groupBy('role')
            ->pluck('total', 'role');

        return view('dashboard', [
            'totalProducts' => Product::count(),
            'activeProducts' => Product::where('is_active', true)->count(),
            'totalWarehouses' => Warehouse::count(),
            'activeWarehouses' => Warehouse::where('is_active', true)->count(),
            'totalLocations' => Location::count(),
            'activeLocations' => Location::where('is_active', true)->count(),
            'totalStock' => StockBalance::sum('qty'),
            'stockInToday' => StockMove::whereDate('created_at', today())->sum('qty_in'),
            'stockOutToday' => StockMove::whereDate('created_at', today())->sum('qty_out'),
            'todayMoveCount' => StockMove::whereDate('created_at', today())->count(),
            'todayMoves' => StockMove::with(['product', 'warehouse', 'location', 'user'])
                ->whereDate('created_at', today())
                ->latest()
                ->take(8)
                ->get(),
            'recentMoves' => StockMove::with(['product', 'warehouse', 'location', 'user'])
                ->latest()
                ->take(8)
                ->get(),
            'runningOpnames' => StockOpnameSession::where('status', 'running')->count(),
            'pendingOpnames' => StockOpnameSession::where('status', 'waiting_approval')->count(),
            'opnameDifferences' => StockOpnameLine::where('difference_qty', '!=', 0)->count(),
            'stockByWarehouse' => $stockByWarehouse,
            'topProducts' => $topProducts,
            'opnameStatusCounts' => $opnameStatusCounts,
            'roleCounts' => $roleCounts,
            'totalUsers' => User::count(),
            'activeUsers' => User::where('is_active', true)->count(),
            'inactiveUsers' => User::where('is_active', false)->count(),
            'latestUsers' => User::latest('last_login_at')->latest()->take(5)->get(),
            'roleOptions' => User::roleOptions(),
            'latestAuditLogs' => AuditLog::with('user')->latest()->take(5)->get(),
            'auditLogCountToday' => AuditLog::whereDate('created_at', today())->count(),
        ]);
    }
}
