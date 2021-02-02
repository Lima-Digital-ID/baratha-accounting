<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KartuStock extends Model
{
    use HasFactory;

    protected $table = 'kartu_stock';

    public function barang()
    {
        return $this->belongsTo('App\Models\Barang', 'kode_barang');
    }
}
