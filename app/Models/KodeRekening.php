<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KodeRekening extends Model
{
    use HasFactory;

    protected $table = 'kode_rekening';
    protected $primaryKey = 'kode_rekening';
    public $incrementing = false;

    public function kodeInduk()
    {
        return $this->belongsTo('App\Models\KodeInduk', 'kode_induk');
    }

    public function kodeBiaya()
    {
        return $this->hasMany('App\Models\KodeBiaya');
    }
    
    public function kas()
    {
        return $this->hasMany('App\Models\Kas');
    }
}
