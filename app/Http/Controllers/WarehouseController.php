<?php

namespace App\Http\Controllers;

use App\Models\Warehouse;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class WarehouseController extends Controller
{
    public function index(Request $request): View
    {
        $search = $request->query('search');
        $status = $request->query('status');

        return view('warehouses.index', [
            'warehouses' => Warehouse::query()
                ->withCount(['locations', 'stockMoves', 'stockOpnameSessions'])
                ->when($search, function ($query, string $search): void {
                    $query->where(function ($query) use ($search): void {
                        $query->where('code', 'like', "%{$search}%")
                            ->orWhere('name', 'like', "%{$search}%")
                            ->orWhere('address', 'like', "%{$search}%");
                    });
                })
                ->when($status === 'active', fn ($query) => $query->where('is_active', true))
                ->when($status === 'inactive', fn ($query) => $query->where('is_active', false))
                ->latest()
                ->paginate(15)
                ->withQueryString(),
            'search' => $search,
            'selectedStatus' => $status,
            'summary' => [
                'total' => Warehouse::count(),
                'active' => Warehouse::where('is_active', true)->count(),
                'inactive' => Warehouse::where('is_active', false)->count(),
            ],
        ]);
    }

    public function create()
    {
        return view('warehouses.form', ['warehouse' => new Warehouse()]);
    }

    public function store(Request $request)
    {
        Warehouse::create($this->validated($request));
        return redirect()->route('warehouses.index')->with('success', 'Warehouse berhasil dibuat.');
    }

    public function edit(Warehouse $warehouse)
    {
        return view('warehouses.form', compact('warehouse'));
    }

    public function update(Request $request, Warehouse $warehouse)
    {
        $warehouse->update($this->validated($request, $warehouse->id));
        return redirect()->route('warehouses.index')->with('success', 'Warehouse berhasil diperbarui.');
    }

    public function destroy(Warehouse $warehouse): RedirectResponse
    {
        if ($warehouse->locations()->exists() || $warehouse->stockBalances()->exists() || $warehouse->stockMoves()->exists() || $warehouse->stockOpnameSessions()->exists()) {
            return back()->withErrors('Warehouse sudah memiliki lokasi atau transaksi. Nonaktifkan warehouse jika tidak ingin digunakan lagi.');
        }

        $warehouse->delete();
        return back()->with('success', 'Warehouse berhasil dihapus.');
    }

    private function validated(Request $request, ?int $ignoreId = null): array
    {
        $data = $request->validate([
            'code' => ['required', 'string', 'max:100', 'unique:warehouses,code,' . $ignoreId],
            'name' => ['required', 'string', 'max:255'],
            'address' => ['nullable', 'string'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $data['is_active'] = $request->boolean('is_active');

        return $data;
    }
}
