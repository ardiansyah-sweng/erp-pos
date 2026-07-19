<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use App\Models\Product;
use App\Services\SyncService;
use App\Services\ProductService;

class ProductController extends Controller
{
    protected $productService;
    protected $syncService;

    public function __construct(ProductService $productService, SyncService $syncService)
    {
        $this->productService = $productService;
        $this->syncService = $syncService;
    }

    public function getProducts(Request $request)
    {
        $search = trim((string) $request->query('search', ''));
        $categoryId = $request->query('category_id');
        $sort = $request->query('sort');

        $results = $this->productService->search($search)
            ->when($categoryId, function ($collection) use ($categoryId) {
                return $collection->where('category_id', (int) $categoryId);
            })
            ->when($sort === 'price_asc', function ($collection) {
                return $collection->sortBy('selling_price');
            })
            ->when($sort === 'price_desc', function ($collection) {
                return $collection->sortByDesc('selling_price');
            })
            ->when($sort === 'name_asc', function ($collection) {
                return $collection->sortBy('name', SORT_NATURAL | SORT_FLAG_CASE);
            })
            ->when($sort === 'name_desc', function ($collection) {
                return $collection->sortByDesc('name', SORT_NATURAL | SORT_FLAG_CASE);
            })
            ->values();

        return response()->json([
            'success' => true,
            'data' => $results,
        ]);
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

    public function manage(Request $request): View
    {
        $search = trim((string) $request->query('search', ''));

        $products = Product::query()
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($innerQuery) use ($search) {
                    $innerQuery->where('name', 'like', "%{$search}%")
                        ->orWhere('sku', 'like', "%{$search}%")
                        ->orWhere('barcode', 'like', "%{$search}%");
                });
            })
            ->orderBy('name')
            ->paginate(15)
            ->withQueryString();

        return view('products.manage', compact('products', 'search'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'sku' => ['required', 'string', 'max:50', 'unique:products,sku'],
            'barcode' => ['nullable', 'string', 'max:100', 'unique:products,barcode'],
            'selling_price' => ['required', 'integer', 'min:0'],
            'unit' => ['required', 'string', 'max:20'],
            'stock_quantity' => ['required', 'integer', 'min:0'],
            'min_stock' => ['required', 'integer', 'min:0'],
            'description' => ['nullable', 'string', 'max:500'],
        ]);

        $product = Product::create($validated);

        $this->syncService->log(
            'Product', 
            'Berhasil', 
            "CREATE - Produk berhasil ditambahkan: {$product->name}");

        return Redirect::route('products.manage')->with('success', 'Produk berhasil ditambahkan.');
    }

    public function update(Request $request, Product $product): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'sku' => ['required', 'string', 'max:50', Rule::unique('products', 'sku')->ignore($product->id)],
            'barcode' => ['nullable', 'string', 'max:100', Rule::unique('products', 'barcode')->ignore($product->id)],
            'selling_price' => ['required', 'integer', 'min:0'],
            'unit' => ['required', 'string', 'max:20'],
            'stock_quantity' => ['required', 'integer', 'min:0'],
            'min_stock' => ['required', 'integer', 'min:0'],
            'description' => ['nullable', 'string', 'max:500'],
        ]);

        $product->update($validated);

        $this->syncService->log(
            'Product', 
            'Berhasil', 
            "UPDATE - Produk berhasil diperbarui: {$product->name}");
        return Redirect::route('products.manage')->with('success', 'Produk berhasil diperbarui.');
    }

    public function destroy(Product $product): RedirectResponse
    {
        $product->update(['is_active' => !$product->is_active]);

        $status = $product->is_active ? 'diaktifkan' : 'dinonaktifkan';
        $this->syncService->log(
            'Product', 
            'Berhasil', 
            "UPDATE - Produk berhasil {$status}: {$product->name}");
        return Redirect::route('products.manage')->with('success', "Produk berhasil {$status}.");
    }
    
    public function getLowStock()
    {
        $products = Product::where('is_active', true)
            ->whereColumn('stock_quantity', '<=', 'min_stock')
            ->orderBy('stock_quantity')
            ->get(['id', 'name', 'sku', 'stock_quantity', 'min_stock', 'unit']);

        return response()->json([
            'success' => true,
            'count'   => $products->count(),
            'data'    => $products,
        ]);
    }
}
}
