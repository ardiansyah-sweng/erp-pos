<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Discount extends Model
{
  protected $fillable = [
    'product_id',
    'name',
    'type',
    'value',
    'start_date',
    'end_date',
    'is_active',
  ];

  protected $casts = [
    'is_active'  => 'boolean',
    'value'      => 'integer',
    'start_date' => 'date',
    'end_date'   => 'date',
  ];

  public function product()
  {
    return $this->belongsTo(Product::class);
  }

  public function isCurrentlyActive(): bool
  {
    $today = Carbon::today();
    return $this->is_active
      && $today->greaterThanOrEqualTo($this->start_date)
      && $today->lessThanOrEqualTo($this->end_date);
  }

  public function getDiscountedPrice(int $originalPrice): int
  {
    if ($this->type === 'percentage') {
      return (int) ($originalPrice - ($originalPrice * $this->value / 100));
    }
    return max(0, $originalPrice - $this->value);
  }
}
