<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use \App\Models\Jurnal;
use \App\Models\Kas;
use \App\Models\DetailKas;
use \App\Models\KodeRekening;
use \App\Models\Supplier;
use \App\Models\Customer;
use \App\Models\KartuHutang;
use \App\Models\KartuPiutang;
use \App\Models\PembelianBarang;
use \App\Models\PenjualanLain;
use Illuminate\Support\Facades\DB;
use \App\Models\LogActivity;
use Illuminate\Support\Facades\Auth;

class KasController extends Controller
{
    private $param;
    public function __construct()
    {
        $this->param['icon'] = 'fa-wallet';
    }

    public function index(Request $request)
    {
        $this->param['pageInfo'] = 'Transaksi Kas / List Transaksi Kas';
        $this->param['btnRight']['text'] = 'Tambah Data';
        $this->param['btnRight']['link'] = route('transaksi-kas.create');

        try {
            $keyword = $request->get('keyword');
            if ($keyword) {
                $this->param['kas'] = Kas::where('kode_kas', 'LIKE', "%$keyword%")->orWhere('kode_supplier', 'LIKE', "%$keyword%")->orWhere('kode_customer', 'LIKE', "%$keyword%")->orderBy('tanggal', 'DESC')->paginate(10);
            } else {
                $this->param['kas'] = Kas::orderBy('tanggal', 'DESC')->paginate(10);
            }
        } catch (\Illuminate\Database\QueryException $e) {
            return redirect()->back()->withStatus('Terjadi Kesalahan');
        }

        return \view('kas.list-transaksi-kas', $this->param);
    }

    public function create()
    {
        try {
            $this->param['pageInfo'] = 'Transaksi Kas / Tambah Transaksi Kas';
            $this->param['btnRight']['text'] = 'Lihat Data';
            $this->param['btnRight']['link'] = route('transaksi-kas.index');
            $this->param['kodeRekeningKas'] = KodeRekening::select('kode_rekening', 'kode_rekening.nama')->join('kode_induk', 'kode_induk.kode_induk', '=', 'kode_rekening.kode_induk')->where('kode_rekening.nama', 'LIKE', 'Kas%')->get();

            $this->param['lawan'] = KodeRekening::select('kode_rekening', 'kode_rekening.nama')->join('kode_induk', 'kode_induk.kode_induk', '=', 'kode_rekening.kode_induk')->where('kode_rekening.nama', '!=','Kas')->where('kode_rekening.nama', '!=','Bank')->get();

            $this->param['supplier'] = Supplier::get();
            $this->param['customer'] = Customer::get();

        } catch (\Exception $e) {
            return redirect()->back()->withStatus('Terjadi kesalahan. : ' . $e->getMessage());
        }

        return \view('kas.tambah-transaksi-kas', $this->param);
    }

    public function getKode()
    {
        $tgl = explode('-',$_GET['tanggal']);
        $y = $tgl[0];
        $m = $tgl[1];
        $tipe = $_GET['tipe'];
        $lastKode = \DB::table('kas')
        ->select('kode_kas')
        ->where(\DB::raw('length(kode_kas)'), 13)
        ->whereMonth('tanggal', $m)
        ->whereYear('tanggal', $y)
        ->where('tipe', $tipe)
        ->orderBy('kode_kas','desc')
        ->skip(0)->take(1)
        ->get();
        $dateCreate = date_create($_GET['tanggal']);
        $date = date_format($dateCreate, 'my');
        if(count($lastKode)==0 && $tipe == 'Masuk'){
            $kode = "BKM-".$date."-0001";
        }
        elseif(count($lastKode)==0 && $tipe == 'Keluar'){
            $kode = "BKK-".$date."-0001";
        }
        else{
            $ex = explode('-', $lastKode[0]->kode_kas);
            $no = (int)$ex[2] + 1;
            $newNo = sprintf("%04s", $no);
            $kode = $ex[0].'-'.$date.'-'.$newNo;
        }
        return $kode;
    }

