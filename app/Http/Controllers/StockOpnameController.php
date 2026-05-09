<?php

namespace App\Http\Controllers;

use App\Models\Location;
use App\Models\Product;
use App\Models\StockBalance;
use App\Models\StockOpnameLine;
use App\Models\StockOpnameSession;
use App\Models\Warehouse;
use App\Services\StockService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class StockOpnameController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->query('search');
        $status = $request->query('status');
        $warehouseId = $request->query('warehouse_id');

        return view('stock-opnames.index', [
            'sessions' => StockOpnameSession::query()
                ->with(['warehouse', 'creator', 'approver'])
                ->withCount('lines')
                ->when($search, function ($query, string $search): void {
                    $query->where(function ($query) use ($search): void {
                        $query->where('opname_no', 'like', "%{$search}%")
                            ->orWhere('note', 'like', "%{$search}%");
                    });
                })
                ->when($status, fn ($query, string $status) => $query->where('status', $status))
                ->when($warehouseId, fn ($query, string $warehouseId) => $query->where('warehouse_id', $warehouseId))
                ->latest()
                ->paginate(15)
                ->withQueryString(),
            'warehouses' => Warehouse::orderBy('name')->get(),
            'search' => $search,
            'selectedStatus' => $status,
            'selectedWarehouse' => $warehouseId,
            'statusOptions' => [
                'draft' => 'Draft',
                'running' => 'Berjalan',
                'waiting_approval' => 'Menunggu Approval',
                'done' => 'Selesai',
                'rejected' => 'Ditolak',
            ],
            'summary' => [
                'total' => StockOpnameSession::count(),
                'running' => StockOpnameSession::where('status', 'running')->count(),
                'waiting' => StockOpnameSession::where('status', 'waiting_approval')->count(),
                'done' => StockOpnameSession::where('status', 'done')->count(),
            ],
        ]);
    }

    public function create()
    {
        return view('stock-opnames.form', [
            'warehouses' => Warehouse::where('is_active', true)->orderBy('name')->get()
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'warehouse_id' => ['required', 'exists:warehouses,id'],
            'note' => ['nullable', 'string'],
        ]);

        $session = StockOpnameSession::create([
            'opname_no' => 'OPN-' . now()->format('YmdHis'),
            'warehouse_id' => $data['warehouse_id'],
            'status' => 'running',
            'started_at' => now(),
            'created_by' => auth()->id(),
            'note' => $data['note'] ?? null,
        ]);

        return redirect()->route('stock-opnames.scan', $session)->with('success', 'Sesi opname dibuat.');
    }

    public function show(StockOpnameSession $stockOpname)
    {
        $stockOpname->load(['warehouse', 'lines.product', 'lines.location']);
        return view('stock-opnames.show', ['session' => $stockOpname]);
    }

    public function scan(StockOpnameSession $stockOpname)
    {
        return view('stock-opnames.scan', ['session' => $stockOpname]);
    }

    public function submitScan(Request $request, StockOpnameSession $stockOpname)
    {
        $data = $request->validate([
            'product_qr' => ['required', 'string'],
            'location_qr' => ['required', 'string'],
            'physical_qty' => ['required', 'numeric', 'min:0'],
            'note' => ['nullable', 'string'],
        ]);

        $productCode = str_replace('PRODUCT:', '', $data['product_qr']);
        $locationCode = str_replace('LOCATION:', '', $data['location_qr']);

        $product = Product::where('code', $productCode)->firstOrFail();
        $location = Location::where('code', $locationCode)->firstOrFail();

        $systemQty = (float) StockBalance::where([
            'product_id' => $product->id,
            'warehouse_id' => $stockOpname->warehouse_id,
            'location_id' => $location->id,
        ])->value('qty');

        $physicalQty = (float) $data['physical_qty'];

        StockOpnameLine::updateOrCreate([
            'opname_session_id' => $stockOpname->id,
            'product_id' => $product->id,
            'location_id' => $location->id,
        ], [
            'system_qty' => $systemQty,
            'physical_qty' => $physicalQty,
            'difference_qty' => $physicalQty - $systemQty,
            'status' => 'counted',
            'counted_by' => auth()->id(),
            'counted_at' => now(),
            'note' => $data['note'] ?? null,
        ]);

        return back()->with('success', 'Hasil scan opname berhasil disimpan.');
    }

    public function submitApproval(StockOpnameSession $stockOpname)
    {
        $stockOpname->update(['status' => 'waiting_approval']);
        return redirect()->route('stock-opnames.show', $stockOpname)->with('success', 'Opname dikirim untuk approval.');
    }

    public function approve(StockOpnameSession $stockOpname, StockService $stock)
    {
        DB::transaction(function () use ($stockOpname, $stock) {
            foreach ($stockOpname->lines as $line) {
                $stock->adjustToPhysical(
                    $line->product_id,
                    $stockOpname->warehouse_id,
                    $line->location_id,
                    (float) $line->physical_qty,
                    'Adjustment dari stock opname ' . $stockOpname->opname_no,
                    auth()->id()
                );

                $line->update(['status' => 'approved']);
            }

            $stockOpname->update([
                'status' => 'done',
                'approved_by' => auth()->id(),
                'finished_at' => now(),
            ]);
        });

        return redirect()->route('stock-opnames.show', $stockOpname)->with('success', 'Stock opname disetujui dan stok sudah disesuaikan.');
    }

    public function reject(StockOpnameSession $stockOpname)
    {
        $stockOpname->update(['status' => 'rejected']);
        return back()->with('success', 'Stock opname ditolak.');
    }
}
