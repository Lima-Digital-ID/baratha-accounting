<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDetailPemakaianBarangTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('detail_pemakaian_barang', function (Blueprint $table) {
            $table->increments('id');
            $table->string('kode_pemakaian', 25);
            $table->string('kode_barang', 25);
            $table->decimal('qty', 12, 2);
            $table->decimal('subtotal', 13, 2);
            $table->string('kode_biaya', 15);
            $table->text('keterangan')->nullable();
            $table->timestamps();

            $table->foreign('kode_pemakaian')->references('kode_pemakaian')->on('pemakaian_barang');
            $table->foreign('kode_barang')->references('kode_barang')->on('barang');
            $table->foreign('kode_biaya')->references('kode_biaya')->on('kode_biaya');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('detail_pemakaian_barang');
    }
}
