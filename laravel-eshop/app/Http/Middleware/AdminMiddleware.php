<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

/**
 * AdminMiddleware - overuje či je používateľ admin
 * Podporuje Laravel Auth s rolami aj legacy session auth
 */
class AdminMiddleware
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Najprv skontroluj Laravel Auth (nový systém)
        if (Auth::check()) {
            $user = Auth::user();
            if ($user->isAdmin()) {
                return $next($request);
            }

            // Používateľ je prihlásený, ale nie je admin
            if ($request->expectsJson()) {
                return response()->json([
                    'ok' => false,
                    'error' => 'Forbidden - Admin access required'
                ], 403);
            }

            return redirect()
                ->route('home')
                ->with('error', 'Prístup zamietnutý. Vyžaduje sa admin oprávnenie.');
        }

        // Fallback na legacy session auth (pre spätnú kompatibilitu)
        if (session('admin_logged_in')) {
            return $next($request);
        }

        // Neprihlásený
        if ($request->expectsJson()) {
            return response()->json([
                'ok' => false,
                'error' => 'Unauthorized'
            ], 401);
        }

        return redirect()
            ->route('admin.login')
            ->with('error', 'Prístup zamietnutý. Prihláste sa ako admin.');
    }
}
