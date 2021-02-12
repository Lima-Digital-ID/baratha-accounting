<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDetailMemorialTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('detail_memorial', function (Blueprint $table) {
            $table->increments('id');
            $table->string('kode_memorial', 20);
            $table->text('keterangan');
            $table->string('kode', 15);
            $table->string('lawan', 15);
            $table->decimal('subtotal', 13,2);
            $table->timestamps();

            $table->foreign('kode_memorial')->references('kode_memorial')->on('memorial');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('detail_memorial');
    }
}
