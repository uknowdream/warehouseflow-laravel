<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Location;
use App\Models\Product;
use App\Models\Unit;
use App\Models\Warehouse;
use Illuminate\Database\Seeder;

class WarehouseFlowSeeder extends Seeder
{
    public function run(): void
    {
        $unitKg = Unit::updateOrCreate(['symbol' => 'kg'], ['name' => 'Kg']);
        $unitBox = Unit::updateOrCreate(['symbol' => 'box'], ['name' => 'Box']);

        $finishGood = Category::updateOrCreate(['name' => 'Finish Good'], ['description' => 'Barang jadi']);
        $rawMaterial = Category::updateOrCreate(['name' => 'Raw Material'], ['description' => 'Bahan baku']);
        Category::updateOrCreate(['name' => 'Import'], ['description' => 'Barang impor']);

        $legacyPcsUnits = Unit::where('symbol', 'pcs')
            ->orWhere('name', 'Pieces')
            ->get();

        foreach ($legacyPcsUnits as $legacyPcsUnit) {
            Product::where('unit_id', $legacyPcsUnit->id)->update(['unit_id' => $unitKg->id]);
            $legacyPcsUnit->delete();
        }

        $legacyGeneral = Category::where('name', 'General')->first();

        if ($legacyGeneral) {
            Product::where('category_id', $legacyGeneral->id)->update(['category_id' => $finishGood->id]);
            $legacyGeneral->delete();
        }

        $warehouse = Warehouse::firstOrCreate(['code' => 'WH-UTAMA'], [
            'name' => 'Gudang Utama',
            'address' => 'Jakarta',
            'is_active' => true,
        ]);

        foreach (['RAK-A1', 'RAK-A2', 'RAK-B1'] as $rack) {
            Location::firstOrCreate(['code' => $rack], [
                'warehouse_id' => $warehouse->id,
                'name' => $rack,
                'area' => str_contains($rack, 'A') ? 'Area A' : 'Area B',
                'rack' => $rack,
                'qr_code' => 'LOCATION:' . $rack,
                'is_active' => true,
            ]);
        }

        Product::firstOrCreate(['code' => 'PRD-0001'], [
            'category_id' => $finishGood->id,
            'unit_id' => $unitKg->id,
            'name' => 'Contoh Produk 1',
            'qr_code' => 'PRODUCT:PRD-0001',
            'is_active' => true,
        ]);

        Product::firstOrCreate(['code' => 'PRD-0002'], [
            'category_id' => $rawMaterial->id,
            'unit_id' => $unitBox->id,
            'name' => 'Contoh Produk 2',
            'qr_code' => 'PRODUCT:PRD-0002',
            'is_active' => true,
        ]);
    }
}
