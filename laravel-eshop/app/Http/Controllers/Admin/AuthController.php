<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Session;

/**
 * AuthController - autentifikácia admina
 */
class AuthController extends Controller
{
    /**
     * Zobrazí prihlasovací formulár
     * GET /admin/login
     */
    public function showLoginForm(): View
    {
        return view('admin.login');
    }

    /**
     * Spracuje prihlásenie
     * POST /admin/login
     */
    public function login(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'username' => 'required|string|min:1',
            'password' => 'required|string|min:1',
        ], [
            'username.required' => 'Meno je povinné',
            'password.required' => 'Heslo je povinné',
        ]);

        $adminPassword = env('ADMIN_PASS', 'admin123');

        if ($validated['password'] !== $adminPassword) {
            return back()
                ->withInput(['username' => $validated['username']])
                ->withErrors(['password' => 'Nesprávne prihlasovacie údaje']);
        }

        // Úspešné prihlásenie
        Session::put('admin_logged_in', true);
        Session::put('admin_username', $validated['username']);
        Session::put('admin_role', 'admin');

        return redirect()
            ->route('admin.products.index')
            ->with('success', 'Prihlásenie úspešné');
    }

    /**
     * Odhlásenie
     * POST /admin/logout
     */
    public function logout(): RedirectResponse
    {
        Session::forget(['admin_logged_in', 'admin_username', 'admin_role']);
        Session::flush();

        return redirect()
            ->route('admin.login')
            ->with('success', 'Boli ste odhlásení');
    }

    /**
     * Zobrazí stránku pre výber prihlásenia
     * GET /login
     */
    public function showLoginChoice(): View
    {
        return view('login-choice');
    }

    /**
     * API: Prihlásenie
     * POST /api/auth/login
     */
    public function apiLogin(Request $request)
    {
        $username = $request->input('username');
        $password = $request->input('password');

        if (empty($username) || empty($password)) {
            return response()->json([
                'ok' => false,
                'error' => 'Missing credentials'
            ], 400);
        }

        $adminPassword = env('ADMIN_PASS', 'admin123');

        if ($password !== $adminPassword) {
            return response()->json([
                'ok' => false,
                'error' => 'Invalid credentials'
            ], 401);
        }

        Session::put('admin_logged_in', true);
        Session::put('admin_username', $username);
        Session::put('admin_role', 'admin');

        return response()->json([
            'ok' => true,
            'user' => [
                'id' => 1,
                'username' => $username,
                'role' => 'admin'
            ]
        ]);
    }

    /**
     * API: Odhlásenie
     * POST /api/auth/logout
     */
    public function apiLogout()
    {
        Session::forget(['admin_logged_in', 'admin_username', 'admin_role']);
        return response()->json(['ok' => true]);
    }
}

