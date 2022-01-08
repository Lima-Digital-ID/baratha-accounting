<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateTipePenjuanTablePenjualanLain extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('penjualan_lain', function (Blueprint $table) {
            \DB::statement("ALTER TABLE `penjualan_lain` CHANGE `tipe_penjualan` `tipe_penjualan` ENUM('catering','resto','hotel') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('penjualan_lain', function (Blueprint $table) {
            //
        });
    }
}