    public function addDetailTransaksiKas()
    {
        $next = $_GET['biggestNo'] + 1;
        $lawan = KodeRekening::select('kode_rekening', 'kode_rekening.nama')->join('kode_induk', 'kode_induk.kode_induk', '=', 'kode_rekening.kode_induk')->where('kode_rekening.nama', '!=','Kas')->where('kode_rekening.nama', '!=','Bank')->get();
        return view('kas.tambah-detail-transaksi-kas', ['hapus' => true, 'no' => $next, 'lawan' => $lawan]);
    }

    public function store(Request $request)
    {
        
        if(empty($_POST['bayar'])){
            $validate['lawan.*'] = 'required';
            $validate['subtotal.*'] = 'required|numeric|gt:0';
            $validate['keterangan.*'] = 'required';
            $loopTotal = $_POST['subtotal'];
        }
        else{
            $validate['bayar.*'] = 'required|numeric';
            $loopTotal = $_POST['bayar'];
        }
        $validatedData = $request->validate($validate);

        try {
            $total = 0;
            
            foreach ($loopTotal as $key => $value) {
                $total += $value;
            }

            $newKas = new Kas;
            $newKas->kode_kas = $request->get('kode_kas');
            $newKas->tanggal = $request->get('tanggal');
            $newKas->tipe = $request->get('tipe');
            $newKas->kode_rekening = $request->get('kode_rekening');
            $newKas->kode_supplier = $request->get('kode_supplier');
            $newKas->kode_customer = $request->get('kode_customer');
            $newKas->total = $total;
            $newKas->created_by = Auth::user()->id;
            $newKas->save();

            if(empty($_POST['bayar'])){
                foreach ($_POST['subtotal'] as $key => $value) {

                    $newDetail = new DetailKas;
                    $newDetail->kode_kas = $request->get('kode_kas');
                    $newDetail->keterangan = str_replace(' ', '-', strtoupper($_POST['keterangan'][$key]));
                    $newDetail->lawan = $_POST['lawan'][$key];
                    $newDetail->subtotal = $value;
    
                    $newDetail->save();
    
                    $newJurnal = new Jurnal;
                    $newJurnal->tanggal = $request->get('tanggal');
                    $newJurnal->jenis_transaksi = 'Kas';
                    $newJurnal->kode_transaksi = $request->get('kode_kas');
                    $newJurnal->keterangan = str_replace(' ', '-', strtoupper($_POST['keterangan'][$key]));
                    $newJurnal->kode = $request->get('kode_rekening');
                    $newJurnal->lawan = $_POST['lawan'][$key];
                    $newJurnal->tipe = $request->get('tipe') == 'Masuk' ? 'Debet' : 'Kredit';
                    $newJurnal->nominal = $_POST['subtotal'][$key];
                    $newJurnal->id_detail = $newDetail->id;
                    $newJurnal->save();
    
                }
    
            }
            else{
                if(isset($_POST['kode_supplier'])){
                    $lawan = '2101';
                    $keterangan = 'Pembayaran Hutang';

                    foreach ($_POST['bayar'] as $key => $value) {
                        if($value!=0){
                            //insert ke kartu hutang
                            $kartuHutang = new KartuHutang;
                            $kartuHutang->tanggal = date('Y-m-d');
                            $kartuHutang->kode_supplier = $request->get('kode_supplier');
                            // $kartuHutang->kode_transaksi = $_POST['kode_transaksi'][$key];
                            $kartuHutang->kode_transaksi = $request->get('kode_kas');
                            $kartuHutang->nominal = $value;
                            $kartuHutang->tipe = 'Pembayaran';
                            $kartuHutang->save();
    
                            //update terbayar pembelian barang
                            PembelianBarang::where('kode_pembelian', $_POST['kode_transaksi'][$key])
                            ->update([
                                'terbayar' => \DB::raw('terbayar+' . $value),
                                'updated_by' => Auth::user()->id
                            ]);
                            //update hutang Supplier
                            Supplier::where('kode_supplier', $request->get('kode_supplier'))
                            ->update([
                                'hutang' => \DB::raw('hutang-' . $value),
                            ]);
                        }

                    }                    
                }
                else{
                    $lawan = '1103';
                    $keterangan = 'Pembayaran Piutang';

                    foreach ($_POST['bayar'] as $key => $value) {
                        if($value!=0){
                            //insert ke kartu piutang
                            $kartuHutang = new KartuPiutang;
                            $kartuHutang->tanggal = date('Y-m-d');
                            $kartuHutang->kode_customer = $request->get('kode_customer');
                            // $kartuHutang->kode_transaksi = $_POST['kode_transaksi'][$key];
                            $kartuHutang->kode_transaksi = $request->get('kode_kas');
                            $kartuHutang->nominal = $value;
                            $kartuHutang->tipe = 'Pelunasan';
                            $kartuHutang->save();

                            //update terbayar penjualan barang
                            PenjualanLain::where('kode_penjualan', $_POST['kode_transaksi'][$key])
                            ->update([
                                'terbayar' => \DB::raw('terbayar+' . $value),
                                'updated_by' => Auth::user()->id
                            ]);

                            //update piutang customer
                            Customer::where('kode_customer', $request->get('kode_customer'))
                            ->update([
                                'piutang' => \DB::raw('piutang-' . $value),
                            ]);

                            $getTipe = PenjualanLain::select('tipe_penjualan')->where('kode_penjualan', $_POST['kode_transaksi'][$key])->get()[0];
                            if($getTipe->tipe_penjualan=='resto'){
                                $url = urlApiResto()."bayar-piutang";
                                $data = array("kode_transaksi" => $_POST['kode_transaksi'][$key]);
                                $options = array(
                                            "http"=> array(
                                                "method"=>"POST",
                                                "header"=>"Content-Type: application/x-www-form-urlencoded",
                                                "content"=>http_build_query($data)
                                            )
                                );
                                file_get_contents($url,false,stream_context_create($options));
                            }
                        }

                    }

                }

                $newDetail = new DetailKas;
                $newDetail->kode_kas = $request->get('kode_kas');
                $newDetail->keterangan = $keterangan;
                $newDetail->lawan = $lawan;
                $newDetail->subtotal = $total;

                $newDetail->save();

                $newJurnal = new Jurnal;
                $newJurnal->tanggal = $request->get('tanggal');
                $newJurnal->jenis_transaksi = 'Kas';
                $newJurnal->kode_transaksi = $request->get('kode_kas');
                $newJurnal->keterangan = $keterangan;
                $newJurnal->kode = $request->get('kode_rekening');
                $newJurnal->lawan = $lawan;
                $newJurnal->tipe = $request->get('tipe') == 'Masuk' ? 'Debet' : 'Kredit';
                $newJurnal->nominal = $total;
                $newJurnal->id_detail = $newDetail->id;
                $newJurnal->save();



            }


            return redirect()->route('transaksi-kas.index')->withStatus('Data berhasil ditambahkan.');
        } catch (\Exception $e) {
            return redirect()->back()->withStatus('Terjadi kesalahan. : ' . $e->getMessage());
        } catch (\Illuminate\Database\QueryException $e) {
            return redirect()->back()->withStatus('Terjadi kesalahan pada database : ' . $e->getMessage());
        }
    }

