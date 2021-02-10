<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DetailBank extends Model
{
    use HasFactory;

    protected $table = 'detail_bank';

    protected $fillable = ['kode_bank', 'lawan', 'subtotal', 'keterangan', 'created_at', 'updated_at'];

    public function kodeRekening()
    {
        return $this->belongsTo('App\Models\Bank', 'kode_bank');
    }
}
