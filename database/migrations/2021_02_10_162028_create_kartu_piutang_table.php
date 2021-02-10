<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateKartuPiutangTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('kartu_piutang', function (Blueprint $table) {
            $table->increments('id');
            $table->string('kode_customer', 15);
            $table->enum('tipe', ['Penjualan', 'Pelunasan']);
            $table->string('kode_transaksi', 25);
            $table->decimal('nominal', 13,2);
            $table->date('tanggal');
            $table->timestamps();

            $table->foreign('kode_customer')->references('kode_customer')->on('customer');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('kartu_piutang');
    }
}
