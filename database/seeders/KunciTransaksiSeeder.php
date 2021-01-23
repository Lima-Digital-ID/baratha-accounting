<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class KunciTransaksiSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $jenisTransaksi = ['Pembelian','Pemakaian','Penjualan','Kas','Bank','Memorial'];
        for($i = 0; $i < 6; $i++){
            $kunciTransaksi = new \App\Models\KunciTransaksi();
            $kunciTransaksi->jenis_transaksi = $jenisTransaksi[$i];
            $kunciTransaksi->tanggal_kunci = date('Y-m-d');
            $kunciTransaksi->save();
        }
    }
}
