<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DetailMemorial extends Model
{
    use HasFactory;

    protected $table = 'detail_memorial';

    protected $fillable = ['kode_memorial', 'kode', 'lawan', 'subtotal', 'keterangan', 'created_at', 'updated_at'];

    public function kodeRekening()
    {
        return $this->belongsTo('App\Models\Memorial', 'kode_memorial');
    }
}
