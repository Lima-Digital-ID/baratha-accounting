<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PemakaianBarang extends Model
{
    use HasFactory;

    protected $table = 'pemakaian_barang';
    protected $primaryKey = 'kode_pemakaian';

    public $incrementing = false;

    public function detailPemakaianBarang()
    {
        return $this->hasMany('App\Models\DetailPemakaianBarang');
    }
}
