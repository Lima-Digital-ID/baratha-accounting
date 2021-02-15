<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CustomJurnalTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        \DB::statement("ALTER TABLE jurnal MODIFY COLUMN jenis_transaksi ENUM('Pembelian','Pemakaian','Penjualan Resto','Penjualan Hotel','Penjualan Catering','Kas','Bank','Memorial')");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        \DB::statement("ALTER TABLE jurnal MODIFY COLUMN jenis_transaksi ENUM('Pembelian','Pemakaian','Penjualan Resto','Penjualan Hotel','Penjualan lain-lain','Kas','Bank','Memorial')");
    }
}
