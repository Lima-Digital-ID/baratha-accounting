<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use \App\Models\Rekap_hotel;
use \App\Models\Jurnal;

class RekapHotelController extends Controller
{
    public function getRekap($tanggal)
    {
        $url = "http://127.0.0.1:8001/api/rekap-hotel/".$_GET['tanggal'];
        $json = json_decode(file_get_contents($url), true);
        return $json;
    }
    public function index()
    {
        try {
            $this->param['pageInfo'] = 'Input Rekap Hotel';
    
            if(isset($_GET['tanggal'])){
                $cek = Rekap_hotel::where('tanggal',$_GET['tanggal'])->count();
                if($cek==0){
                    $this->param['json'] = $this->getRekap($_GET['tanggal']);
                }
            }
            return view('penjualan.input-rekap.input-rekap-hotel', $this->param);
        } catch (\Throwable $th) {
            echo $th->getMessage();
        }
    }
    public function save()
    {
        try {
            $json = $this->getRekap($_GET['tanggal']);
            $rekapHotel = new Rekap_hotel;
            $rekapHotel->tanggal = $_GET['tanggal'];
            $rekapHotel->total = $json['total'];
            $rekapHotel->total_ppn = $json['total_ppn'];
            $rekapHotel->save();

            // save jurnal penjualan
            // semua penjualan hotel langsung masuk ke kas
            $newJurnal = new Jurnal;
            $newJurnal->tanggal = $_GET['tanggal'];
            $newJurnal->jenis_transaksi = 'Penjualan Hotel';
            $newJurnal->kode_transaksi = 'Penjualan Hotel';
            $newJurnal->keterangan = 'Penjualan Hotel';
            $newJurnal->kode = '1111.0001';
            $newJurnal->lawan = '4110.0001';
            $newJurnal->tipe = 'Debet';
            $newJurnal->nominal = $json['total'];
            $newJurnal->id_detail = '';
            $newJurnal->save();

            
            // save jurnal ppn penjualan
            // ppn penjualan hotel belum fix

            // $newJurnalPpn = new Jurnal;
            // $newJurnalPpn->tanggal = $_GET['tanggal'];
            // $newJurnalPpn->jenis_transaksi = 'Penjualan Resto';
            // $newJurnalPpn->kode_transaksi = 'Penjualan Resto';
            // $newJurnalPpn->keterangan = 'PPN Penjualan Resto';
            // $newJurnalPpn->kode = '1120.0001';
            // $newJurnalPpn->lawan = '2116.0001';
            // $newJurnalPpn->tipe = 'Debet';
            // $newJurnalPpn->nominal = $totalPpn;
            // $newJurnalPpn->id_detail = '';
            // $newJurnalPpn->save();

            return redirect()->back()->withStatus('Penarikan Data Berhasil');

        } catch (\Exception $e) {
            return redirect()->back()->withStatus('Terjadi kesalahan. : ' . $e->getMessage());
        } catch (\Illuminate\Database\QueryException $e) {
            return redirect()->back()->withStatus('Terjadi kesalahan pada database : ' . $e->getMessage());
        }
    }
}
