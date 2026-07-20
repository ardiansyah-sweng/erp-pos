<?php

namespace App\Http\Controllers;

use App\Models\Discount;
use App\Models\Product;
use App\Services\DiscountService;
use App\Services\SyncService; // Ditambahkan untuk mencatat log aktivitas
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DiscountController extends Controller
{
  protected $discountService;
  protected $syncService;

  public function __construct(DiscountService $discountService, SyncService $syncService) 
  {
    $this->discountService = $discountService;
    $this->syncService = $syncService;
  }

  public function index(Request $request): View
  {
    $search = trim((string) $request->query('search', ''));

    $discounts = Discount::with('product')
      ->when($search !== '', function ($query) use ($search) {
        $query->where('name', 'like', "%{$search}%")
          ->orWhereHas('product', function ($q) use ($search) {
            $q->where('name', 'like', "%{$search}%")
              ->orWhere('sku', 'like', "%{$search}%");
          });
      })
      ->latest()
      ->paginate(10)
      ->appends(request()->query());

    $products = Product::where('is_active', true)->orderBy('name')->get();

    return view('discounts.index', compact('discounts', 'products', 'search'));
  }

  public function store(Request $request): RedirectResponse
  {
    $validated = $request->validate([
      'product_id' => ['required', 'exists:products,id'],
      'name'       => ['required', 'string', 'max:255'],
      'type'       => ['required', 'in:percentage,fixed'],
      'value'      => ['required', 'integer', 'min:1'],
      'start_date' => ['required', 'date'],
      'end_date'   => ['required', 'date', 'after_or_equal:start_date'],
      'is_active'  => ['boolean'],
    ]);

    $validated['is_active'] = $request->boolean('is_active', true);

    try {
      $discount = $this->discountService->store($validated);

      // Menggunakan SyncService persis seperti contoh CustomerController
      $this->syncService->log(
        'Diskon',
        'Berhasil',
        "CREATE - Diskon baru berhasil dibuat: {$validated['name']}"
      );

      return redirect()
        ->route('discounts.index')
        ->with('success', 'Diskon berhasil ditambahkan.');

    } catch (\Exception $e) {
      // Catat log jika gagal
      $this->syncService->log(
        'Diskon',
        'Gagal',
        "CREATE - Gagal menambahkan diskon '{$validated['name']}': " . $e->getMessage()
      );

      return redirect()
        ->route('discounts.index')
        ->with('error', 'Gagal menambahkan diskon.');
    }
  }

  public function destroy(Discount $discount): RedirectResponse
  {
    try {
      $discountName = $discount->name;
      $this->discountService->delete($discount);

      // Menggunakan SyncService saat berhasil menghapus
      $this->syncService->log(
        'Diskon',
        'Berhasil',
        "DELETE - Diskon berhasil dihapus: {$discountName}"
      );

      return redirect()
        ->route('discounts.index')
        ->with('success', 'Diskon berhasil dihapus.');

    } catch (\Exception $e) {
      // Catat log jika gagal menghapus
      $this->syncService->log(
        'Diskon',
        'Gagal',
        "DELETE - Gagal menghapus diskon: " . $e->getMessage()
      );

      return redirect()
        ->route('discounts.index')
        ->with('error', 'Gagal menghapus diskon.');
    }
  }

  public function getActiveForProducts(Request $request)
  {
    $productIds = explode(',', $request->query('ids', ''));
    $productIds = array_filter(array_map('intval', $productIds));

    $discounts = Discount::whereIn('product_id', $productIds)
      ->where('is_active', true)
      ->whereDate('start_date', '<=', now())
      ->whereDate('end_date', '>=', now())
      ->get()
      ->keyBy('product_id');

    return response()->json([
      'success' => true,
      'data'    => $discounts,
    ]);
  }
}