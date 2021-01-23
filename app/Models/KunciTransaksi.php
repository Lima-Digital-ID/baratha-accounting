<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KunciTransaksi extends Model
{
    use HasFactory;
    protected $table = 'kunci_transaksi';
    protected $primaryKey = 'id';

    public function kunciTransaksi()
    {
        return $this->hasMany('App\Models\KunciTransaksi');
    }
}
