<?php

// app/Console/Commands/SyncData.php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;

use App\Models\Transaction;

class SyncData extends Command
{
    protected $signature = 'sync:data';
    protected $description = 'Sinkronisasi data ke server';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        // Ambil data dari tabel lokal yang perlu disinkronkan
        // $dataToSync = DB::table('your_local_table')->where('sync_status', 'pending')->get();
        $transactions = Transaction::all();
        dd($transactions);

        // Siapkan data yang akan dikirim ke server
        // $syncData = $dataToSync->map(function ($item) {
        //     return [
        //         'unique_column' => $item->unique_column,  // Ganti dengan kolom yang unik
        //         'field1' => $item->field1,
        //         'field2' => $item->field2,
        //         // Sesuaikan dengan field lainnya
        //     ];
        // });

        // Kirim data ke server
        // $response = Http::post('http://your-server.com/api/sync-data', [
        //     'data' => $syncData->toArray()
        // ]);

        // Cek response
        // if ($response->successful()) {
        //     // Update status sinkronisasi di database lokal
        //     DB::table('your_local_table')->whereIn('id', $dataToSync->pluck('id'))->update([
        //         'sync_status' => 'done'
        //     ]);
        //     $this->info('Data synced successfully!');
        // } else {
        //     $this->error('Failed to sync data');
        // }
    }
}
