<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTriggerUpdatePembelianBarang extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::unprepared("
        CREATE TRIGGER tr_update_pembelian_barang AFTER UPDATE ON `pembelian_barang` FOR EACH ROW
            BEGIN
                INSERT INTO log_activity (`id_user`, `jenis_transaksi`, `tipe`, `keterangan`, `created_at`)
                VALUES (NEW.updated_by, 'Pembelian', 'Update', CONCAT('Update Pembelian Barang dengan kode ', NEW.kode_pembelian, ' dengan grandtotal awal ', OLD.grandtotal, ' menjadi ', NEW.grandtotal), now());
            END
        ");
    }

    public function down()
    {
        DB::unprepared('DROP TRIGGER `tr_update_pembelian_barang`');
    }
}
