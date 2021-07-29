<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddJenisBayarToRekapRestoAndHotel extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('rekap_resto', function (Blueprint $table) {
            $table->string('jenis_bayar')->after('tanggal');
        });
        
        Schema::table('rekap_hotel', function (Blueprint $table) {
            $table->string('jenis_bayar')->after('tanggal');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('rekap_resto', function (Blueprint $table) {
            $table->dropColumn('jenis_bayar');
        });
        Schema::table('rekap_hotel', function (Blueprint $table) {
            $table->dropColumn('jenis_bayar');
        });
    }
}
