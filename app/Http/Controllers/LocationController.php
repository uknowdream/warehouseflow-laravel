<?php

namespace App\Http\Controllers;

use App\Models\Location;
use App\Models\Warehouse;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class LocationController extends Controller
{
    public function index(Request $request): View
    {
        $search = $request->query('search');
        $warehouseId = $request->query('warehouse_id');
        $status = $request->query('status');

        return view('locations.index', [
            'locations' => Location::query()
                ->with('warehouse')
                ->withSum('stockBalances as total_stock', 'qty')
                ->when($search, function ($query, string $search): void {
                    $query->where(function ($query) use ($search): void {
                        $query->where('code', 'like', "%{$search}%")
                            ->orWhere('name', 'like', "%{$search}%")
                            ->orWhere('area', 'like', "%{$search}%")
                            ->orWhere('rack', 'like', "%{$search}%");
                    });
                })
                ->when($warehouseId, fn ($query, string $warehouseId) => $query->where('warehouse_id', $warehouseId))
                ->when($status === 'active', fn ($query) => $query->where('is_active', true))
                ->when($status === 'inactive', fn ($query) => $query->where('is_active', false))
                ->latest()
                ->paginate(15)
                ->withQueryString(),
            'warehouses' => Warehouse::orderBy('name')->get(),
            'search' => $search,
            'selectedWarehouse' => $warehouseId,
            'selectedStatus' => $status,
            'summary' => [
                'total' => Location::count(),
                'active' => Location::where('is_active', true)->count(),
                'inactive' => Location::where('is_active', false)->count(),
            ],
        ]);
    }

    public function create()
    {
        return view('locations.form', [
            'location' => new Location(),
            'warehouses' => Warehouse::where('is_active', true)->orderBy('name')->get(),
        ]);
    }

    public function store(Request $request)
    {
        $data = $this->validated($request);
        $data['qr_code'] = 'LOCATION:' . $data['code'];

        Location::create($data);
        return redirect()->route('locations.index')->with('success', 'Lokasi berhasil dibuat.');
    }

    public function edit(Location $location)
    {
        return view('locations.form', [
            'location' => $location,
            'warehouses' => Warehouse::orderBy('name')->get(),
        ]);
    }

    public function update(Request $request, Location $location)
    {
        $data = $this->validated($request, $location->id);
        $data['qr_code'] = 'LOCATION:' . $data['code'];

        $location->update($data);
        return redirect()->route('locations.index')->with('success', 'Lokasi berhasil diperbarui.');
    }

    public function destroy(Location $location): RedirectResponse
    {
        if ($location->stockBalances()->where('qty', '>', 0)->exists() || $location->stockMoves()->exists() || $location->stockOpnameLines()->exists()) {
            return back()->withErrors('Lokasi sudah dipakai transaksi. Nonaktifkan lokasi jika tidak ingin digunakan lagi.');
        }

        $location->delete();
        return back()->with('success', 'Lokasi berhasil dihapus.');
    }

    public function qr(Location $location)
    {
        return view('locations.qr', compact('location'));
    }

    private function validated(Request $request, ?int $ignoreId = null): array
    {
        $data = $request->validate([
            'warehouse_id' => ['required', 'exists:warehouses,id'],
            'code' => ['required', 'string', 'max:100', 'unique:locations,code,' . $ignoreId],
            'name' => ['required', 'string', 'max:255'],
            'area' => ['nullable', 'string', 'max:100'],
            'rack' => ['nullable', 'string', 'max:100'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $data['is_active'] = $request->boolean('is_active');

        return $data;
    }
}
