<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTriggerInMemorialTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('memorial', function (Blueprint $table) {
            $table->tinyInteger('created_by');
            $table->tinyInteger('updated_by')->nullable();
        });

        DB::unprepared("
        CREATE TRIGGER tr_insert_memorial AFTER INSERT ON `memorial` FOR EACH ROW
            BEGIN
                INSERT INTO log_activity (`id_user`, `jenis_transaksi`, `tipe`, `keterangan`, `created_at`)
                VALUES (NEW.created_by, 'Memorial', 'Insert', CONCAT('Input Memorial  dengan kode ', NEW.kode_memorial, ' dengan total ', NEW.total), now());
            END
        ");

        DB::unprepared("
        CREATE TRIGGER tr_update_memorial AFTER UPDATE ON `memorial` FOR EACH ROW
            BEGIN
                INSERT INTO log_activity (`id_user`, `jenis_transaksi`, `tipe`, `keterangan`, `created_at`)
                VALUES (NEW.updated_by, 'Memorial', 'Update', CONCAT('Update Memorial dengan kode ', NEW.kode_memorial, ' dengan total awal ', OLD.total, ' menjadi ', NEW.total), now());
            END
        ");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('memorial', function (Blueprint $table) {
            $table->dropColumn('created_by');
            $table->dropColumn('updated_by');
        });

        DB::unprepared('DROP TRIGGER `tr_insert_memorial`');

        DB::unprepared('DROP TRIGGER `tr_update_memorial`');
    }
}
