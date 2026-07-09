<?php

namespace App\Http\Controllers;

use App\Models\Category;

class PosController extends Controller
{
    public function index()
    {
        $categories = Category::where('is_active', true)->orderBy('name')->get();

        return view('pos.index', compact('categories'));
    }
}