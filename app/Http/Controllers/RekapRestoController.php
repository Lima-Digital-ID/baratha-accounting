<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use \App\Models\Rekap_resto;
use \App\Models\Jurnal;

class RekapRestoController extends Controller
{
    public function getRekap($tanggal)
    {
        $url = urlApiResto()."rekap-resto/".$_GET['tanggal'];
        $json = json_decode(file_get_contents($url), true);
        return $json;
    }
    public function index()
    {
        $this->param['pageInfo'] = 'Input Rekap Resto';

        if(isset($_GET['tanggal'])){
            $cek = Rekap_resto::where('tanggal',$_GET['tanggal'])->count();
            if($cek==0){
                $this->param['json'] = $this->getRekap($_GET['tanggal']);
            }
        }
        return view('penjualan.input-rekap.input-rekap-resto', $this->param);
    }
    public function save()
    {
        try {
            $json = $this->getRekap($_GET['tanggal']);
            $rekapHotel = new Rekap_resto;
            $rekapHotel->tanggal = $_GET['tanggal'];
            $rekapHotel->total = $json['data']['total'];
            $rekapHotel->total_ppn = $json['data']['total_ppn'];
            $rekapHotel->save();

            // save jurnal penjualan
            // semua penjualan resto langsung masuk ke kas
            $newJurnal = new Jurnal;
            $newJurnal->tanggal = $_GET['tanggal'];
            $newJurnal->jenis_transaksi = 'Penjualan Resto';
            $newJurnal->kode_transaksi = 'Penjualan Resto';
            $newJurnal->keterangan = 'Penjualan Resto';
            $newJurnal->kode = '1101';
            $newJurnal->lawan = '4101';
            $newJurnal->tipe = 'Debet';
            $newJurnal->nominal = $json['data']['total'];
            $newJurnal->id_detail = '';
            $newJurnal->save();

            
            // save jurnal ppn penjualan
            // ppn penjualan resto belum fix

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
