<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateKunciTransaksiTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('kunci_transaksi', function (Blueprint $table) {
            $table->tinyIncrements('id');
            $table->enum('jenis_transaksi', ['Pembelian', 'Pemakaian', 'Penjualan', 'Kas', 'Bank', 'Memorial']);
            $table->date('tanggal_kunci');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('kunci_transaksi');
    }
}
