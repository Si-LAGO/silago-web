<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Memastikan pengguna yang mengakses adalah admin.
 * Dipakai untuk semua route panel admin.
 */
class EnsureAdmin
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (! $user || $user->role !== 'admin') {
            if ($request->expectsJson()) {
                return response()->json(['message' => 'Akses tidak diizinkan.'], 403);
            }
            return redirect()->route('admin.login');
        }

        return $next($request);
    }
}
