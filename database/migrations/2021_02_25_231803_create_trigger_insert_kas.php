<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTriggerInsertKas extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::unprepared("
        CREATE TRIGGER tr_insert_kas AFTER INSERT ON `kas` FOR EACH ROW
            BEGIN
                INSERT INTO log_activity (`id_user`, `jenis_transaksi`, `tipe`, `keterangan`, `created_at`)
                VALUES (NEW.created_by, 'Kas', 'Insert', CONCAT('Input Kas  dengan kode ', NEW.kode_kas, ' dengan total ', NEW.total), now());
            END
        ");

        DB::unprepared("
        CREATE TRIGGER tr_update_kas AFTER UPDATE ON `kas` FOR EACH ROW
            BEGIN
                INSERT INTO log_activity (`id_user`, `jenis_transaksi`, `tipe`, `keterangan`, `created_at`)
                VALUES (NEW.updated_by, 'Kas', 'Update', CONCAT('Update Kas dengan kode ', NEW.kode_kas, ' dengan total awal ', OLD.total, ' menjadi ', NEW.total), now());
            END
        ");
    }

    public function down()
    {
        DB::unprepared('DROP TRIGGER `tr_insert_kas`');

        DB::unprepared('DROP TRIGGER `tr_update_kas`');
    }
}
