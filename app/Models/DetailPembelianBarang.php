<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DetailPembelianBarang extends Model
{
    use HasFactory;

    protected $table = 'detail_pembelian_barang';

    public function pembelianBarang()
    {
        return $this->belongsTo('App\Models\PembelianBarang', 'kode_pembelian');
    }
    
    public function barang()
    {
        return $this->belongsTo('App\Models\Barang', 'kode_barang');
    }
}