    public function edit($kode)
    {
        try {
            $this->param['pageInfo'] = 'Transaksi Kas / Edit Data';
            $this->param['btnRight']['text'] = 'Lihat Data';
            $this->param['btnRight']['link'] = route('transaksi-kas.index');
            $this->param['kodeRekeningKas'] = KodeRekening::select('kode_rekening', 'kode_rekening.nama')->join('kode_induk', 'kode_induk.kode_induk', '=', 'kode_rekening.kode_induk')->where('kode_rekening.nama', 'LIKE', 'Kas%')->get();

            $this->param['lawan'] = KodeRekening::select('kode_rekening', 'kode_rekening.nama')->join('kode_induk', 'kode_induk.kode_induk', '=', 'kode_rekening.kode_induk')->where('kode_rekening.nama', '!=','Kas')->where('kode_rekening.nama', '!=','Bank')->get();

            $this->param['supplier'] = Supplier::get();
            $this->param['customer'] = Customer::get();

            $this->param['kas'] = Kas::find($kode);
            $this->param['detailTransaksiKas'] = DetailKas::where('kode_kas', $kode)->get();
            if(isset($_GET['page'])){
                if($this->param['kas']->kode_supplier!=''){
                    $this->param['totalBayar'] = KartuHutang::select(DB::raw('sum(nominal) as total'))->where('kode_transaksi', $kode)->get()[0];
                    $this->param['hutang'] = PembelianBarang::select('kode_pembelian','grandtotal','terbayar','tanggal','jatuh_tempo')->where("kode_supplier",$this->param['kas']->kode_supplier)->whereRaw('terbayar != grandtotal')->get();
                }
                else{
                    $this->param['totalBayar'] = KartuPiutang::select(DB::raw('sum(nominal) as total'))->where('kode_transaksi', $kode)->get()[0];
                    $this->param['piutang'] = PenjualanLain::select('kode_penjualan','grandtotal','terbayar','tanggal','jatuh_tempo')->where("kode_customer",$this->param['kas']->kode_customer)->whereRaw('terbayar != grandtotal')->get();
                }
            }
            return \view('kas.edit-transaksi-kas', $this->param);
        } catch (\Exception $e) {
            return redirect()->back()->withStatus('Terjadi kesalahan : ' . $e->getMessage());
        } catch (\Illuminate\Database\QueryException $e) {
            return redirect()->back()->withStatus('Terjadi kesalahan pada database : ' . $e->getMessage());
        }
    }

