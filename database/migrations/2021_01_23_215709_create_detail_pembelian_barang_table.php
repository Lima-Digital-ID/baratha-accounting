<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDetailPembelianBarangTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('detail_pembelian_barang', function (Blueprint $table) {
            $table->increments('id');
            $table->string('kode_pembelian', 25);
            $table->string('kode_barang', 20);
            $table->decimal('qty', 12, 2);
            $table->decimal('subtotal', 13, 2);
            $table->timestamps();

            $table->foreign('kode_pembelian')->references('kode_pembelian')->on('pembelian_barang');
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
        Schema::dropIfExists('detail_pembelian_barang');
    }
}
