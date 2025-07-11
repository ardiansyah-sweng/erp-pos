<?php

// app/Console/Kernel.php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    protected function schedule(Schedule $schedule)
    {
        // $schedule->command('sync:data-to-server')->everyMinute();
        
        // $schedule->call(function () {
        //     \Log::info('Scheduler test executed at ' . now());
        // })->everyMinute();    
    }
}
