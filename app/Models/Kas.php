<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Kas extends Model
{
    use HasFactory;

    protected $table = 'kas';

    protected $primaryKey = 'kode_kas';
    public $incrementing = false;

    public function kodeRekening()
    {
        return $this->belongsTo('App\Models\KodeRekening', 'kode_rekening');
    }

    public function detailKas()
    {
        return $this->hasMany('App\Models\DetailKas');
    }
}
