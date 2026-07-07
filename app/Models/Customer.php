<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    protected $table = 'customers';

    protected $fillable = [
        'customer_code',
        'name',
        'phone',
        'email',
        'address',
        'points',
        'member_level'
    ];

    public function transactions()
    {
        return $this->hasMany(Transaction::class, 'customer_id');
    }
}
