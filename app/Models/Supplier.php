<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Supplier extends Model
{
    use HasFactory;
    protected $table = 'supplier';
    protected $primaryKey = 'kode_supplier';
    public $incrementing = false;

    public function pembelianBarang()
    {
        return $this->hasMany('App\Models\PembelianBarang');
    }

    public function kartuHutang()
    {
        return $this->hasMany('App\Models\KartuHutang');
    }
}
