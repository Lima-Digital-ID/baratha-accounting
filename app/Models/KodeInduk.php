<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KodeInduk extends Model
{
    use HasFactory;

    protected $table = 'kode_induk';
    protected $primaryKey = 'kode_induk';
    public $incrementing = false;

    public function kodeRekening()
    {
        return $this->hasMany('App\Models\KodeRekening');
    }
}
