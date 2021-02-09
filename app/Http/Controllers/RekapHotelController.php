<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use \App\Models\Rekap_hotel;

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
            return redirect()->back()->withStatus('Penarikan Data Berhasil');

        } catch (\Exception $e) {
            return redirect()->back()->withStatus('Terjadi kesalahan. : ' . $e->getMessage());
        } catch (\Illuminate\Database\QueryException $e) {
            return redirect()->back()->withStatus('Terjadi kesalahan pada database : ' . $e->getMessage());
        }
    }
}
