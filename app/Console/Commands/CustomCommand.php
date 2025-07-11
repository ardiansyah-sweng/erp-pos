<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class CustomCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:custom-command';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    public function __construct()
    {
        parent:: __construct();
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        \Log::info('Custom task dijalankan!');
        $this->info('Custome task berhasil dijalankan.');
    }
}
