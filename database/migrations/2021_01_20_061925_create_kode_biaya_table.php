<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateKodeBiayaTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('kode_biaya', function (Blueprint $table) {
            $table->string('kode_biaya', 15)->primary();
            $table->string('nama', 40);
            $table->string('kode_rekening', 15);
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
        Schema::dropIfExists('kode_biaya');
    }
}
