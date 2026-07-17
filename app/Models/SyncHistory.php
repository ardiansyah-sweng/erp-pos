<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SyncHistory extends Model
{
    protected $table = 'sync_histories';

    protected $fillable = [
        'module',
        'status',
        'message',
        'synced_at',
    ];

    /**
     * Ambil nama kolom tertentu dari $fillable
     */
    public function getColumn($index)
    {
        $fillable = $this->getFillable();
        return $fillable[$index] ?? null;
    }
}