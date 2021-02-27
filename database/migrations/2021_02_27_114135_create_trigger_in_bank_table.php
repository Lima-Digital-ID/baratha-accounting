<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTriggerInBankTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('bank', function (Blueprint $table) {
            $table->tinyInteger('created_by');
            $table->tinyInteger('updated_by')->nullable();
        });

        DB::unprepared("
        CREATE TRIGGER tr_insert_bank AFTER INSERT ON `bank` FOR EACH ROW
            BEGIN
                INSERT INTO log_activity (`id_user`, `jenis_transaksi`, `tipe`, `keterangan`, `created_at`)
                VALUES (NEW.created_by, 'Bank', 'Insert', CONCAT('Input Bank  dengan kode ', NEW.kode_bank, ' dengan total ', NEW.total), now());
            END
        ");

        DB::unprepared("
        CREATE TRIGGER tr_update_bank AFTER UPDATE ON `bank` FOR EACH ROW
            BEGIN
                INSERT INTO log_activity (`id_user`, `jenis_transaksi`, `tipe`, `keterangan`, `created_at`)
                VALUES (NEW.updated_by, 'Bank', 'Update', CONCAT('Update Bank dengan kode ', NEW.kode_bank, ' dengan total awal ', OLD.total, ' menjadi ', NEW.total), now());
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
        Schema::table('bank', function (Blueprint $table) {
            $table->dropColumn('created_by');
            $table->dropColumn('updated_by');
        });

        DB::unprepared('DROP TRIGGER `tr_insert_bank`');

        DB::unprepared('DROP TRIGGER `tr_update_bank`');
    }
}
