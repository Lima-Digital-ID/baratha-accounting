<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DetailKas extends Model
{
    use HasFactory;

    protected $table = 'detail_kas';

    public function kodeRekening()
    {
        return $this->belongsTo('App\Models\Kas', 'kode_kas');
    }
}
