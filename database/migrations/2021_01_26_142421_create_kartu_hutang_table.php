<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateKartuHutangTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('kartu_hutang', function (Blueprint $table) {
            $table->id();
            $table->date('tanggal');
            $table->string('kode_supplier', 15);
            $table->string('kode_transaksi', 25);
            $table->integer('id_detail');
            $table->decimal('nominal', 13, 2);
            $table->enum('tipe', ['Pembelian', 'Pembayaran']);
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
        Schema::dropIfExists('kartu_hutang');
    }
}
