<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StoreSetting extends Model
{
    protected $fillable = [
        'store_name',
        'address',
        'phone',
        'currency',
        'receipt_footer',
        'low_stock_notification',
    ];

    protected $casts = [
        'low_stock_notification' => 'boolean',
    ];

    /**
     * @return array<string, mixed>
     */
    public static function defaults(): array
    {
        return [
            'store_name' => 'ERP POS',
            'address' => null,
            'phone' => null,
            'currency' => 'IDR',
            'receipt_footer' => 'Terima kasih telah berbelanja.',
            'low_stock_notification' => true,
        ];
    }
}
