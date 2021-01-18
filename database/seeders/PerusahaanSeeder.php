<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class PerusahaanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $perusahaan = new \App\Models\Perusahaan;
        $perusahaan->nama = 'Baratha Hotel And Coffee';
        $perusahaan->alamat = 'Jl. Saliwiryo Pranowo Gg. Taman No.11, Pattian, Kotakulon, Kec. Bondowoso';
        $perusahaan->kota = 'Bondowoso';
        $perusahaan->provinsi = 'Jawa Timur';
        $perusahaan->telepon = '0332-';
        $perusahaan->email = 'barathahotel@baratha.com';
        $perusahaan->save();
    }
}
