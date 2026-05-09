<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Location;
use App\Models\Product;
use App\Models\StockBalance;
use App\Models\StockMove;
use App\Models\StockOpnameLine;
use App\Models\StockOpnameSession;
use App\Models\Unit;
use App\Models\User;
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
        $import = Category::updateOrCreate(['name' => 'Import'], ['description' => 'Barang impor']);

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

        $warehouses = collect([
            ['code' => 'WH-UTAMA', 'name' => 'Gudang Utama', 'address' => 'Jakarta Timur'],
            ['code' => 'WH-BARAT', 'name' => 'Gudang Barat', 'address' => 'Tangerang'],
            ['code' => 'WH-COLD', 'name' => 'Cold Storage', 'address' => 'Bekasi'],
        ])->mapWithKeys(fn (array $row) => [
            $row['code'] => Warehouse::updateOrCreate(['code' => $row['code']], [
                'name' => $row['name'],
                'address' => $row['address'],
                'is_active' => true,
            ]),
        ]);

        $locations = collect([
            ['warehouse' => 'WH-UTAMA', 'code' => 'A-01-01', 'name' => 'Rak A01 Bin 01', 'area' => 'Area A', 'rack' => 'A01'],
            ['warehouse' => 'WH-UTAMA', 'code' => 'A-01-02', 'name' => 'Rak A01 Bin 02', 'area' => 'Area A', 'rack' => 'A01'],
            ['warehouse' => 'WH-UTAMA', 'code' => 'B-02-01', 'name' => 'Rak B02 Bin 01', 'area' => 'Area B', 'rack' => 'B02'],
            ['warehouse' => 'WH-BARAT', 'code' => 'W-01-01', 'name' => 'Rak Barat 01', 'area' => 'Receiving', 'rack' => 'W01'],
            ['warehouse' => 'WH-BARAT', 'code' => 'W-02-01', 'name' => 'Rak Barat 02', 'area' => 'Dispatch', 'rack' => 'W02'],
            ['warehouse' => 'WH-COLD', 'code' => 'C-01-01', 'name' => 'Cold Rack 01', 'area' => 'Chiller', 'rack' => 'C01'],
        ])->mapWithKeys(fn (array $row) => [
            $row['code'] => Location::updateOrCreate(['code' => $row['code']], [
                'warehouse_id' => $warehouses[$row['warehouse']]->id,
                'name' => $row['name'],
                'area' => $row['area'],
                'rack' => $row['rack'],
                'qr_code' => 'LOCATION:' . $row['code'],
                'is_active' => true,
            ]),
        ]);

        $products = collect([
            ['code' => 'FG-RICE-25', 'name' => 'Beras Premium 25kg', 'category' => $finishGood->id, 'unit' => $unitKg->id],
            ['code' => 'FG-SUGAR-50', 'name' => 'Gula Kristal 50kg', 'category' => $finishGood->id, 'unit' => $unitKg->id],
            ['code' => 'RM-FLOUR-25', 'name' => 'Tepung Terigu 25kg', 'category' => $rawMaterial->id, 'unit' => $unitKg->id],
            ['code' => 'IMP-SPICE-BOX', 'name' => 'Import Spice Box', 'category' => $import->id, 'unit' => $unitBox->id],
            ['code' => 'RM-PACK-BOX', 'name' => 'Packaging Carton Box', 'category' => $rawMaterial->id, 'unit' => $unitBox->id],
        ])->mapWithKeys(fn (array $row) => [
            $row['code'] => Product::updateOrCreate(['code' => $row['code']], [
                'category_id' => $row['category'],
                'unit_id' => $row['unit'],
                'name' => $row['name'],
                'qr_code' => 'PRODUCT:' . $row['code'],
                'is_active' => true,
            ]),
        ]);

        $stockRows = [
            ['product' => 'FG-RICE-25', 'warehouse' => 'WH-UTAMA', 'location' => 'A-01-01', 'qty' => 420],
            ['product' => 'FG-SUGAR-50', 'warehouse' => 'WH-UTAMA', 'location' => 'A-01-02', 'qty' => 180],
            ['product' => 'RM-FLOUR-25', 'warehouse' => 'WH-UTAMA', 'location' => 'B-02-01', 'qty' => 310],
            ['product' => 'IMP-SPICE-BOX', 'warehouse' => 'WH-BARAT', 'location' => 'W-01-01', 'qty' => 65],
            ['product' => 'RM-PACK-BOX', 'warehouse' => 'WH-BARAT', 'location' => 'W-02-01', 'qty' => 240],
            ['product' => 'FG-RICE-25', 'warehouse' => 'WH-COLD', 'location' => 'C-01-01', 'qty' => 75],
        ];

        foreach ($stockRows as $row) {
            StockBalance::updateOrCreate([
                'product_id' => $products[$row['product']]->id,
                'warehouse_id' => $warehouses[$row['warehouse']]->id,
                'location_id' => $locations[$row['location']]->id,
            ], [
                'qty' => $row['qty'],
            ]);
        }

        $adminId = User::where('role', User::ROLE_ADMIN)->value('id');

        foreach ([
            ['no' => 'IN-DEMO-001', 'type' => 'IN', 'product' => 'FG-RICE-25', 'warehouse' => 'WH-UTAMA', 'location' => 'A-01-01', 'in' => 500, 'out' => 0, 'note' => 'Stok awal demo'],
            ['no' => 'OUT-DEMO-001', 'type' => 'OUT', 'product' => 'FG-RICE-25', 'warehouse' => 'WH-UTAMA', 'location' => 'A-01-01', 'in' => 0, 'out' => 80, 'note' => 'Pengiriman demo'],
            ['no' => 'IN-DEMO-002', 'type' => 'IN', 'product' => 'IMP-SPICE-BOX', 'warehouse' => 'WH-BARAT', 'location' => 'W-01-01', 'in' => 65, 'out' => 0, 'note' => 'Barang import masuk'],
            ['no' => 'ADJ-DEMO-001', 'type' => 'ADJUSTMENT', 'product' => 'RM-PACK-BOX', 'warehouse' => 'WH-BARAT', 'location' => 'W-02-01', 'in' => 0, 'out' => 10, 'note' => 'Adjustment demo'],
        ] as $row) {
            StockMove::updateOrCreate(['transaction_no' => $row['no']], [
                'transaction_type' => $row['type'],
                'product_id' => $products[$row['product']]->id,
                'warehouse_id' => $warehouses[$row['warehouse']]->id,
                'location_id' => $locations[$row['location']]->id,
                'qty_in' => $row['in'],
                'qty_out' => $row['out'],
                'note' => $row['note'],
                'created_by' => $adminId,
            ]);
        }

        $session = StockOpnameSession::updateOrCreate(['opname_no' => 'OPN-DEMO-001'], [
            'warehouse_id' => $warehouses['WH-UTAMA']->id,
            'status' => 'waiting_approval',
            'started_at' => now()->subDay(),
            'created_by' => $adminId,
            'note' => 'Sesi dummy untuk testing approval.',
        ]);

        foreach ([
            ['product' => 'FG-RICE-25', 'location' => 'A-01-01', 'system' => 420, 'physical' => 418],
            ['product' => 'FG-SUGAR-50', 'location' => 'A-01-02', 'system' => 180, 'physical' => 180],
        ] as $row) {
            StockOpnameLine::updateOrCreate([
                'opname_session_id' => $session->id,
                'product_id' => $products[$row['product']]->id,
                'location_id' => $locations[$row['location']]->id,
            ], [
                'system_qty' => $row['system'],
                'physical_qty' => $row['physical'],
                'difference_qty' => $row['physical'] - $row['system'],
                'status' => 'counted',
                'counted_by' => $adminId,
                'counted_at' => now()->subHours(3),
                'note' => 'Dummy count',
            ]);
        }
    }
}
