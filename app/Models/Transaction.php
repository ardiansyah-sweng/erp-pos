<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    protected $table = 'transaction';
    protected $fillable = ['total'];

    /**
     * Ambil nama kolom tertentu dari $fillable
     */
    public function getColumn($index)
    {
        $fillable = $this->getFillable();
        return $fillable[$index] ?? null;
    }
}
