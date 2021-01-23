<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePembelianBarangTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pembelian_barang', function (Blueprint $table) {
            $table->string('kode_pembelian', 25)->primary();
            $table->string('kode_supplier', 15);
            $table->date('tanggal');
            $table->enum('status_ppn', ['Tanpa', 'Belum', 'Sudah'])->default('Tanpa');
            $table->date('jatuh_tempo')->nullable();
            $table->decimal('total_qty', 12, 2);
            $table->decimal('total', 13, 2);
            $table->decimal('total_ppn', 13, 2)->nullable()->default(0);
            $table->decimal('grandtotal', 13, 2);
            $table->decimal('terbayar', 13, 2)->nullable()->default(0);
            $table->timestamps();

            $table->foreign('kode_supplier')->references('kode_supplier')->on('supplier');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('pembelian_barang');
    }
}
