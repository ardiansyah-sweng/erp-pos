<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;

class ProductController extends Controller
{
    public function getProducts(Request $request)
    {
        $search = trim((string) $request->query('search', ''));

        $products = Product::query()
            ->where('is_active', true)
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($innerQuery) use ($search) {
                    $innerQuery->where('name', 'like', '%' . $search . '%')
                        ->orWhere('sku', 'like', '%' . $search . '%')
                        ->orWhere('barcode', 'like', '%' . $search . '%');
                });
            })
            ->orderBy('name')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $products,
        ]);
    }

        public function index()
    {
        $products = Product::orderBy('name')->get();

        return view('products.index', compact('products'));
    }


    public function store(Request $request)
    {
        $validated = $request->validate([
            'sku' => 'required|unique:products,sku',
            'barcode' => 'nullable|string|max:100',
            'name' => 'required|string|max:255',
            'unit' => 'required|string|max:50',
            'selling_price' => 'required|numeric|min:0',
            'stock_quantity' => 'required|integer|min:0',
            'min_stock' => 'required|integer|min:0',
        ]);

        Product::create([
            ...$validated,
            'is_active' => true,
        ]);

        return redirect()->route('products.list')
            ->with('success', 'Produk berhasil ditambahkan');
    }

    public function update(Request $request, Product $product)
    {

        $validated = $request->validate([
            'sku' => 'required|unique:products,sku,' . $product->id,
            'barcode' => 'nullable|string|max:100',
            'name' => 'required|string|max:255',
            'unit' => 'required|string|max:50',
            'selling_price' => 'required|numeric|min:0',
            'stock_quantity' => 'required|integer|min:0',
            'min_stock' => 'required|integer|min:0',
            'is_active' => 'required|boolean',
        ]);
        
        $product->update($validated);

        return redirect()->route('products.list')
            ->with('success', 'Produk berhasil diupdate');
    }

    public function destroy(Product $product)
    {
        $product->delete();

        return redirect()->route('products.list')
            ->with('success', 'Produk berhasil dihapus');
    }
}
