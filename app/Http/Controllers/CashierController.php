<?php

namespace App\Http\Controllers;

use App\Models\Cashiers;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class CashierController extends Controller
{
    public function index(): View
    {
        $cashiers = Cashiers::all();

        return view('cashier.index', compact('cashiers'));
    }

    public function create(): View
    {
        return view('cashier.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'username' => ['required', 'string', 'max:255', 'unique:cashiers,username'],
            'password' => ['required', 'string', 'min:6', 'confirmed'],
        ]);

        Cashiers::create($validated);

        return redirect()
            ->route('cashiers.index')
            ->with('success', 'Cashier berhasil ditambahkan.');
    }

    public function search(Request $request)
    {
        $keyword = trim((string) $request->query('keyword', ''));

        $cashiers = Cashiers::query()
            ->when($keyword !== '', function ($query) use ($keyword) {
                $query->where('name', 'like', '%' . $keyword . '%')
                    ->orWhere('username', 'like', '%' . $keyword . '%');
            })
            ->orderBy('name')
            ->get();

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'data' => $cashiers,
            ]);
        }

        return view('cashier.index', compact('cashiers', 'keyword'));
    }

    public function resetPassword(Request $request, int $id): RedirectResponse
    {
        $validated = $request->validate([
            'password' => ['required', 'string', 'min:6', 'confirmed'],
        ]);

        $cashier = Cashiers::findOrFail($id);
        $cashier->update([
            'password' => $validated['password'],
        ]);

        return redirect()
            ->route('cashiers.index')
            ->with('success', 'Password cashier berhasil direset.');
    }
}
