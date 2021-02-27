<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLogActivityTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('log_activity', function (Blueprint $table) {
            $table->id();
            $table->tinyInteger('id_user');
            $table->enum('jenis_transaksi', ['Pembelian','Pemakaian','Penjualan Resto','Penjualan Hotel','Penjualan Catering','Kas','Bank','Memorial', 'Piutang Resto']);
            $table->enum('tipe', ['Insert', 'Update', 'Delete']);
            $table->text('keterangan');
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
        Schema::dropIfExists('log_activity');
    }
}
