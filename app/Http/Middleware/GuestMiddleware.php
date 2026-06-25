<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class GuestMiddleware
{
    /**
     * Jika user sudah login, redirect ke dashboard.
     * Dipakai di route login & register.
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Mengecek apakah user saat ini sudah login
        if (Auth::check()) {
            // Jika sudah login, langsung lempar ke halaman dashboard
            return redirect()->route('dashboard');
        }

        // Jika belum login (benar-benar guest), biarkan mengakses halaman
        return $next($request);
    }
}