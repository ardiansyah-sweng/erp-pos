<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureRole
{
    /**
     * Batasi akses berdasarkan peran (role) kasir yang sedang login.
     *
     * Contoh pemakaian pada route: ->middleware('role:admin')
     */
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        $role = $request->session()->get('cashier_role', 'kasir');

        if (! empty($roles) && ! in_array($role, $roles, true)) {
            abort(403, 'Anda tidak memiliki akses ke halaman ini.');
        }

        return $next($request);
    }
}
