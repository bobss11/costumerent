<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Illuminate\View\View;

class LoginController extends Controller
{
    /**
     * Display the login form.
     */
    public function showLogin(): View|RedirectResponse
    {
        if (Auth::check()) {
            return $this->redirectForRole();
        }

        return view('auth.login');
    }

    /**
     * Handle a login attempt.
     */
    public function login(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        $remember = $request->boolean('remember');

        if (! Auth::attempt($credentials, $remember)) {
            return back()
                ->withErrors(['email' => 'Email atau kata sandi tidak cocok.'])
                ->onlyInput('email');
        }

        $request->session()->regenerate();

        return $this->redirectForRole();
    }

    /**
     * Log the user out and invalidate the session.
     */
    public function logout(Request $request): RedirectResponse
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }

    /**
     * Resolve the post-login landing page based on the user's role.
     */
    protected function redirectForRole(): RedirectResponse
    {
        $user = Auth::user();

        if ($user && ($user->isAdmin() || $user->isStaff())) {
            // Admin dashboard is delivered by a later feature; fall back to home for now.
            return Route::has('admin.dashboard')
                ? redirect()->route('admin.dashboard')
                : redirect('/');
        }

        // Customer catalog is delivered by a later feature; fall back to home for now.
        return Route::has('customer.catalog')
            ? redirect()->route('customer.catalog')
            : redirect('/');
    }
}
