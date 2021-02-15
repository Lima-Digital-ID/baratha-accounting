<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class RenameTablePenjualanCatering extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::rename("penjualan_catering", "penjualan_lain");
        Schema::table('penjualan_lain', function (Blueprint $table) {
            $table->enum('tipe_penjualan',['catering','resto']);
        });
    }
    
    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::rename("penjualan_lain", "penjualan_catering");
        Schema::table('penjualan_lain', function (Blueprint $table) {
            $table->dropColumn('tipe_penjualan');
        });
    }
}
