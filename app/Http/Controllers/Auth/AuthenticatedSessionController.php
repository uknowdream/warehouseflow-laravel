<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Models\AuditLog;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): View
    {
        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();

        $request->session()->regenerate();

        $request->user()->forceFill([
            'last_login_at' => now(),
        ])->save();

        AuditLog::create([
            'user_id' => $request->user()->id,
            'event' => 'logged_in',
            'auditable_type' => $request->user()::class,
            'auditable_id' => $request->user()->id,
            'label' => $request->user()->email,
            'ip_address' => $request->ip(),
            'user_agent' => str($request->userAgent())->limit(500, '')->toString(),
        ]);

        return redirect()->intended(route('dashboard', absolute: false));
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        if ($request->user()) {
            AuditLog::create([
                'user_id' => $request->user()->id,
                'event' => 'logged_out',
                'auditable_type' => $request->user()::class,
                'auditable_id' => $request->user()->id,
                'label' => $request->user()->email,
                'ip_address' => $request->ip(),
                'user_agent' => str($request->userAgent())->limit(500, '')->toString(),
            ]);
        }

        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }
}