    public function addEditDetailTransaksiKas()
    {
        $fields = array(
            'lawan' => 'lawan',
            'subtotal' => 'subtotal',
            'keterangan' => 'keterangan',
        );
        $next = $_GET['biggestNo'] + 1;
        $lawan = KodeRekening::select('kode_rekening', 'kode_rekening.nama')->join('kode_induk', 'kode_induk.kode_induk', '=', 'kode_rekening.kode_induk')->where('kode_rekening.nama', '!=','Kas')->where('kode_rekening.nama', '!=','Bank')->get();
        return view('kas.edit-detail-transaksi-kas', ['hapus' => true, 'no' => $next, 'lawan' => $lawan, 'fields' => $fields, 'idDetail' => '0']);
    }

    public function update(Request $request, $kode)
    {
        $validatedData = $request->validate([
            'kode_kas' => 'required',
            'tanggal' => 'required',
            'kode_rekening' => 'required',
            'lawan.*' => 'required',
            'subtotal.*' => 'required|numeric|gt:0',
            'keterangan.*' => 'required',
        ]);

        try {

            $transaksiKas = Kas::where('kode_kas', $kode)->get()[0];

            $bulanTransaksiKas = date('m-Y', strtotime($transaksiKas->tanggal));
            $editBulanTransaksiKas = date('m-Y', strtotime($request->get('tanggal')));

            if ($bulanTransaksiKas != $editBulanTransaksiKas) {
                return redirect()->back()->withStatus('Tidak dapat merubah bulan transaksi');
            }

            $tipe = $transaksiKas->tipe;
            // $grandtotal = $transaksiKas->grandtotal;
            // $kodeSupplier = $transaksiKas->kode_supplier;

            $newTotal = 0;

            foreach ($_POST['lawan'] as $key => $value) {
                // cek apakah penambahan detail baru atau tidak
                if ($_POST['id_detail'][$key] != 0) { // perubahan pada detail tanpa menambah detail baru
                    $getDetail = DetailKas::select('lawan', 'keterangan', 'subtotal')->where('id', $_POST['id_detail'][$key])->get()[0];

                    // cek apakah terdapat perubahan pada detail
                    if ($_POST['lawan'][$key] != $getDetail['lawan'] || str_replace(' ', '-', strtoupper($_POST['keterangan'][$key])) != $getDetail['keterangan'] || $_POST['subtotal'][$key] != $getDetail['subtotal']) { 
                        //update detail
                        DetailKas::where('id', $_POST['id_detail'][$key])
                        ->update([
                            'lawan' => $_POST['lawan'][$key],
                            'subtotal' => $_POST['subtotal'][$key],
                            'keterangan' => str_replace(' ', '-', strtoupper($_POST['keterangan'][$key])),
                        ]);

                    // update jurnal
                        Jurnal::where('id_detail', $_POST['id_detail'][$key])
                        ->where('kode_transaksi', $kode)
                        ->update([
                            'tanggal' => $_POST['tanggal'],
                            'keterangan' => str_replace(' ', '-', strtoupper($_POST['keterangan'][$key])),
                            'kode' => $request->get('kode_rekening'),
                            'lawan' => $_POST['lawan'][$key],
                            'nominal' => $_POST['subtotal'][$key],
                        ]);
                        
                    }
                    else{ //hanya mengupdate jurnal
                        // update jurnal
                        Jurnal::where('id_detail', $_POST['id_detail'][$key])
                        ->where('kode_transaksi', $kode)
                        ->update([
                            'tanggal' => $_POST['tanggal'],
                            'kode' => $request->get('kode_rekening'),
                        ]);
                    }
                    
                } 
                else { //perubahan pada detail dengan menambah detail baru

                    //insert to detail
                    $newDetail = DetailKas::create([
                            'kode_kas' => $_POST['kode_kas'],
                            'keterangan' => str_replace(' ', '-', strtoupper($_POST['keterangan'][$key])),
                            'lawan' => $_POST['lawan'][$key],
                            'subtotal' => $_POST['subtotal'][$key],
                        ]);
                    
                    // insert jurnal
                    Jurnal::insert([
                            'tanggal' => $_POST['tanggal'],
                            'jenis_transaksi' => 'Kas',
                            'kode_transaksi' => $_POST['kode_kas'],
                            'keterangan' => str_replace(' ', '-', strtoupper($_POST['keterangan'][$key])),
                            'kode' => $_POST['kode_rekening'],
                            'lawan' => $_POST['lawan'][$key],
                            'tipe' => $tipe == 'Masuk' ? 'Debet' : 'Kredit',
                            'nominal' => $_POST['subtotal'][$key],
                            'id_detail' => $newDetail->id
                        ]);
                }
                $newTotal = $newTotal + $_POST['subtotal'][$key];
            }

            if (isset($_POST['id_delete'])) {
                foreach ($_POST['id_delete'] as $key => $value) {

                    //delete detail
                    DetailKas::where('id', $value)->delete();

                    //delete jurnal
                    Jurnal::where('id_detail', $value)->where('kode_transaksi', $kode)->delete();
                }
            }

            //update kas
            Kas::where('kode_kas', $kode)
                ->update([
                    'tanggal' => $_POST['tanggal'],
                    'kode_rekening' => $_POST['kode_rekening'],
                    'kode_supplier' => $_POST['kode_supplier'],
                    'kode_customer' => $_POST['kode_customer'],
                    'total' => $newTotal,
                    'updated_by' => Auth::user()->id,
                ]);
            return redirect()->back()->withStatus('Data berhasil diperbarui.');
        } catch (\Exception $e) {
            return redirect()->back()->withStatus('Terjadi kesalahan. : ' . $e->getMessage());
        } catch (\Illuminate\Database\QueryException $e) {
            return redirect()->back()->withStatus('Terjadi kesalahan pada database : ' . $e->getMessage());
        }
    }

