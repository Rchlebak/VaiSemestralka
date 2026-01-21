<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Validation\Rules\Password;

/**
 * UserAuthController - autentifikácia zákazníkov
 * Registrácia, prihlásenie, odhlásenie
 */
class UserAuthController extends Controller
{
    /**
     * Zobrazí registračný formulár
     * GET /register
     */
    public function showRegisterForm(): View
    {
        return view('auth.register');
    }

    /**
     * Spracuje registráciu
     * POST /register
     */
    public function register(Request $request): RedirectResponse
    {
        // Validácia na strane servera
        $validated = $request->validate([
            'name' => ['required', 'string', 'min:2', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'phone' => ['nullable', 'string', 'max:20', 'regex:/^[+]?[0-9\s\-]+$/'],
            'address' => ['nullable', 'string', 'max:255'],
            'city' => ['nullable', 'string', 'max:100'],
            'zip' => ['nullable', 'string', 'max:10'],
        ], [
            'name.required' => 'Meno je povinné',
            'name.min' => 'Meno musí mať aspoň 2 znaky',
            'email.required' => 'Email je povinný',
            'email.email' => 'Zadajte platný email',
            'email.unique' => 'Tento email je už registrovaný',
            'password.required' => 'Heslo je povinné',
            'password.min' => 'Heslo musí mať aspoň 8 znakov',
            'password.confirmed' => 'Heslá sa nezhodujú',
            'phone.regex' => 'Neplatný formát telefónneho čísla',
        ]);

        // Vytvorenie používateľa s hashovaným heslom
        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role' => User::ROLE_CUSTOMER,
            'phone' => $validated['phone'] ?? null,
            'address' => $validated['address'] ?? null,
            'city' => $validated['city'] ?? null,
            'zip' => $validated['zip'] ?? null,
        ]);

        // Automatické prihlásenie po registrácii
        Auth::login($user);

        return redirect()
            ->route('home')
            ->with('success', 'Registrácia úspešná! Vitajte, ' . $user->name);
    }

    /**
     * Zobrazí prihlasovací formulár
     * GET /login
     */
    public function showLoginForm(): View
    {
        return view('auth.login');
    }

    /**
     * Spracuje prihlásenie
     * POST /login
     */
    public function login(Request $request): RedirectResponse
    {
        // Validácia na strane servera
        $validated = $request->validate([
            'email' => ['required', 'string', 'email'],
            'password' => ['required', 'string'],
        ], [
            'email.required' => 'Email je povinný',
            'email.email' => 'Zadajte platný email',
            'password.required' => 'Heslo je povinné',
        ]);

        // Pokus o prihlásenie
        $remember = $request->boolean('remember');

        if (Auth::attempt($validated, $remember)) {
            $request->session()->regenerate();

            $user = Auth::user();

            // Presmerovanie podľa role
            if ($user->isAdmin()) {
                return redirect()
                    ->intended(route('admin.dashboard'))
                    ->with('success', 'Vitajte späť, ' . $user->name);
            }

            return redirect()
                ->intended(route('home'))
                ->with('success', 'Vitajte späť, ' . $user->name);
        }

        return back()
            ->withInput(['email' => $validated['email']])
            ->withErrors(['email' => 'Nesprávne prihlasovacie údaje']);
    }

    /**
     * Odhlásenie
     * POST /logout
     */
    public function logout(Request $request): RedirectResponse
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()
            ->route('home')
            ->with('success', 'Boli ste odhlásení');
    }

    /**
     * Zobrazí profil používateľa
     * GET /profile
     */
    public function profile(): View
    {
        $user = Auth::user();

        // Načítanie objednávok používateľa s položkami a produktmi
        $orders = \App\Models\Order::where('user_id', $user->user_id)
            ->with(['items.variant.product.images'])
            ->orderBy('order_id', 'desc')
            ->get();

        return view('auth.profile', [
            'user' => $user,
            'orders' => $orders
        ]);
    }

    /**
     * Aktualizuje profil
     * PUT /profile
     */
    public function updateProfile(Request $request): RedirectResponse
    {
        $user = Auth::user();

        $validated = $request->validate([
            'name' => ['required', 'string', 'min:2', 'max:255'],
            'phone' => ['nullable', 'string', 'max:20', 'regex:/^[+]?[0-9\s\-]+$/'],
            'address' => ['nullable', 'string', 'max:255'],
            'city' => ['nullable', 'string', 'max:100'],
            'zip' => ['nullable', 'string', 'max:10'],
        ]);

        $user->update($validated);

        return back()->with('success', 'Profil bol aktualizovaný');
    }

    /**
     * Zmena hesla
     * PUT /profile/password
     */
    public function updatePassword(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'current_password' => ['required', 'current_password'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ], [
            'current_password.current_password' => 'Aktuálne heslo nie je správne',
            'password.min' => 'Nové heslo musí mať aspoň 8 znakov',
            'password.confirmed' => 'Heslá sa nezhodujú',
        ]);

        Auth::user()->update([
            'password' => Hash::make($validated['password'])
        ]);

        return back()->with('success', 'Heslo bolo zmenené');
    }
}
