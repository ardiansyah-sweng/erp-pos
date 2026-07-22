<?php

namespace App\Http\Controllers;

use App\Models\StoreSetting;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SettingController extends Controller
{
    public function index(): View
    {
        $setting = StoreSetting::query()->firstOrNew([], StoreSetting::defaults());

        return view('settings.index', compact('setting'));
    }

    public function update(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'store_name' => ['required', 'string', 'max:255'],
            'address' => ['nullable', 'string', 'max:1000'],
            'phone' => ['nullable', 'string', 'max:30'],
            'receipt_footer' => ['nullable', 'string', 'max:500'],
        ]);

        $validated['currency'] = 'IDR';
        $validated['low_stock_notification'] = $request->boolean('low_stock_notification');

        $setting = StoreSetting::query()->firstOrNew();
        $setting->fill($validated)->save();

        return redirect()
            ->route('settings.index')
            ->with('success', 'Pengaturan toko berhasil diperbarui.');
    }
}
