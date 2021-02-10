<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePenjualanCateringTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('penjualan_catering', function (Blueprint $table) {
            $table->string('kode_penjualan', 25)->primary();
            $table->string('kode_customer', 15);
            $table->date('tanggal');
            $table->enum('status_ppn', ['Tanpa', 'Belum', 'Sudah']);
            $table->date('jatuh_tempo')->nullable();
            $table->decimal('qty', 11,2);
            $table->decimal('harga_satuan', 13,2);
            $table->text('keterangan')->nullable();
            $table->decimal('total', 13,2);
            $table->decimal('total_ppn', 13,2)->default(0);
            $table->decimal('grandtotal', 13,2);
            $table->decimal('terbayar', 13,2)->default(0);
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
        Schema::dropIfExists('penjualan_catering');
    }
}
