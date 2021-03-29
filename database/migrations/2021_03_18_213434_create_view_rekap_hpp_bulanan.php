<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateViewRekapHppBulanan extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        \DB::statement("
            CREATE VIEW rekap_hpp_bulanan AS SELECT MONTH(tanggal) AS bulan, YEAR(tanggal) AS tahun, SUM(nominal_hpp) AS nominal FROM hpp GROUP BY MONTH(tanggal), YEAR(tanggal)
        ");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        \DB::statement("DROP VIEW rekap_hpp_bulanan");
    }
}
