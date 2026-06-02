<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoginRequest;
use App\Services\AuthService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class LoginController extends Controller
{
    public function __construct(
        protected AuthService $authService
    ) {}

    public function login(LoginRequest $request): JsonResponse
    {
        $result = $this->authService->loginCashier(
            username: $request->validated('username'),
            password: $request->validated('password'),
        );

        return response()->json([
            'message' => 'Login berhasil',
            'data'    => $result,
        ]);
    }

    public function showLogin(): View
    {
        return view('auth.login');
    }
}