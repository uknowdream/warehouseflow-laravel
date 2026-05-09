<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Illuminate\Validation\Rules\Password;
use Illuminate\View\View;

class UserController extends Controller
{
    public function index(Request $request): View
    {
        $search = $request->query('search');
        $role = $request->query('role');
        $status = $request->query('status');

        $users = User::query()
            ->when($search, function ($query, string $search): void {
                $query->where(function ($query) use ($search): void {
                    $query->where('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%")
                        ->orWhere('role', 'like', "%{$search}%");
                });
            })
            ->when($role, fn ($query, string $role) => $query->where('role', $role))
            ->when($status === 'active', fn ($query) => $query->where('is_active', true))
            ->when($status === 'inactive', fn ($query) => $query->where('is_active', false))
            ->latest()
            ->paginate(15)
            ->withQueryString();

        $summary = [
            'total' => User::count(),
            'active' => User::where('is_active', true)->count(),
            'inactive' => User::where('is_active', false)->count(),
            'admins' => User::where('role', User::ROLE_ADMIN)->count(),
        ];

        return view('users.index', [
            'users' => $users,
            'search' => $search,
            'selectedRole' => $role,
            'selectedStatus' => $status,
            'summary' => $summary,
            'roleOptions' => User::roleOptions(),
        ]);
    }

    public function create(): View
    {
        return view('users.form', [
            'user' => new User(['role' => User::ROLE_STAFF, 'is_active' => true]),
            'roleOptions' => User::roleOptions(),
            'roleDescriptions' => User::roleDescriptions(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validated($request);
        $data['password'] = Hash::make($data['password']);

        User::create($data);

        return redirect()->route('users.index')->with('success', 'User berhasil dibuat.');
    }

    public function edit(User $user): View
    {
        return view('users.form', [
            'user' => $user,
            'roleOptions' => User::roleOptions(),
            'roleDescriptions' => User::roleDescriptions(),
        ]);
    }

    public function update(Request $request, User $user): RedirectResponse
    {
        $data = $this->validated($request, $user);
        $nextRole = $data['role'] ?? $user->role;
        $nextActive = $data['is_active'] ?? $user->is_active;

        if ($request->user()->is($user) && ($nextRole !== User::ROLE_ADMIN || ! $nextActive)) {
            throw ValidationException::withMessages([
                'role' => 'Admin yang sedang login tidak bisa menurunkan role atau menonaktifkan akunnya sendiri.',
            ]);
        }

        if ($user->isAdmin() && ($nextRole !== User::ROLE_ADMIN || ! $nextActive)) {
            $this->ensureAnotherActiveAdminExists($user);
        }

        if (blank($data['password'] ?? null)) {
            unset($data['password']);
        } else {
            $data['password'] = Hash::make($data['password']);
        }

        $user->update($data);

        return redirect()->route('users.index')->with('success', 'User berhasil diperbarui.');
    }

    public function destroy(Request $request, User $user): RedirectResponse
    {
        if ($request->user()->is($user)) {
            return back()->withErrors('User yang sedang login tidak bisa dihapus.');
        }

        if ($user->isAdmin()) {
            $this->ensureAnotherActiveAdminExists($user);
        }

        $user->delete();

        return redirect()->route('users.index')->with('success', 'User berhasil dihapus.');
    }

    private function validated(Request $request, ?User $user = null): array
    {
        $passwordRules = $user?->exists
            ? ['nullable', 'confirmed', Password::defaults()]
            : ['required', 'confirmed', Password::defaults()];

        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => [
                'required',
                'string',
                'lowercase',
                'email',
                'max:255',
                Rule::unique('users', 'email')->ignore($user),
            ],
            'role' => ['required', Rule::in(array_keys(User::roleOptions()))],
            'is_active' => ['nullable', 'boolean'],
            'password' => $passwordRules,
        ]);

        $data['is_active'] = $request->boolean('is_active');

        return $data;
    }

    private function ensureAnotherActiveAdminExists(User $user): void
    {
        $adminCount = User::where('role', User::ROLE_ADMIN)
            ->where('is_active', true)
            ->whereKeyNot($user->id)
            ->count();

        if ($adminCount === 0) {
            throw ValidationException::withMessages([
                'role' => 'Minimal harus ada satu admin aktif.',
            ]);
        }
    }
}
