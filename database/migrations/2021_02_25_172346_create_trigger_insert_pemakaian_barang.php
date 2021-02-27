<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTriggerInsertPemakaianBarang extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::unprepared("
        CREATE TRIGGER tr_insert_pemakaian_barang AFTER INSERT ON `pemakaian_barang` FOR EACH ROW
            BEGIN
                INSERT INTO log_activity (`id_user`, `jenis_transaksi`, `tipe`, `keterangan`, `created_at`)
                VALUES (NEW.created_by, 'Pemakaian', 'Insert', CONCAT('Input Pemakaian Barang dengan kode ', NEW.kode_pemakaian, ' dengan total quantity ', NEW.total_qty), now());
            END
        ");
    }

    public function down()
    {
        DB::unprepared('DROP TRIGGER `tr_insert_pemakaian_barang`');
    }
}
