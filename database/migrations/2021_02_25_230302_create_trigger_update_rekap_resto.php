<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTriggerUpdateRekapResto extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::unprepared("
        CREATE TRIGGER tr_update_rekap_resto AFTER UPDATE ON `rekap_resto` FOR EACH ROW
            BEGIN
                INSERT INTO log_activity (`id_user`, `jenis_transaksi`, `tipe`, `keterangan`, `created_at`)
                VALUES (NEW.updated_by, 'Penjualan Resto', 'Update', 'Update Rekap Penjualan Resto', now());
            END
        ");
    }

    public function down()
    {
        DB::unprepared('DROP TRIGGER `tr_update_rekap_resto`');
    }
}
