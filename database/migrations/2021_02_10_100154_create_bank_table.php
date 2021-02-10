<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBankTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('bank', function (Blueprint $table) {
            $table->string('kode_bank', 20)->primary();
            $table->date('tanggal');
            $table->string('kode_rekening', 15);
            $table->enum('tipe', ['Masuk', 'Keluar']);
            $table->string('kode_supplier', 15)->nullable();
            $table->string('kode_customer', 15)->nullable();
            $table->decimal('total', 13,2);
            $table->timestamps();

            $table->foreign('kode_rekening')->references('kode_rekening')->on('kode_rekening');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('bank');
    }
}
