<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\View\View;

class RoleController extends Controller
{
    public function index(): View
    {
        $roles = collect(User::roleOptions())->map(function (string $label, string $role): array {
            return [
                'key' => $role,
                'label' => $label,
                'description' => User::roleDescriptions()[$role] ?? '-',
                'total' => User::where('role', $role)->count(),
                'active' => User::where('role', $role)->where('is_active', true)->count(),
                'inactive' => User::where('role', $role)->where('is_active', false)->count(),
                'permissions' => [
                    'Dashboard' => true,
                    'Master Data' => in_array($role, [User::ROLE_ADMIN, User::ROLE_SUPERVISOR], true),
                    'Mutasi Stok' => in_array($role, [User::ROLE_ADMIN, User::ROLE_SUPERVISOR, User::ROLE_STAFF], true),
                    'Stock Opname' => in_array($role, [User::ROLE_ADMIN, User::ROLE_SUPERVISOR, User::ROLE_STAFF], true),
                    'Approval Opname' => in_array($role, [User::ROLE_ADMIN, User::ROLE_SUPERVISOR], true),
                    'User Management' => $role === User::ROLE_ADMIN,
                    'Audit Log' => $role === User::ROLE_ADMIN,
                ],
            ];
        });

        return view('roles.index', [
            'roles' => $roles,
        ]);
    }
}
