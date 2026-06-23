<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
	
class Customer extends Model
{
    protected $fillable = [
        'name',
        'email',
        'phone',
        'address',
        'loyalty_points',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'loyalty_points' => 'integer',
    ];

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}