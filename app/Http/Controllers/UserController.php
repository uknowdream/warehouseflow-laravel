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

        $users = User::query()
            ->when($search, function ($query, string $search): void {
                $query->where(function ($query) use ($search): void {
                    $query->where('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%")
                        ->orWhere('role', 'like', "%{$search}%");
                });
            })
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('users.index', [
            'users' => $users,
            'search' => $search,
            'roleOptions' => User::roleOptions(),
        ]);
    }

    public function create(): View
    {
        return view('users.form', [
            'user' => new User(['role' => User::ROLE_STAFF]),
            'roleOptions' => User::roleOptions(),
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
        ]);
    }

    public function update(Request $request, User $user): RedirectResponse
    {
        $data = $this->validated($request, $user);

        if ($user->role === User::ROLE_ADMIN && ($data['role'] ?? null) !== User::ROLE_ADMIN) {
            $this->ensureAnotherAdminExists($user);
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

        if ($user->role === User::ROLE_ADMIN) {
            $this->ensureAnotherAdminExists($user);
        }

        $user->delete();

        return redirect()->route('users.index')->with('success', 'User berhasil dihapus.');
    }

    private function validated(Request $request, ?User $user = null): array
    {
        $passwordRules = $user?->exists
            ? ['nullable', 'confirmed', Password::defaults()]
            : ['required', 'confirmed', Password::defaults()];

        return $request->validate([
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
            'password' => $passwordRules,
        ]);
    }

    private function ensureAnotherAdminExists(User $user): void
    {
        $adminCount = User::where('role', User::ROLE_ADMIN)->whereKeyNot($user->id)->count();

        if ($adminCount === 0) {
            throw ValidationException::withMessages([
                'role' => 'Minimal harus ada satu user admin.',
            ]);
        }
    }
}
