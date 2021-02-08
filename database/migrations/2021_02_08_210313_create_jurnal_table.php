<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateJurnalTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('jurnal', function (Blueprint $table) {
            $table->increments('id');
            $table->date('tanggal');
            $table->enum('jenis_transaksi', ['Pembelian', 'Pemakaian', 'Penjualan Resto', 'Penjualan Hotel', 'Penjualan lain-lain', 'Kas', 'Bank', 'Memorial']);
            $table->string('kode_transaksi', 25);
            $table->text('keterangan', 25);
            $table->string('kode', 15);
            $table->string('lawan', 15);
            $table->enum('tipe', ['Debet', 'Kredit']);
            $table->decimal('nominal', 13, 2);
            $table->integer('id_detail')->nullable();
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
        Schema::dropIfExists('jurnal');
    }
}
