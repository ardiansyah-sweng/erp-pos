<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Http;

class ProductController extends Controller
{
    public function getProducts()
    {

        $products = Http::get('http://127.0.0.1:8000/products')->json();
    }
}
