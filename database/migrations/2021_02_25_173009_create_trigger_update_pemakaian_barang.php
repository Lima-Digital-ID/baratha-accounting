<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTriggerUpdatePemakaianBarang extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::unprepared("
        CREATE TRIGGER tr_update_pemakaian_barang AFTER UPDATE ON `pemakaian_barang` FOR EACH ROW
            BEGIN
                INSERT INTO log_activity (`id_user`, `jenis_transaksi`, `tipe`, `keterangan`, `created_at`)
                VALUES (NEW.updated_by, 'Pemakaian', 'Update', CONCAT('Update Pemakaian Barang dengan kode ', NEW.kode_pemakaian, ' dengan total quantity awal ', OLD.total_qty, ' menjadi ', NEW.total_qty), now());
            END
        ");
    }

    public function down()
    {
        DB::unprepared('DROP TRIGGER `tr_update_pemakaian_barang`');
    }
}
