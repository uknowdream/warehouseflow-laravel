<?php

namespace Tests\Feature;

use App\Models\AuditLog;
use App\Models\Category;
use App\Models\Location;
use App\Models\Product;
use App\Models\StockBalance;
use App\Models\Unit;
use App\Models\User;
use App\Models\Warehouse;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class WarehouseCrudFlowTest extends TestCase
{
    use RefreshDatabase;

    public function test_product_crud_records_audit_logs(): void
    {
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);
        $category = Category::create(['name' => 'Finish Good']);
        $unit = Unit::create(['name' => 'Kg', 'symbol' => 'kg']);

        $this->actingAs($admin)
            ->post(route('products.store'), [
                'category_id' => $category->id,
                'unit_id' => $unit->id,
                'code' => 'TEST-001',
                'name' => 'Produk Test',
                'is_active' => '1',
            ])
            ->assertRedirect(route('products.index'));

        $product = Product::where('code', 'TEST-001')->firstOrFail();

        $this->assertDatabaseHas('audit_logs', [
            'event' => 'created',
            'auditable_type' => Product::class,
            'auditable_id' => $product->id,
            'user_id' => $admin->id,
        ]);

        $this->actingAs($admin)
            ->put(route('products.update', $product), [
                'category_id' => $category->id,
                'unit_id' => $unit->id,
                'code' => 'TEST-001',
                'name' => 'Produk Test Update',
            ])
            ->assertRedirect(route('products.index'));

        $this->assertDatabaseHas('products', [
            'id' => $product->id,
            'name' => 'Produk Test Update',
            'is_active' => false,
        ]);
        $this->assertDatabaseHas('audit_logs', [
            'event' => 'updated',
            'auditable_type' => Product::class,
            'auditable_id' => $product->id,
        ]);
    }

    public function test_product_with_stock_can_not_be_deleted(): void
    {
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);
        $warehouse = Warehouse::create(['code' => 'WH-TST', 'name' => 'Warehouse Test', 'is_active' => true]);
        $location = Location::create([
            'warehouse_id' => $warehouse->id,
            'code' => 'LOC-TST',
            'name' => 'Lokasi Test',
            'qr_code' => 'LOCATION:LOC-TST',
            'is_active' => true,
        ]);
        $product = Product::create([
            'code' => 'PRD-TST',
            'name' => 'Produk Test',
            'qr_code' => 'PRODUCT:PRD-TST',
            'is_active' => true,
        ]);
        StockBalance::create([
            'product_id' => $product->id,
            'warehouse_id' => $warehouse->id,
            'location_id' => $location->id,
            'qty' => 10,
        ]);

        $this->actingAs($admin)
            ->delete(route('products.destroy', $product))
            ->assertSessionHasErrors();

        $this->assertDatabaseHas('products', ['id' => $product->id]);
    }

    public function test_admin_can_view_audit_logs(): void
    {
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);
        AuditLog::create([
            'user_id' => $admin->id,
            'event' => 'created',
            'auditable_type' => Warehouse::class,
            'auditable_id' => 10,
            'label' => 'WH-DEMO',
        ]);

        $this->actingAs($admin)
            ->get(route('audit-logs.index'))
            ->assertOk()
            ->assertSee('Audit Log')
            ->assertSee('WH-DEMO');
    }
}
