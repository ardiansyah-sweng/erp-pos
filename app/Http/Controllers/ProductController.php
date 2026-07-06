<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Services\ProductService;

class ProductController extends Controller
{
    protected $productService;

    public function __construct(ProductService $productService)
    {
        $this->productService = $productService;
    }

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

    public function index(Request $request)
    {
        $search = trim((string) $request->query('search', ''));

        $products = Product::query()
            ->when($search !== '', fn($q) => $q->where('name', 'like', "%$search%")
                ->orWhere('sku', 'like', "%$search%"))
            ->orderBy('name')
            ->paginate(15)
            ->withQueryString();

        return view('products.index', compact('products', 'search'));
    }

    public function create()
    {
        return view('products.form', ['product' => null]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'sku'            => 'required|unique:products,sku',
            'name'           => 'required',
            'barcode'        => 'nullable|unique:products,barcode',
            'unit'           => 'required',
            'selling_price'  => 'required|integer|min:0',
            'stock_quantity' => 'required|integer|min:0',
            'min_stock'      => 'required|integer|min:0',
            'description'    => 'nullable',
        ]);

        $data['is_active'] = $request->boolean('is_active', true);
        Product::create($data);

        return redirect()->route('produk.index')->with('success', 'Produk berhasil ditambahkan.');
    }

    public function edit(Product $product)
    {
        return view('products.form', compact('product'));
    }

    public function update(Request $request, Product $product)
    {
        $data = $request->validate([
            'sku'            => 'required|unique:products,sku,' . $product->id,
            'name'           => 'required',
            'barcode'        => 'nullable|unique:products,barcode,' . $product->id,
            'unit'           => 'required',
            'selling_price'  => 'required|integer|min:0',
            'stock_quantity' => 'required|integer|min:0',
            'min_stock'      => 'required|integer|min:0',
            'description'    => 'nullable',
        ]);

        $data['is_active'] = $request->boolean('is_active');
        $product->update($data);

        return redirect()->route('produk.index')->with('success', 'Produk berhasil diperbarui.');
    }

    public function destroy(Product $product)
    {
        $product->delete();

        return redirect()->route('produk.index')->with('success', 'Produk berhasil dihapus.');
    }

    function getItemBySKU($sku)
    {
        $product = $this->productService->getItemBySKU($sku);

        if (!$product) {
            return response()->json([
                'success' => false,
                'message' => 'Produk dengan SKU ' . $sku . ' tidak ditemukan.',
                'data'    => null,
            ], 404);
        }

        return response()->json([
            'success' => true,
            'message' => 'Produk berhasil ditemukan.',
            'data'    => $product,
        ], 200);
    }
}