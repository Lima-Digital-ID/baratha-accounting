<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use \App\Models\Rekap_hotel;
use \App\Models\Jurnal;
use App\Models\KodeRekening;

class RekapHotelController extends Controller
{
    public function getRekap($tanggal)
    {
        $url = urlApiHotel()."rekap-hotel/".$_GET['tanggal'];
        $json = json_decode(file_get_contents($url), true);
        return $json;
    }
    public function index()
    {
        try {
            $this->param['pageInfo'] = 'Input Rekap Hotel';
            $this->param['kodeRekening'] = KodeRekening::select('kode_rekening', 'kode_rekening.nama')->join('kode_induk', 'kode_induk.kode_induk', '=', 'kode_rekening.kode_induk')->where('kode_rekening.nama', 'LIKE', 'Kas%')->orWhere('kode_rekening.nama', 'LIKE', 'Bank%')->get();
            if(isset($_GET['tanggal'])){
                $this->param['json'] = $this->getRekap($_GET['tanggal']);
                // $cek = Rekap_hotel::where('tanggal',$_GET['tanggal'])->count();
                // if($cek==0){
                // }
                // elseif ($cek > 0) {
                //     $this->param['status'] = 'Data pada tanggal ' . $_GET['tanggal'] . ' telah ditarik.';
                //     $this->param['json'] = $this->getRekap($_GET['tanggal']);
                // }
            }
            return view('penjualan.input-rekap.input-rekap-hotel', $this->param);
        } catch (\Throwable $th) {
            echo $th->getMessage();
        }
    }
    public function save()
    {
        try {
            $rekapHotel = new Rekap_hotel;
            $rekapHotel->tanggal = $_GET['tanggal'];
            $rekapHotel->jenis_bayar = $_GET['jenis_bayar'];
            $rekapHotel->total = $_GET['total'];
            $rekapHotel->total_ppn = $_GET['total_ppn'];
            $rekapHotel->save();

            // save jurnal penjualan
            // semua penjualan hotel langsung masuk ke kas
            $newJurnal = new Jurnal;
            $newJurnal->tanggal = $_GET['tanggal'];
            $newJurnal->jenis_transaksi = 'Penjualan Hotel';
            $newJurnal->kode_transaksi = 'Penjualan Hotel';
            $newJurnal->keterangan = 'Penjualan Hotel';
            $newJurnal->kode = $_GET['kode_rekening'];
            $newJurnal->lawan = '4101';
            $newJurnal->tipe = 'Debet';
            $newJurnal->nominal = $_GET['total'];
            $newJurnal->id_detail = '';
            $newJurnal->save();

            
            // save jurnal ppn penjualan

            $newJurnalPpn = new Jurnal;
            $newJurnalPpn->tanggal = $_GET['tanggal'];
            $newJurnalPpn->jenis_transaksi = 'Penjualan Hotel';
            $newJurnalPpn->kode_transaksi = 'Penjualan Hotel';
            $newJurnalPpn->keterangan = 'PPN Penjualan Hotel';
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
