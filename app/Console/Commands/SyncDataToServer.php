<?php

namespace App\Console\Commands;

use App\Models\Coba;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;

class SyncDataToServer extends Command
{
    protected $signature = 'sync:data-to-server';

    protected $description = 'Sync local data to server';

    public function handle()
    {
        Coba::create([]);
        // $serverUrl = 'https://your-server-domain/api/sync-data'; // Ganti dengan URL API server

        // $response = Http::post($serverUrl, $data);

        // if ($response->successful()) {
        //     $this->info('Data synced successfully.');
        // } else {
        //     $this->error('Failed to sync data. Error: ' . $response->body());
        // }
    }
}
