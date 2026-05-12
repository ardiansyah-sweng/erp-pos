<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;

class ProductCrudController extends Controller
{
    public function index()
    {
        $products = Product::latest()->paginate(10);
        return view('product-index', compact('products'));
    }

    public function create()
    {
        return view('product-create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'product_id'     => 'required|string|size:6|unique:products,product_id',
            'barcode'        => 'required|string|max:20|unique:products,barcode',
            'name'           => 'required|string|max:255',
            'description'    => 'nullable|string',
            'category_id'    => 'required|integer',
            'category_name'  => 'required|string|max:255',
            'unit'           => 'required|string|max:20',
            'cost_price'     => 'required|integer|min:0',
            'selling_price'  => 'required|integer|min:0',
            'stock_quantity' => 'required|integer|min:0',
            'min_stock'      => 'required|integer|min:0',
        ]);

        Product::create($request->all());

        return redirect()->route('products.index')
            ->with('success', 'Produk berhasil ditambahkan!');
    }

    public function show(Product $product)
    {
        return view('product-show', compact('product'));
    }

    public function edit(Product $product)
    {
        return view('product-edit', compact('product'));
    }

    public function update(Request $request, Product $product)
    {
        $request->validate([
            'product_id'     => 'required|string|size:6|unique:products,product_id,' . $product->id,
            'barcode'        => 'required|string|max:20|unique:products,barcode,' . $product->id,
            'name'           => 'required|string|max:255',
            'description'    => 'nullable|string',
            'category_id'    => 'required|integer',
            'category_name'  => 'required|string|max:255',
            'unit'           => 'required|string|max:20',
            'cost_price'     => 'required|integer|min:0',
            'selling_price'  => 'required|integer|min:0',
            'stock_quantity' => 'required|integer|min:0',
            'min_stock'      => 'required|integer|min:0',
        ]);

        $product->update($request->all());

        return redirect()->route('products.index')
            ->with('success', 'Produk berhasil diperbarui!');
    }

    public function destroy(Product $product)
    {
        $product->delete();

        return redirect()->route('products.index')
            ->with('success', 'Produk berhasil dihapus!');
    }
}