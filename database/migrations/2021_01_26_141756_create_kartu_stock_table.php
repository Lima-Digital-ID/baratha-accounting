<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateKartuStockTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('kartu_stock', function (Blueprint $table) {
            $table->id();
            $table->date('tanggal');
            $table->string('kode_barang', 20);
            $table->string('kode_transaksi', 25);
            $table->integer('id_detail');
            $table->decimal('qty', 12,2);
            $table->decimal('nominal', 13,2);
            $table->enum('tipe', ['Masuk', 'Keluar']);
            $table->timestamps();

            $table->foreign('kode_barang')->references('kode_barang')->on('barang');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('kartu_stock');
    }
}
