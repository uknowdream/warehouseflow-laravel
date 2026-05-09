<?php

namespace App\Http\Controllers;

use App\Models\Warehouse;
use Illuminate\Http\Request;

class WarehouseController extends Controller
{
    public function index()
    {
        return view('warehouses.index', ['warehouses' => Warehouse::latest()->paginate(15)]);
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

    public function destroy(Warehouse $warehouse)
    {
        $warehouse->delete();
        return back()->with('success', 'Warehouse berhasil dihapus.');
    }

    private function validated(Request $request, ?int $ignoreId = null): array
    {
        return $request->validate([
            'code' => ['required', 'string', 'max:100', 'unique:warehouses,code,' . $ignoreId],
            'name' => ['required', 'string', 'max:255'],
            'address' => ['nullable', 'string'],
            'is_active' => ['nullable', 'boolean'],
        ]);
    }
}
