<?php

namespace App\Http\Controllers;

use App\Models\Cashiers;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class CashierController extends Controller
{
    public function index()
    {
        $cashiers = Cashiers::all();

        return view('cashier.index', compact('cashiers'));
    }

    public function checkUsername(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'username' => ['required', 'string', 'max:255'],
            'ignore_id' => ['nullable', 'integer', 'exists:cashiers,id'],
        ]);

        $usernameExists = Cashiers::query()
            ->where('username', $validated['username'])
            ->when(
                isset($validated['ignore_id']),
                fn ($query) => $query->whereKeyNot($validated['ignore_id'])
            )
            ->exists();

        return response()->json([
            'success' => true,
            'available' => ! $usernameExists,
            'message' => $usernameExists
                ? 'Username sudah digunakan.'
                : 'Username tersedia.',
        ]);
    }

    public function add(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'username' => ['required', 'string', 'max:255', 'unique:cashiers,username'],
            'password' => ['required', 'string', 'min:8'],
        ]);

        Cashiers::create($validated);

        return redirect()
            ->route('cashier.index')
            ->with('success', 'Kasir berhasil ditambahkan.');
    }

    public function edit(int $id): JsonResponse
    {
        $cashier = Cashiers::findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => $cashier,
        ]);
    }

    public function update(Request $request, int $id): RedirectResponse
    {
        $cashier = Cashiers::findOrFail($id);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'username' => [
                'required',
                'string',
                'max:255',
                Rule::unique('cashiers', 'username')->ignore($cashier->id),
            ],
            'password' => ['nullable', 'string', 'min:8'],
        ]);

        if (empty($validated['password'])) {
            unset($validated['password']);
        }

        $cashier->update($validated);

        return redirect()
            ->route('cashier.index')
            ->with('success', 'Data kasir berhasil diperbarui.');
    }
}