    public function destroy($kode)
    {
        try {
            $kas = Kas::findOrFail($kode);
            // delete detail
            DetailKas::where('kode_kas', $kode)->delete();
            
            // delete jurnal
            Jurnal::where('kode_transaksi', $kode)->delete();
            // delete kas
            // insert log activity delete
            $newActivity = new LogActivity;
            $newActivity->id_user = Auth::user()->id;
            $newActivity->jenis_transaksi = 'Kas';
            $newActivity->tipe = 'Delete';
            $newActivity->keterangan = 'Hapus Kas dengan kode '. $kode .' dengan total '. $kas->total;
            $newActivity->save();

            $kas->delete();
            //reset hutang/piutang
            //update hutang/piutang pada customer/supplier

            return redirect()->route('transaksi-kas.index')->withStatus('Data berhasil dihapus.');
        } catch (\Exception $e) {
            return redirect()->back()->withStatus('Terjadi kesalahan. : ' . $e->getMessage());
        } catch (\Illuminate\Database\QueryException $e) {
            return redirect()->back()->withStatus('Terjadi kesalahan pada database : ' . $e->getMessage());
        }
    }
    
    public function reportKas()
    {
        try{
            $this->param['kodeRekeningKas'] = KodeRekening::select('kode_rekening', 'kode_rekening.nama')->join('kode_induk', 'kode_induk.kode_induk', '=', 'kode_rekening.kode_induk')->where('kode_rekening.nama', 'LIKE', 'Kas%')->get();
            $this->param['report'] = null;

            return view('kas.laporan-kas', $this->param);
        }
        catch(\Exception $e){
            return redirect()->back()->withStatus('Terjadi kesalahan. : ' . $e->getMessage());
        }
        catch(\Illuminate\Database\QueryException $e){
            return redirect()->back()->withStatus('Terjadi kesalahan pada database : ' . $e->getMessage());
        }
    }

