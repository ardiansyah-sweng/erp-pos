<?php

namespace App\Http\Controllers;

use App\Models\SyncHistory;
use Illuminate\Http\Request;

class SyncHistoryController extends Controller
{
    /**
     * Menampilkan halaman riwayat sinkronisasi
     */
    public function index(Request $request)
    {
        $search = $request->get('search');

        $histories = SyncHistory::when($search, function ($query) use ($search) {
                $query->where('module', 'like', '%' . $search . '%');
            })
            ->orderByDesc('synced_at')
            ->paginate(10)
            ->withQueryString();

        $summary = [
            'total' => SyncHistory::count(),
            'success' => SyncHistory::where('status', 'Berhasil')->count(),
            'failed' => SyncHistory::where('status', 'Gagal')->count(),
        ];

        return view('sync-histories.index', compact(
            'histories',
            'summary',
            'search'
        ));
    }
}