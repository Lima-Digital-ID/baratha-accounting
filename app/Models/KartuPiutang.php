<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KartuPiutang extends Model
{
    use HasFactory;

    protected $table = 'kartu_piutang';

    public function customer()
    {
        return $this->belongsTo('App\Models\Customer', 'kode_customer');
    }
}
