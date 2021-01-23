<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PembelianBarang extends Model
{
    use HasFactory;

    protected $table = 'pembelian_barang';
    protected $primaryKey = 'kode_pembelian';

    public $incrementing = false;

    public function supplier()
    {
        return $this->belongsTo('App\Models\Supplier', 'kode_supplier');
    }

    public function detailPembelianBarang()
    {
        return $this->hasMany('App\Models\DetailPembelianBarang');
    }
}
