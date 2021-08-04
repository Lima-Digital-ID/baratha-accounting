<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use \App\Models\Jurnal;
use \App\Models\Memorial;
use \App\Models\DetailMemorial;
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

class MemorialController extends Controller
{
    private $param;
    // public function __construct()
    // {
    //     $this->param['icon'] = 'fa-credit-card';
    // }

    public function index(Request $request)
    {
        $this->param['pageInfo'] = 'Memorial / List Memorial';
        $this->param['btnRight']['text'] = 'Tambah Data';
        $this->param['btnRight']['link'] = route('memorial.create');

        try {
            $keyword = $request->get('keyword');
            $getMemorial = Memorial::orderBy('tanggal', 'DESC');

            if ($keyword) {
                $getMemorial->where('kode_memorial', 'LIKE', "%$keyword%")->orWhere('kode_supplier', 'LIKE', "%$keyword%")->orWhere('kode_customer', 'LIKE', "%$keyword%");
            }
            
            $this->param['memorial'] = $getMemorial->paginate(10);

        } catch (\Illuminate\Database\QueryException $e) {
            return redirect()->route('memorial.index')->withStatus('Terjadi Kesalahan');
            echo $e->getMessage();
        }

        return \view('memorial.list-memorial', $this->param);
    }

    public function create()
    {
        try {
            $this->param['pageInfo'] = 'Memorial / Tambah Memorial';
            $this->param['btnRight']['text'] = 'Lihat Data';
            $this->param['btnRight']['link'] = route('memorial.index');
            $this->param['kodeRekening'] = KodeRekening::select('kode_rekening', 'kode_rekening.nama')->get();

            $this->param['lawan'] = KodeRekening::select('kode_rekening', 'kode_rekening.nama')->get();

            $this->param['supplier'] = Supplier::get();
            $this->param['customer'] = Customer::get();

        } catch (\Exception $e) {
            return redirect()->back()->withStatus('Terjadi kesalahan. : ' . $e->getMessage());
        }

        return \view('memorial.tambah-memorial', $this->param);
    }

    public function getKode()
    {
        $tgl = explode('-',$_GET['tanggal']);
        $y = $tgl[0];
        $m = $tgl[1];
        $tipe = $_GET['tipe'];
        $lastKode = \DB::table('memorial')
        ->select('kode_memorial')
        ->where(\DB::raw('length(kode_memorial)'), 13)
        ->whereMonth('tanggal', $m)
        ->whereYear('tanggal', $y)
        ->where('tipe', $tipe)
        ->orderBy('kode_memorial','desc')
        ->skip(0)->take(1)
        ->get();
        $dateCreate = date_create($_GET['tanggal']);
        $date = date_format($dateCreate, 'my');
        if(count($lastKode)==0 && $tipe == 'Masuk'){
            $kode = "BMM-".$date."-0001";
        }
        elseif(count($lastKode)==0 && $tipe == 'Keluar'){
            $kode = "BMK-".$date."-0001";
        }
        else{
            $ex = explode('-', $lastKode[0]->kode_memorial);
            $no = (int)$ex[2] + 1;
            $newNo = sprintf("%04s", $no);
            $kode = $ex[0].'-'.$date.'-'.$newNo;
        }
        return $kode;
    }

