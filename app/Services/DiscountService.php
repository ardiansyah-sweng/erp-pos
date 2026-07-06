<?php

namespace App\Services;

use App\Models\Discount;
use App\Models\Product;
use Illuminate\Support\Facades\DB;

class DiscountService
{
  public function store(array $data): Discount
  {
    return DB::transaction(function () use ($data) {
      return Discount::create($data);
    });
  }

  public function delete(Discount $discount): void
  {
    DB::transaction(function () use ($discount) {
      $discount->delete();
    });
  }

  public function getActiveDiscountForProduct(int $productId): ?Discount
  {
    return Discount::where('product_id', $productId)
      ->where('is_active', true)
      ->whereDate('start_date', '<=', now())
      ->whereDate('end_date', '>=', now())
      ->first();
  }
}
