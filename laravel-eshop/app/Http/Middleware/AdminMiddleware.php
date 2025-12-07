<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * AdminMiddleware - overuje či je používateľ prihlásený ako admin
 */
class AdminMiddleware
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!session('admin_logged_in')) {
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

        return $next($request);
    }
}

