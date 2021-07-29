<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use \App\Models\Rekap_resto;
use \App\Models\Jurnal;
use App\Models\KodeRekening;

class RekapRestoController extends Controller
{
    public function getRekap($tanggal)
    {
        $url = urlApiResto()."rekap-resto/".$tanggal;
        $json = json_decode(file_get_contents($url), true);
        
        return $json;
    }
    public function index()
    {
        $this->param['pageInfo'] = 'Input Rekap Resto';
        $this->param['kodeRekening'] = KodeRekening::select('kode_rekening', 'kode_rekening.nama')->join('kode_induk', 'kode_induk.kode_induk', '=', 'kode_rekening.kode_induk')->where('kode_rekening.nama', 'LIKE', 'Kas%')->orWhere('kode_rekening.nama', 'LIKE', 'Bank%')->get();

        if(isset($_GET['tanggal'])){
            $this->param['json'] = $this->getRekap($_GET['tanggal']);
            // $cek = Rekap_resto::where('tanggal',$_GET['tanggal'])->count();
            // if($cek==0){
            // }
            // else{
            //     $this->param['status'] = 'Data pada tanggal ' . $_GET['tanggal'] . ' telah ditarik.';
            //     $this->param['json'] = $this->getRekap($_GET['tanggal']);
            // }
        }
        
        return view('penjualan.input-rekap.input-rekap-resto', $this->param);
    }
    public function save()
    {
        try {
            // $json = $this->getRekap($_GET['tanggal']);
            $rekapResto = new Rekap_resto;
            $rekapResto->tanggal = $_GET['tanggal'];
            $rekapResto->jenis_bayar = $_GET['jenis_bayar'];
            $rekapResto->total = $_GET['total'];
            $rekapResto->total_ppn = $_GET['total_ppn'];
            $rekapResto->save();

            // save jurnal penjualan
            // semua penjualan resto langsung masuk ke kas
            $newJurnal = new Jurnal;
            $newJurnal->tanggal = $_GET['tanggal'];
            $newJurnal->jenis_transaksi = 'Penjualan Resto';
            $newJurnal->kode_transaksi = 'Penjualan Resto';
            $newJurnal->keterangan = 'Penjualan Resto';
            $newJurnal->kode = $_GET['kode_rekening'];
            $newJurnal->lawan = '4101';
            $newJurnal->tipe = 'Debet';
            $newJurnal->nominal = $_GET['total'];
            $newJurnal->id_detail = '';
            $newJurnal->save();

            
            // save jurnal ppn penjualan

            $newJurnalPpn = new Jurnal;
            $newJurnalPpn->tanggal = $_GET['tanggal'];
            $newJurnalPpn->jenis_transaksi = 'Penjualan Resto';
            $newJurnalPpn->kode_transaksi = 'Penjualan Resto';
            $newJurnalPpn->keterangan = 'PPN Penjualan Resto';
            $newJurnalPpn->kode = $_GET['kode_rekening'];
            $newJurnalPpn->lawan = '2105';
            $newJurnalPpn->tipe = 'Debet';
            $newJurnalPpn->nominal = $_GET['total_ppn'];
            $newJurnalPpn->id_detail = '';
            $newJurnalPpn->save();
            
            return redirect()->back()->withStatus('Penarikan Data Berhasil');

        } catch (\Exception $e) {
            return redirect()->back()->withStatus('Terjadi kesalahan. : ' . $e->getMessage());
        } catch (\Illuminate\Database\QueryException $e) {
            return redirect()->back()->withStatus('Terjadi kesalahan pada database : ' . $e->getMessage());
        }
    }
}
