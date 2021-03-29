<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateViewLabaRugi extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        \DB::statement("
        CREATE VIEW view_laba_rugi AS SELECT MONTH(tanggal) AS bulan, YEAR(tanggal) AS tahun, SUM(nominal) AS nominal, kode,lawan,tipe FROM jurnal WHERE kode LIKE '4%' OR kode LIKE '5%' OR kode LIKE '6%' OR lawan LIKE '4%' OR lawan LIKE '5%' OR lawan LIKE '6%' GROUP BY MONTH(tanggal), YEAR(tanggal), kode,lawan,tipe ORDER BY MONTH(tanggal),YEAR(tanggal)
        ");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        \DB::statement("DROP VIEW view_laba_rugi");
    }
}
