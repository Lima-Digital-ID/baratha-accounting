<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateViewEkuitas extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        \DB::statement("
        CREATE VIEW view_ekuitas AS SELECT MONTH(tanggal) AS bulan, YEAR(tanggal) AS tahun, SUM(nominal) AS nominal, kode,lawan,tipe FROM jurnal WHERE kode LIKE '3%' OR lawan LIKE '3%' GROUP BY MONTH(tanggal), YEAR(tanggal), kode,lawan,tipe ORDER BY MONTH(tanggal),YEAR(tanggal)
        ");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        \DB::statement("DROP VIEW view_ekuitas");
    }
}
