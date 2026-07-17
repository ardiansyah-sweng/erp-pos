<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\SyncHistory;
use Carbon\Carbon;

class SyncHistorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        SyncHistory::insert([
            [
                'module' => 'Produk',
                'status' => 'Berhasil',
                'message' => 'Sinkronisasi data produk berhasil.',
                'synced_at' => Carbon::now()->subMinutes(10),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'module' => 'Transaksi',
                'status' => 'Gagal',
                'message' => 'Sinkronisasi gagal karena koneksi server terputus.',
                'synced_at' => Carbon::now()->subMinutes(30),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'module' => 'Kasir',
                'status' => 'Berhasil',
                'message' => 'Sinkronisasi data kasir berhasil.',
                'synced_at' => Carbon::now()->subHour(),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'module' => 'Pelanggan',
                'status' => 'Berhasil',
                'message' => 'Sinkronisasi data pelanggan berhasil.',
                'synced_at' => Carbon::now()->subHours(2),
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}