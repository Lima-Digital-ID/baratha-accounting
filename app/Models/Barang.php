<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Barang extends Model
{
    use HasFactory;

    protected $table='barang';
    protected $primaryKey = 'kode_barang';

    public $incrementing = false;

    public function kategoriBarang()
    {
        return $this->belongsTo('App\Models\KategoriBarang', 'id_kategori');
    }
}
