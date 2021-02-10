<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Bank extends Model
{
    use HasFactory;

    protected $table = 'bank';

    protected $primaryKey = 'kode_bank';
    public $incrementing = false;

    public function kodeRekening()
    {
        return $this->belongsTo('App\Models\KodeRekening', 'kode_rekening');
    }

    public function detailBank()
    {
        return $this->hasMany('App\Models\DetailBank');
    }
}