    public function addDetailMemorial()
    {
        $next = $_GET['biggestNo'] + 1;
        $kodeRekening = KodeRekening::select('kode_rekening', 'kode_rekening.nama')->join('kode_induk', 'kode_induk.kode_induk', '=', 'kode_rekening.kode_induk')->get();
        return view('memorial.tambah-detail-memorial', ['hapus' => true, 'no' => $next, 'kodeRekening' => $kodeRekening]);
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'kode_memorial' => 'required',
            'tanggal' => 'required',
            'tipe' => 'required',
            'kode.*' => 'required',
            'lawan.*' => 'required',
            'subtotal.*' => 'required|numeric|gt:0',
            'keterangan.*' => 'required',
            ]
        );

        try {
            $total = 0;
            
            foreach ($_POST['subtotal'] as $key => $value) {
                $total += $value;
            }

            $newMemorial = new Memorial;
            $newMemorial->kode_memorial = $request->get('kode_memorial');
            $newMemorial->tanggal = $request->get('tanggal');
            $newMemorial->tipe = $request->get('tipe');
            $newMemorial->kode_supplier = $request->get('kode_supplier');
            $newMemorial->kode_customer = $request->get('kode_customer');
            $newMemorial->total = $total;
            $newMemorial->created_by = Auth::user()->id;
            $newMemorial->save();

            foreach ($_POST['subtotal'] as $key => $value) {

                $newDetail = new DetailMemorial;
                $newDetail->kode_memorial = $request->get('kode_memorial');
                $newDetail->keterangan = str_replace(' ', '-', strtoupper($_POST['keterangan'][$key]));
                $newDetail->kode = $_POST['kode'][$key];
                $newDetail->lawan = $_POST['lawan'][$key];
                $newDetail->subtotal = $value;

                $newDetail->save();

                $newJurnal = new Jurnal;
                $newJurnal->tanggal = $request->get('tanggal');
                $newJurnal->jenis_transaksi = 'Memorial';
                $newJurnal->kode_transaksi = $request->get('kode_memorial');
                $newJurnal->keterangan = str_replace(' ', '-', strtoupper($_POST['keterangan'][$key]));
                $newJurnal->kode = $_POST['kode'][$key];
                $newJurnal->lawan = $_POST['lawan'][$key];
                $newJurnal->tipe = $request->get('tipe') == 'Masuk' ? 'Debet' : 'Kredit';
                $newJurnal->nominal = $_POST['subtotal'][$key];
                $newJurnal->id_detail = $newDetail->id;
                $newJurnal->save();

            }

            return redirect()->route('memorial.edit', $request->get('kode_memorial'))->withStatus('Data berhasil ditambahkan.');
            // return redirect()->back()->withStatus('Berhasil');
        } catch (\Exception $e) {
            return redirect()->back()->withStatus('Terjadi kesalahan. : ' . $e->getMessage());
        } catch (\Illuminate\Database\QueryException $e) {
            return redirect()->back()->withStatus('Terjadi kesalahan pada database : ' . $e->getMessage());
        }
    }

    public function edit($kode)
    {
        try {
            $this->param['pageInfo'] = 'Memorial / Edit Data';
            $this->param['btnRight']['text'] = 'Lihat Data';
            $this->param['btnRight']['link'] = route('memorial.index');
            $this->param['kodeRekening'] = KodeRekening::select('kode_rekening', 'kode_rekening.nama')->get();

            $this->param['supplier'] = Supplier::get();
            $this->param['customer'] = Customer::get();

            $this->param['memorial'] = Memorial::find($kode);
            $this->param['detailMemorial'] = DetailMemorial::where('kode_memorial', $kode)->get();
            if(isset($_GET['page'])){
                if($this->param['memorial']->kode_supplier!=''){
                    $this->param['totalBayar'] = KartuHutang::select(DB::raw('sum(nominal) as total'))->where('kode_transaksi', $kode)->get()[0];
                    $this->param['hutang'] = PembelianBarang::select('kode_pembelian','grandtotal','terbayar','tanggal','jatuh_tempo')->where("kode_supplier",$this->param['memorial']->kode_supplier)->whereRaw('terbayar != grandtotal')->get();
                }
                else{
                    $this->param['totalBayar'] = KartuPiutang::select(DB::raw('sum(nominal) as total'))->where('kode_transaksi', $kode)->get()[0];
                    $this->param['piutang'] = PenjualanLain::select('kode_penjualan','grandtotal','terbayar','tanggal','jatuh_tempo')->where("kode_customer",$this->param['memorial']->kode_customer)->whereRaw('terbayar != grandtotal')->orWhere()->get();
                }
            }
            return \view('memorial.edit-memorial', $this->param);
        } catch (\Exception $e) {
            return redirect()->back()->withStatus('Terjadi kesalahan : ' . $e->getMessage());
        } catch (\Illuminate\Database\QueryException $e) {
            return redirect()->back()->withStatus('Terjadi kesalahan pada database : ' . $e->getMessage());
        }
    }

    public function addEditDetailMemorial()
    {
        $fields = array(
            'kode' => 'kode',
            'lawan' => 'lawan',
            'subtotal' => 'subtotal',
            'keterangan' => 'keterangan',
        );
        $next = $_GET['biggestNo'] + 1;
        $kodeRekening = KodeRekening::select('kode_rekening', 'kode_rekening.nama')->get();
        return view('memorial.edit-detail-memorial', ['hapus' => true, 'no' => $next, 'kodeRekening' => $kodeRekening, 'fields' => $fields, 'idDetail' => '0']);
    }

    public function update(Request $request, $kode)
    {
        $validatedData = $request->validate([
            'kode_memorial' => 'required',
            'tanggal' => 'required',
            'kode.*' => 'required',
            'lawan.*' => 'required',
            'subtotal.*' => 'required|numeric|gt:0',
            'keterangan.*' => 'required',
        ]);

        try {

            $memorial = Memorial::where('kode_memorial', $kode)->get()[0];

            $bulanMemorial = date('m-Y', strtotime($memorial->tanggal));
            $editBulanMemorial = date('m-Y', strtotime($request->get('tanggal')));

            if ($bulanMemorial != $editBulanMemorial) {
                return redirect()->back()->withStatus('Tidak dapat merubah bulan transaksi');
            }

            $tipe = $memorial->tipe;
            // $grandtotal = $memorial->grandtotal;
            // $kodeSupplier = $memorial->kode_supplier;

            $newTotal = 0;

            foreach ($_POST['lawan'] as $key => $value) {
                // cek apakah penambahan detail baru atau tidak
                if ($_POST['id_detail'][$key] != 0) { // perubahan pada detail tanpa menambah detail baru
                    $getDetail = DetailMemorial::select('kode','lawan', 'keterangan', 'subtotal')->where('id', $_POST['id_detail'][$key])->get()[0];

                    // cek apakah terdapat perubahan pada detail
                    if ($_POST['kode'][$key] != $getDetail['kode'] || $_POST['lawan'][$key] != $getDetail['lawan'] || str_replace(' ', '-', strtoupper($_POST['keterangan'][$key])) != $getDetail['keterangan'] || $_POST['subtotal'][$key] != $getDetail['subtotal']) { 
                        //update detail
                        DetailMemorial::where('id', $_POST['id_detail'][$key])
                        ->update([
                            'kode' => $_POST['kode'][$key],
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
                            'kode' => $_POST['kode'][$key],
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
                        ]);
                    }
                    
                } 
                else { //perubahan pada detail dengan menambah detail baru

                    //insert to detail
                    $newDetail = DetailMemorial::create([
                            'kode_memorial' => $_POST['kode_memorial'],
                            'keterangan' => str_replace(' ', '-', strtoupper($_POST['keterangan'][$key])),
                            'kode' => $_POST['kode'][$key],
                            'lawan' => $_POST['lawan'][$key],
                            'subtotal' => $_POST['subtotal'][$key],
                        ]);
                    
                    // update kartu stock
                    Jurnal::insert([
                            'tanggal' => $_POST['tanggal'],
                            'jenis_transaksi' => 'Memorial',
                            'kode_transaksi' => $_POST['kode_memorial'],
                            'keterangan' => str_replace(' ', '-', strtoupper($_POST['keterangan'][$key])),
                            'kode' => $_POST['kode'][$key],
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
                    DetailMemorial::where('id', $value)->delete();

                    //delete kartu stock
                    Jurnal::where('id_detail', $value)->where('kode_transaksi', $kode)->delete();
                }
            }

            //update memorial
            Memorial::where('kode_memorial', $kode)
                ->update([
                    'tanggal' => $_POST['tanggal'],
                    'kode_supplier' => $request->get('kode_supplier'),
                    'kode_customer' => $request->get('kode_customer'),
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
            $memorial = Memorial::findOrFail($kode);
            // delete detail
            DetailMemorial::where('kode_memorial', $kode)->delete();
            
            // delete jurnal
            Jurnal::where('kode_transaksi', $kode)->delete();

            // insert log activity delete
            $newActivity = new LogActivity;
            $newActivity->id_user = Auth::user()->id;
            $newActivity->jenis_transaksi = 'Memorial';
            $newActivity->tipe = 'Delete';
            $newActivity->keterangan = 'Hapus Memorial dengan kode '. $kode .' dengan total '. $memorial->total;
            $newActivity->save();


            // delete memorial
            $memorial->delete();
            

            return redirect()->route('memorial.index')->withStatus('Data berhasil dihapus.');
        } catch (\Exception $e) {
            return redirect()->back()->withStatus('Terjadi kesalahan. : ' . $e->getMessage());
        } catch (\Illuminate\Database\QueryException $e) {
            return redirect()->back()->withStatus('Terjadi kesalahan pada database : ' . $e->getMessage());
        }
    }

    public function reportMemorial()
    {
        try {
            $this->param['pageInfo'] = 'Memorial / List Memorial';
            $this->param['report'] = null;
        } catch (\Exception $e) {
            return redirect()->back()->withError('Terjadi kesalahan. : ' . $e->getMessage());
        }
        catch(\Illuminate\Database\QueryException $e){
            return redirect()->back()->withError('Terjadi kesalahan pada database. : ' . $e->getMessage());
        }
        return \view('memorial.laporan-memorial', $this->param);
    }

    public function getReport(Request $request)
    {
        $validatedData = $request->validate([
            'start' => 'required',
            'end' => 'required',
            ]
        );
        try {
            $this->param['pageInfo'] = 'Memorial / List Memorial';
            $this->param['kode_memorial'] = Memorial::select('kode_memorial')->get();
            $this->param['report'] = Memorial::join('detail_memorial', 'detail_memorial.kode_memorial', 'memorial.kode_memorial')
                                            ->whereBetween('memorial.tanggal', [$request->get('start'), $request->get('end')])
                                            ->get();
        } catch (\Exception $e) {
            return redirect()->back()->withError('Terjadi kesalahan. : ' . $e->getMessage());
        }
        catch(\Illuminate\Database\QueryException $e){
            return redirect()->back()->withError('Terjadi kesalahan pada database. : ' . $e->getMessage());
        }
        return \view('memorial.laporan-memorial', $this->param);
    }

    public function printReport(Request $request)
    {
        $validatedData = $request->validate([
            'start' => 'required',
            'end' => 'required'
        ]);
        try{
            $this->param['kodeRekeningKas'] = KodeRekening::select('kode_rekening', 'kode_rekening.nama')->join('kode_induk', 'kode_induk.kode_induk', '=', 'kode_rekening.kode_induk')->where('kode_induk.nama', 'Kas')->get();
            $this->param['report'] = Memorial::join('detail_memorial', 'detail_memorial.kode_memorial', 'memorial.kode_memorial')
                                            ->whereBetween('memorial.tanggal', [$request->get('start'), $request->get('end')])
                                            ->get();
            return view('memorial.print-laporan-memorial', $this->param);
        }
        catch(\Exception $e){
            return redirect()->back()->withStatus('Terjadi kesalahan. : ' . $e->getMessage());
        }
        catch(\Illuminate\Database\QueryException $e){
            return redirect()->back()->withStatus('Terjadi kesalahan pada database : ' . $e->getMessage());
        }
    }
}
