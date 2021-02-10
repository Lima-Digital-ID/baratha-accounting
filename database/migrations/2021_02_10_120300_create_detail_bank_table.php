<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDetailBankTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('detail_bank', function (Blueprint $table) {
            $table->increments('id');
            $table->string('kode_bank', 20);
            $table->text('keterangan');
            $table->string('lawan', 15);
            $table->decimal('subtotal', 13, 2);
            $table->timestamps();

            $table->foreign('kode_bank')->references('kode_bank')->on('bank');
            $table->foreign('lawan')->references('kode_rekening')->on('kode_rekening');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('detail_bank');
    }
}
