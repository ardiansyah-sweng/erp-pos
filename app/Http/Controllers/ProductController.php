<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class ProductController extends Controller
{
    function getProducts() {

        $products = Http::get('http://127.0.0.1:8000/products')->json();
    }
}
