<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DetailPemakaianBarang extends Model
{
    use HasFactory;

    protected $table = 'detail_pemakaian_barang';

    protected $fillable = ['kode_pemakaian', 'kode_barang', 'qty', 'subtotal', 'kode_biaya', 'keterangan', 'created_at', 'updated_at', 'created_at', 'updated_at'];

    public function pemakaianBarang()
    {
        return $this->belongsTo('App\Models\PemakaianBarang', 'kode_pemakaian');
    }
    
    public function barang()
    {
        return $this->belongsTo('App\Models\Barang', 'kode_barang');
    }
    
    public function kodeBiaya()
    {
        return $this->belongsTo('App\Models\KodeBiaya', 'kode_biaya');
    }
}
