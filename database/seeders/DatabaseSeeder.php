<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        foreach ([
            ['name' => 'Admin Warehouse', 'email' => 'test@example.com', 'role' => User::ROLE_ADMIN],
            ['name' => 'Supervisor Gudang', 'email' => 'supervisor@example.com', 'role' => User::ROLE_SUPERVISOR],
            ['name' => 'Operator Inbound', 'email' => 'operator@example.com', 'role' => User::ROLE_STAFF],
            ['name' => 'Viewer Audit', 'email' => 'viewer@example.com', 'role' => User::ROLE_VIEWER],
        ] as $user) {
            User::updateOrCreate(['email' => $user['email']], [
                'name' => $user['name'],
                'role' => $user['role'],
                'is_active' => true,
                'email_verified_at' => now(),
                'password' => Hash::make('password'),
            ]);
        }

        $this->call(WarehouseFlowSeeder::class);
    }
}
