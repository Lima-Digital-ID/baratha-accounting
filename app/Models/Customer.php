<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    use HasFactory;
    protected $table = 'customer';
    protected $primaryKey = 'kode_customer';
    public $incrementing = false;

    public function penjualanCatering()
    {
        return $this->hasMany('App\Models\PenjualanCatering');
    }

    public function kartuPiutang()
    {
        return $this->hasMany('App\Models\KartuPiutang');
    }
}
