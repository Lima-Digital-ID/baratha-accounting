<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTriggerInsertPembelianBarang extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::unprepared("
        CREATE TRIGGER tr_insert_pembelian_barang AFTER INSERT ON `pembelian_barang` FOR EACH ROW
            BEGIN
                INSERT INTO log_activity (`id_user`, `jenis_transaksi`, `tipe`, `keterangan`, `created_at`)
                VALUES (NEW.created_by, 'Pembelian', 'Insert', CONCAT('Input Pembelian Barang dengan kode ', NEW.kode_pembelian, ' dengan grandtotal ', NEW.grandtotal), now());
            END
        ");
    }

    public function down()
    {
        DB::unprepared('DROP TRIGGER `tr_insert_pembelian_barang`');
    }
}
