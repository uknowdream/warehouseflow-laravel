<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        $now = now();

        foreach ([
            'Finish Good' => 'Barang jadi',
            'Raw Material' => 'Bahan baku',
            'Import' => 'Barang impor',
        ] as $name => $description) {
            DB::table('categories')->updateOrInsert(
                ['name' => $name],
                ['description' => $description, 'updated_at' => $now, 'created_at' => $now]
            );
        }

        DB::table('units')->updateOrInsert(
            ['symbol' => 'kg'],
            ['name' => 'Kg', 'updated_at' => $now, 'created_at' => $now]
        );

        DB::table('units')->updateOrInsert(
            ['symbol' => 'box'],
            ['name' => 'Box', 'updated_at' => $now, 'created_at' => $now]
        );

        $finishGoodId = DB::table('categories')->where('name', 'Finish Good')->value('id');
        $kgId = DB::table('units')->where('symbol', 'kg')->value('id');

        if ($finishGoodId) {
            $generalIds = DB::table('categories')->where('name', 'General')->pluck('id');
            DB::table('products')->whereIn('category_id', $generalIds)->update(['category_id' => $finishGoodId]);
            DB::table('categories')->whereIn('id', $generalIds)->delete();
        }

        if ($kgId) {
            $piecesIds = DB::table('units')
                ->where('symbol', 'pcs')
                ->orWhere('name', 'Pieces')
                ->pluck('id');

            DB::table('products')->whereIn('unit_id', $piecesIds)->update(['unit_id' => $kgId]);
            DB::table('units')->whereIn('id', $piecesIds)->delete();
        }

        Schema::table('products', function (Blueprint $table) {
            foreach (['minimum_stock', 'purchase_price', 'selling_price'] as $column) {
                if (Schema::hasColumn('products', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            if (! Schema::hasColumn('products', 'minimum_stock')) {
                $table->decimal('minimum_stock', 15, 2)->default(0);
            }

            if (! Schema::hasColumn('products', 'purchase_price')) {
                $table->decimal('purchase_price', 15, 2)->default(0);
            }

            if (! Schema::hasColumn('products', 'selling_price')) {
                $table->decimal('selling_price', 15, 2)->default(0);
            }
        });
    }
};
