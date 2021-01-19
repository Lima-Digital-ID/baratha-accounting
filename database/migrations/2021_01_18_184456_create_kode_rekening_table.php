<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateKodeRekeningTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('kode_rekening', function (Blueprint $table) {
            $table->string('kode_rekening', 15)->primary();
            $table->string('nama', 40);
            $table->enum('tipe', ['Debet', 'Kredit']);
            $table->decimal('saldo_awal', 13, 2)->nullable()->default(0.00);
            $table->string('kode_induk', 15);
            $table->timestamps();

            $table->foreign('kode_induk')
                    ->references('kode_induk')
                    ->on('kode_induk');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('kode_rekening');
    }
}
