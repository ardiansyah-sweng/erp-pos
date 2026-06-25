<?php

namespace App\Http\Controllers;

class ReportController extends Controller
{
    // Menampilkan halaman dashboard laporan penjualan.
    // Data diambil di sisi browser memakai endpoint /transactions dan /products yang sudah ada.
    public function index()
    {
        return view('report.index');
    }
}
