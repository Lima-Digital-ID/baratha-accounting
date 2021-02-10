<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PenjualanCatering extends Model
{
    use HasFactory;

    protected $table = 'penjualan_catering';
    protected $primaryKey = 'kode_penjualan';

    public $incrementing = false;

    public function customer()
    {
        return $this->belongsTo('App\Models\Customer', 'kode_customer');
    }
}
