<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTriggerUpdateRekapHotel extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::unprepared("
        CREATE TRIGGER tr_update_rekap_hotel AFTER UPDATE ON `rekap_hotel` FOR EACH ROW
            BEGIN
                INSERT INTO log_activity (`id_user`, `jenis_transaksi`, `tipe`, `keterangan`, `created_at`)
                VALUES (NEW.updated_by, 'Penjualan Hotel', 'Update', 'Update Rekap Transaksi Hotel', now());
            END
        ");
    }

    public function down()
    {
        DB::unprepared('DROP TRIGGER `tr_update_rekap_hotel`');
    }
}
