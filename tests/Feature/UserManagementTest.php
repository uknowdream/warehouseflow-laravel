<?php

namespace Tests\Feature;

use App\Models\StockOpnameSession;
use App\Models\User;
use App\Models\Warehouse;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_inactive_users_can_not_authenticate(): void
    {
        $user = User::factory()->create([
            'is_active' => false,
        ]);

        $this->post('/login', [
            'email' => $user->email,
            'password' => 'password',
        ])->assertSessionHasErrors('email');

        $this->assertGuest();
    }

    public function test_admin_can_create_user_with_role_and_status(): void
    {
        $admin = User::factory()->create([
            'role' => User::ROLE_ADMIN,
        ]);

        $this->actingAs($admin)
            ->post(route('users.store'), [
                'name' => 'Viewer User',
                'email' => 'viewer@example.com',
                'role' => User::ROLE_VIEWER,
                'is_active' => '1',
                'password' => 'password',
                'password_confirmation' => 'password',
            ])
            ->assertRedirect(route('users.index'));

        $this->assertDatabaseHas('users', [
            'email' => 'viewer@example.com',
            'role' => User::ROLE_VIEWER,
            'is_active' => true,
        ]);
    }

    public function test_non_admin_can_not_open_user_management(): void
    {
        $operator = User::factory()->create([
            'role' => User::ROLE_STAFF,
        ]);

        $this->actingAs($operator)
            ->get(route('users.index'))
            ->assertForbidden();
    }

    public function test_viewer_can_not_open_write_routes(): void
    {
        $viewer = User::factory()->create([
            'role' => User::ROLE_VIEWER,
        ]);

        $this->actingAs($viewer)
            ->get(route('products.create'))
            ->assertForbidden();

        $this->actingAs($viewer)
            ->get(route('stock-moves.in.create'))
            ->assertForbidden();
    }

    public function test_supervisor_can_approve_stock_opname(): void
    {
        $supervisor = User::factory()->create([
            'role' => User::ROLE_SUPERVISOR,
        ]);
        $warehouse = Warehouse::create([
            'code' => 'WH-TST',
            'name' => 'Warehouse Test',
            'is_active' => true,
        ]);
        $session = StockOpnameSession::create([
            'opname_no' => 'OPN-TEST',
            'warehouse_id' => $warehouse->id,
            'status' => 'waiting_approval',
            'created_by' => $supervisor->id,
        ]);

        $this->actingAs($supervisor)
            ->post(route('stock-opnames.approve', $session))
            ->assertRedirect(route('stock-opnames.show', $session));

        $this->assertDatabaseHas('stock_opname_sessions', [
            'id' => $session->id,
            'status' => 'done',
            'approved_by' => $supervisor->id,
        ]);
    }
}
