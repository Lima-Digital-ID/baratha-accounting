<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMemorialTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('memorial', function (Blueprint $table) {
            $table->string('kode_memorial', 20)->primary();
            $table->date('tanggal');
            $table->enum('tipe', ['Masuk', 'Keluar']);
            $table->string('kode_supplier', 15)->nullable();
            $table->string('kode_customer', 15)->nullable();
            $table->decimal('total', 13,2);
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
        Schema::dropIfExists('memorial');
    }
}
