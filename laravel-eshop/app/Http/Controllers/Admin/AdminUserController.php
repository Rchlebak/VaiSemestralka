<?php

namespace App\Http\Controllers\Admin;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

/**
 * AdminUserController - správa používateľov v admin paneli
 */
class AdminUserController extends Controller
{
    /**
     * Zobrazí zoznam používateľov
     */
    public function index(): View
    {
        $users = User::orderBy('id', 'desc')->paginate(20);

        return view('admin.users.index', compact('users'));
    }

    /**
     * Zmení heslo používateľa
     */
    public function updatePassword(Request $request, $id): RedirectResponse
    {
        $user = User::findOrFail($id);

        $validated = $request->validate([
            'password' => ['required', 'string', 'min:6', 'confirmed'],
        ], [
            'password.required' => 'Heslo je povinné',
            'password.min' => 'Heslo musí mať aspoň 6 znakov',
            'password.confirmed' => 'Heslá sa nezhodujú',
        ]);

        $user->update([
            'password' => Hash::make($validated['password'])
        ]);

        return redirect()
            ->route('admin.users.index')
            ->with('success', "Heslo používateľa {$user->name} bolo zmenené");
    }

    /**
     * Odstráni používateľa
     */
    public function destroy($id): RedirectResponse
    {
        $user = User::findOrFail($id);

        // Nedovoliť zmazať admin účet
        if ($user->isAdmin()) {
            return redirect()
                ->route('admin.users.index')
                ->with('error', 'Administrátorský účet nie je možné odstrániť');
        }

        $userName = $user->name;
        $user->delete();

        return redirect()
            ->route('admin.users.index')
            ->with('success', "Používateľ {$userName} bol odstránený");
    }
}
