<?php

namespace App\Http\Controllers;

use App\Models\Location;
use App\Models\Product;
use App\Models\StockMove;
use App\Models\Warehouse;
use App\Services\StockService;
use Illuminate\Http\Request;
use RuntimeException;

class StockMoveController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->query('search');
        $type = $request->query('type');
        $warehouseId = $request->query('warehouse_id');
        $date = $request->query('date');

        return view('stock-moves.index', [
            'moves' => StockMove::query()
                ->with(['product', 'warehouse', 'location', 'user'])
                ->when($search, function ($query, string $search): void {
                    $query->where(function ($query) use ($search): void {
                        $query->where('transaction_no', 'like', "%{$search}%")
                            ->orWhere('note', 'like', "%{$search}%")
                            ->orWhereHas('product', fn ($query) => $query->where('name', 'like', "%{$search}%")->orWhere('code', 'like', "%{$search}%"))
                            ->orWhereHas('location', fn ($query) => $query->where('code', 'like', "%{$search}%"));
                    });
                })
                ->when($type, fn ($query, string $type) => $query->where('transaction_type', $type))
                ->when($warehouseId, fn ($query, string $warehouseId) => $query->where('warehouse_id', $warehouseId))
                ->when($date, fn ($query, string $date) => $query->whereDate('created_at', $date))
                ->latest()
                ->paginate(20)
                ->withQueryString(),
            'search' => $search,
            'selectedType' => $type,
            'selectedWarehouse' => $warehouseId,
            'selectedDate' => $date,
            'warehouses' => Warehouse::orderBy('name')->get(),
            'types' => [
                'IN' => 'Barang Masuk',
                'OUT' => 'Barang Keluar',
                'TRANSFER_IN' => 'Transfer Masuk',
                'TRANSFER_OUT' => 'Transfer Keluar',
                'ADJUSTMENT' => 'Adjustment',
            ],
            'summary' => [
                'total' => StockMove::count(),
                'today' => StockMove::whereDate('created_at', today())->count(),
                'inToday' => StockMove::whereDate('created_at', today())->sum('qty_in'),
                'outToday' => StockMove::whereDate('created_at', today())->sum('qty_out'),
            ],
        ]);
    }

    public function createIn()
    {
        return $this->form('IN');
    }

    public function createOut()
    {
        return $this->form('OUT');
    }

    public function createTransfer()
    {
        return view('stock-moves.transfer', $this->data());
    }

    public function storeIn(Request $request, StockService $stock)
    {
        $data = $this->validatedMove($request);
        $stock->moveIn($data['product_id'], $data['warehouse_id'], $data['location_id'], $data['qty'], 'IN', $data['note'] ?? null, auth()->id());

        return redirect()->route('stock-moves.index')->with('success', 'Barang masuk berhasil disimpan.');
    }

    public function storeOut(Request $request, StockService $stock)
    {
        $data = $this->validatedMove($request);

        try {
            $stock->moveOut($data['product_id'], $data['warehouse_id'], $data['location_id'], $data['qty'], 'OUT', $data['note'] ?? null, auth()->id());
        } catch (RuntimeException $e) {
            return back()->withErrors($e->getMessage())->withInput();
        }

        return redirect()->route('stock-moves.index')->with('success', 'Barang keluar berhasil disimpan.');
    }

    public function storeTransfer(Request $request, StockService $stock)
    {
        $data = $request->validate([
            'product_id' => ['required', 'exists:products,id'],
            'warehouse_id' => ['required', 'exists:warehouses,id'],
            'from_location_id' => ['required', 'exists:locations,id', 'different:to_location_id'],
            'to_location_id' => ['required', 'exists:locations,id'],
            'qty' => ['required', 'numeric', 'min:0.01'],
            'note' => ['nullable', 'string'],
        ]);

        try {
            $stock->transfer($data['product_id'], $data['warehouse_id'], $data['from_location_id'], $data['to_location_id'], $data['qty'], $data['note'] ?? null, auth()->id());
        } catch (RuntimeException $e) {
            return back()->withErrors($e->getMessage())->withInput();
        }

        return redirect()->route('stock-moves.index')->with('success', 'Transfer lokasi berhasil disimpan.');
    }

    private function form(string $type)
    {
        return view('stock-moves.form', array_merge($this->data(), ['type' => $type]));
    }

    private function data(): array
    {
        return [
            'products' => Product::where('is_active', true)->orderBy('name')->get(),
            'warehouses' => Warehouse::where('is_active', true)->orderBy('name')->get(),
            'locations' => Location::with('warehouse')->where('is_active', true)->orderBy('code')->get(),
        ];
    }

    private function validatedMove(Request $request): array
    {
        return $request->validate([
            'product_id' => ['required', 'exists:products,id'],
            'warehouse_id' => ['required', 'exists:warehouses,id'],
            'location_id' => ['required', 'exists:locations,id'],
            'qty' => ['required', 'numeric', 'min:0.01'],
            'note' => ['nullable', 'string'],
        ]);
    }
}
