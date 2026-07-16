<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureCashierAuthenticated
{
    /**
     * Pastikan pengguna sudah login sebagai kasir sebelum mengakses halaman.
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Halaman login & logout tetap bisa diakses tanpa login.
        if ($request->is('login') || $request->is('logout')) {
            return $next($request);
        }

        if (! $request->session()->has('cashier_id')) {
            return redirect()
                ->guest(route('login'))
                ->with('status', 'Silakan login terlebih dahulu.');
        }

        return $next($request);
    }
}
