<?php

namespace App\Http\Controllers;

use App\Models\Location;
use App\Models\Warehouse;
use Illuminate\Http\Request;

class LocationController extends Controller
{
    public function index()
    {
        return view('locations.index', [
            'locations' => Location::with('warehouse')->latest()->paginate(15)
        ]);
    }

    public function create()
    {
        return view('locations.form', [
            'location' => new Location(),
            'warehouses' => Warehouse::orderBy('name')->get(),
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

    public function destroy(Location $location)
    {
        $location->delete();
        return back()->with('success', 'Lokasi berhasil dihapus.');
    }

    public function qr(Location $location)
    {
        return view('locations.qr', compact('location'));
    }

    private function validated(Request $request, ?int $ignoreId = null): array
    {
        return $request->validate([
            'warehouse_id' => ['required', 'exists:warehouses,id'],
            'code' => ['required', 'string', 'max:100', 'unique:locations,code,' . $ignoreId],
            'name' => ['required', 'string', 'max:255'],
            'area' => ['nullable', 'string', 'max:100'],
            'rack' => ['nullable', 'string', 'max:100'],
            'is_active' => ['nullable', 'boolean'],
        ]);
    }
}
