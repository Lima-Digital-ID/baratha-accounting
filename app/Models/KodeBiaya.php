<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KodeBiaya extends Model
{
    use HasFactory;

    protected $table = 'kode_biaya';
    protected $primaryKey = 'kode_biaya';
    public $incrementing = false;

    public function kodeRekening()
    {
        return $this->belongsTo('App\Models\KodeRekening', 'kode_rekening');
    }
}
