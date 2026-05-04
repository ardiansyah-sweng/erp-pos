<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Service\AuthService;

class LoginController extends Controller
{
    protected $authService;

    // inject AuthService
    public function __construct(AuthService $authService)
    {
        $this->authService = $authService;
    }

    // tampilkan form login
    public function index()
    {
        return view('login');
    }

    // method cashier login
    public function login(Request $request)
    {
        // menerima request + validasi
        $validated = $request->validate([
            'email' => 'required|email',
            'password' => 'required|min:6',
        ]);

        // delegasi ke service
        $result = $this->authService->loginCashier($validated);

        // response
        if ($result['success']) {
            return response()->json([
                'message' => 'Login berhasil',
                'data' => $result['data']
            ], 200);
        }

        return response()->json([
            'message' => 'Login gagal'
        ], 401);
    }
}