    public function getReport(Request $request)
    {
        $validatedData = $request->validate([
            'kode_perkiraan' => 'required',
            'start' => 'required',
            'end' => 'required'
        ]);
        try{
            $this->param['kodeRekeningKas'] = KodeRekening::select('kode_rekening', 'kode_rekening.nama')->join('kode_induk', 'kode_induk.kode_induk', '=', 'kode_rekening.kode_induk')->where('kode_rekening.nama', 'LIKE', 'Kas%')->get();
            $this->param['report'] = Kas::select(
                                        'kas.kode_kas',
                                        'kas.tanggal',
                                        'kas.kode_rekening',
                                        'kas.tipe',
                                        'kas.total',
                                        'detail.keterangan',
                                        'detail.lawan',
                                        'detail.subtotal'
                                    )
                                    ->join('detail_kas AS detail', 'detail.kode_kas', 'kas.kode_kas')
                                    ->where('kas.kode_rekening', $request->get('kode_perkiraan'))
                                    ->whereBetween('kas.tanggal', [$request->get('start'), $request->get('end')])
                                    ->get();
            return view('kas.laporan-kas', $this->param);
        }
        catch(\Exception $e){
            return redirect()->back()->withStatus('Terjadi kesalahan. : ' . $e->getMessage());
        }
        catch(\Illuminate\Database\QueryException $e){
            return redirect()->back()->withStatus('Terjadi kesalahan pada database : ' . $e->getMessage());
        }
    }

    public function printReport(Request $request)
    {
        $validatedData = $request->validate([
            'kode_perkiraan' => 'required',
            'start' => 'required',
            'end' => 'required'
        ]);
        try{
            $this->param['kodeRekeningKas'] = KodeRekening::select('kode_rekening', 'kode_rekening.nama')->join('kode_induk', 'kode_induk.kode_induk', '=', 'kode_rekening.kode_induk')->where('kode_rekening.nama', 'LIKE', 'Kas%')->get();
            $this->param['report'] = Kas::select(
                                        'kas.kode_kas',
                                        'kas.tanggal',
                                        'kas.kode_rekening',
                                        'kas.tipe',
                                        'kas.total',
                                        'detail.keterangan',
                                        'detail.lawan',
                                        'detail.subtotal'
                                    )
                                    ->join('detail_kas AS detail', 'detail.kode_kas', 'kas.kode_kas')
                                    ->where('kas.kode_rekening', $request->get('kode_perkiraan'))
                                    ->whereBetween('kas.tanggal', [$request->get('start'), $request->get('end')])
                                    ->get();
            return view('kas.print-laporan-kas', $this->param);
        }
        catch(\Exception $e){
            return redirect()->back()->withStatus('Terjadi kesalahan. : ' . $e->getMessage());
        }
        catch(\Illuminate\Database\QueryException $e){
            return redirect()->back()->withStatus('Terjadi kesalahan pada database : ' . $e->getMessage());
        }
    }
}
