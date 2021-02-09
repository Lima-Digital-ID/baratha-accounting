<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DetailKas extends Model
{
    use HasFactory;

    protected $table = 'detail_kas';

    protected $fillable = ['kode_kas', 'lawan', 'subtotal', 'keterangan'];

    public function kodeRekening()
    {
        return $this->belongsTo('App\Models\Kas', 'kode_kas');
    }
}
