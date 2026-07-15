<?php

namespace App\Http\Controllers;

use App\Services\LoginService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class LoginController extends Controller
{
    public function __construct(protected LoginService $loginService)
    {
    }

    public function showLoginForm(): View
    {
        return view('auth.login');
    }

    public function login(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'username' => ['required', 'string'],
            'password' => ['required', 'string'],
        ], [
            'username.required' => 'Username tidak boleh kosong.',
            'password.required' => 'Password tidak boleh kosong.',
        ]);

        $result = $this->loginService->loginService($credentials['username'], $credentials['password']);

        if (!$result['success']) {
            return back()
                ->withErrors(['username' => $result['message']])
                ->withInput($request->only('username'));
        }

        return redirect()->route('pos.index')->with('status', $result['message']);
    }
}