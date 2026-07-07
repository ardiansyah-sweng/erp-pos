<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    protected $table = 'customers';

    protected $fillable = [
        'customer_code',
        'name',
        'email',
        'phone',
        'address',
        'points',
        'member_level',
        'loyalty_points',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'points' => 'integer',
        'loyalty_points' => 'integer',
    ];

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function transactions()
    {
        return $this->hasMany(Transaction::class, 'customer_id');
    }
}
