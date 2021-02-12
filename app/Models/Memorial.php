<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Memorial extends Model
{
    use HasFactory;

    protected $table = 'memorial';

    protected $primaryKey = 'kode_memorial';
    public $incrementing = false;

    public function detailMemorial()
    {
        return $this->hasMany('App\Models\DetailMemorial');
    }
}
