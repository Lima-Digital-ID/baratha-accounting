<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KartuHutang extends Model
{
    use HasFactory;

    protected $table = 'kartu_hutang';

    public function supplier()
    {
        return $this->belongsTo('App\Models\Supplier', 'kode_supplier');
    }
}
