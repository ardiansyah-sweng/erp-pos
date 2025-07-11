<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

class ScheduleController extends Controller
{

    public function index()
    {
        return view('schedule');
    }
    
    public function run(Request $request)
    {
        Artisan::call('sync:data-to-server');
        return 'Schedule berhasil dijalankan!';
    }
}
