<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Customer;
use App\Models\PenjualanLain;
use App\Models\KartuPiutang;
use App\Models\LogActivity;
use App\Models\Jurnal;
use Illuminate\Support\Facades\Auth;

class PiutangHotelController extends Controller
{
    public function getPiutang()
    {
        $json = '';
     
        $url = urlApiHotel()."list-piutang-hotel";
     
        $json = json_decode(file_get_contents($url));
        return json_encode($json);
    }

    public function getPiutangByKode($kodeInvoice)
    {
        $kodeInvoice = str_replace('/', '-', $kodeInvoice);

        $url = urlApiHotel()."piutang-hotel".'/'.$kodeInvoice;

        $json = file_get_contents($url);
        return $json;
    }

    public function index()
    {
        $this->param['pageInfo'] = 'Input Piutang Hotel';
        $this->param['customer'] = Customer::select('kode_customer','nama')->get();
        $piutang = $this->getPiutang();
        $piutang = json_decode($piutang, true);
        
        if($piutang['status'] != 'Kosong'){
            $this->param['piutang']['data'] = [];
            $this->param['piutang']['status'] = $piutang['status'];
            foreach ($piutang['data'] as $key => $value) {
                $cek = PenjualanLain::where('kode_penjualan',$value['kode_penjualan'])->count();
                if($cek==0){
                    $arr = array(
                        "kode_penjualan" => $value['kode_penjualan'],
                        "nama_customer" => $value['nama_customer'],
                        "total" => $value['total'],
                        "total_ppn" => $value['total_ppn'],
                        "waktu" => $value['waktu'],
                    );
                    array_push($this->param['piutang']['data'],$arr);
                }
            }
        }
        else {
            $this->param['piutang'] = $piutang;
        }

        return view('penjualan.piutang-resto.input-piutang-hotel', $this->param);
    }
    public function store(Request $request)
    {
        try {
            $piutang = json_decode($this->getPiutangByKode($request->kode_penjualan), true);
            $totalPpn = $piutang['data'][0]['total_ppn'];
            
            $piutang['tanggal'] = date('Y-m-d', strtotime($piutang['data'][0]['waktu']));

            $total = $piutang['data'][0]['total'];

            $grandtotal = $total + $totalPpn;

            $newPenjualan = new PenjualanLain;
            $newPenjualan->kode_penjualan = $request->get('kode_penjualan');
            $newPenjualan->kode_customer = $request->get('kode_customer');
            $newPenjualan->tanggal = date('Y-m-d', strtotime($piutang['data'][0]['waktu']));
            $newPenjualan->status_ppn = 'Belum';
            $newPenjualan->jatuh_tempo = "0000-00-00";
            $newPenjualan->qty = 1;
            $newPenjualan->harga_satuan = 0;
            $newPenjualan->keterangan = 'Piutang Hotel';
            $newPenjualan->total = $total;
            $newPenjualan->total_ppn = $totalPpn;
            $newPenjualan->grandtotal = $grandtotal;
            $newPenjualan->terbayar = 0;
            $newPenjualan->tipe_penjualan = 'hotel';
            $newPenjualan->created_by = Auth::user()->id;

            // insert log activity update
            $newActivity = new LogActivity;
            $newActivity->id_user = Auth::user()->id;
            $newActivity->jenis_transaksi = 'Piutang Hotel';
            $newActivity->tipe = 'Insert';
            $newActivity->keterangan = 'Input Piutang Hotel dengan kode '. $request->get('kode_penjualan') .' dengan grandtotal '. $grandtotal;
            $newActivity->save();


            $newPenjualan->save();

            // save jurnal penjualan
            $newJurnal = new Jurnal;
            $newJurnal->tanggal = date('Y-m-d', strtotime($piutang['data'][0]['waktu']));
            $newJurnal->jenis_transaksi = 'Penjualan Hotel';
            $newJurnal->kode_transaksi = $request->get('kode_penjualan');
            $newJurnal->keterangan = 'Penjualan Hotel';
            $newJurnal->kode = '1103'; //piutang
            $newJurnal->lawan = '4101'; //pendapatan
            $newJurnal->tipe = 'Debet';
            $newJurnal->nominal = $total;
            $newJurnal->id_detail = '';
            $newJurnal->save();

            // save jurnal ppn penjualan
            
            $newJurnal = new Jurnal;
            $newJurnal->tanggal = $piutang['tanggal'];
            $newJurnal->jenis_transaksi = 'Penjualan Hotel';
            $newJurnal->kode_transaksi = $request->get('kode_penjualan');
            $newJurnal->keterangan = 'PPN Penjualan Hotel';
            $newJurnal->kode = '1103'; //piutang
            $newJurnal->lawan = '2105'; //ppn masukan
            $newJurnal->tipe = 'Debet';
            $newJurnal->nominal = $totalPpn;
            $newJurnal->id_detail = '';
            $newJurnal->save();
 
            //update piutang customer
            Customer::where('kode_customer', $request->get('kode_customer'))
                            ->update([
                                'piutang' => \DB::raw('piutang+' . $grandtotal),
                            ]);
                            
            //insert ke kartu piutang
            $kartuPiutang = new KartuPiutang;
            $kartuPiutang->kode_customer = $request->get('kode_customer');
            $kartuPiutang->tipe = 'Penjualan';
            $kartuPiutang->kode_transaksi = $request->get('kode_penjualan');
            $kartuPiutang->nominal = $grandtotal;
            $kartuPiutang->tanggal = date('Y-m-d', strtotime($piutang['data'][0]['waktu']));
            $kartuPiutang->save();

            return redirect()->back()->withStatus('Data berhasil ditambahkan.');

        } catch (\Exception $e) {
            return redirect()->back()->withStatus('Terjadi kesalahan. : ' . $e->getMessage());
        } catch (\Illuminate\Database\QueryException $e) {
            return redirect()->back()->withStatus('Terjadi kesalahan pada database : ' . $e->getMessage());
        }
    }
}