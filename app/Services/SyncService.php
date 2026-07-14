<?php

namespace App\Services;

use App\Models\SyncHistory;

class SyncService
{
    /**
     * Mencatat aktivitas ke tabel sync_histories.
     *
     * @param string $module  Nama modul (Product, Transaction, Customer, dll)
     * @param string $status  Status aktivitas (Berhasil / Gagal)
     * @param string $message Pesan aktivitas
     */
    public function log(
        string $module,
        string $status,
        string $message
    ): void {
        SyncHistory::create([
            'module'    => $module,
            'status'    => $status,
            'message'   => $message,
            'synced_at' => now(),
        ]);
    }
}