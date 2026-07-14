<?php

namespace App\Http\Controllers;

use App\Services\LoginService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class LoginController extends Controller
{
    public function showLoginForm(Request $request): View|RedirectResponse
    {
        if ($request->session()->has('cashier_id')) {
            return redirect()->route('dashboard');
        }

        return view('auth.login');
    }

    public function login(Request $request, LoginService $loginService): RedirectResponse
    {
        $credentials = $request->validate([
            'username' => ['required', 'string'],
            'password' => ['required', 'string'],
        ]);

        $result = $loginService->loginService(
            $credentials['username'],
            $credentials['password'],
        );

        if (! $result['success']) {
            return back()
                ->withInput($request->only('username'))
                ->withErrors(['username' => $result['message']]);
        }

        $request->session()->regenerate();

        return redirect()
            ->intended(route('dashboard'))
            ->with('status', 'Selamat datang, ' . $result['data']->name . '!');
    }

    public function logout(Request $request): RedirectResponse
    {
        $request->session()->forget([
            'cashier_id',
            'cashier_name',
            'cashier_username',
            'cashier_role',
        ]);

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()
            ->route('login')
            ->with('status', 'Anda telah keluar.');
    }
}
