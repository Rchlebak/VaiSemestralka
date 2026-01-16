<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

/**
 * RoleMiddleware - kontrola rolí používateľov
 * Použitie: middleware('role:admin') alebo middleware('role:customer')
 */
class RoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     * @param  string  ...$roles  Povolené roly
     */
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        // Kontrola prihlásenia
        if (!Auth::check()) {
            if ($request->expectsJson()) {
                return response()->json([
                    'error' => 'Neautorizovaný prístup',
                    'message' => 'Pre prístup sa musíte prihlásiť'
                ], 401);
            }

            return redirect()
                ->route('login')
                ->with('error', 'Pre prístup sa musíte prihlásiť');
        }

        $user = Auth::user();

        // Kontrola role
        if (!in_array($user->role, $roles)) {
            if ($request->expectsJson()) {
                return response()->json([
                    'error' => 'Zakázaný prístup',
                    'message' => 'Nemáte oprávnenie na túto akciu'
                ], 403);
            }

            // Ak používateľ nie je admin, presmeruj na hlavnú stránku
            return redirect()
                ->route('home')
                ->with('error', 'Nemáte oprávnenie na prístup k tejto sekcii');
        }

        return $next($request);
    }
}
