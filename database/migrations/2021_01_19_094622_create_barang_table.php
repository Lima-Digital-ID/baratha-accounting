<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBarangTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('barang', function (Blueprint $table) {
            $table->string('kode_barang', 20)->primary();
            $table->string('nama', 40);
            $table->string('satuan', 20);
            $table->decimal('stock_awal', 12, 2)->nullable()->default(0);
            $table->decimal('saldo_awal', 13, 2)->nullable()->default(0);
            $table->decimal('stock', 12, 2)->nullable()->default(0);
            $table->decimal('saldo', 13, 2)->nullable()->default(0);
            $table->date('exp_date')->nullable();
            $table->text('keterangan')->nullable();
            $table->text('tempat_penyimpanan')->nullable();
            $table->decimal('minimum_stock', 12, 2)->nullable()->default(0);
            $table->tinyInteger('id_kategori')->unsigned()->nullable();
            $table->timestamps();

            $table->foreign('id_kategori')->references('id')->on('kategori_barang');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('barang');
    }
}
