<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use \App\Models\Jurnal;
use \App\Models\Bank;
use \App\Models\DetailBank;
use \App\Models\KodeRekening;
use \App\Models\Supplier;
use \App\Models\Customer;
use \App\Models\KartuHutang;
use \App\Models\PembelianBarang;
use \App\Models\KartuPiutang;
use \App\Models\PenjualanLain;
use Illuminate\Support\Facades\DB;
use \App\Models\LogActivity;
use Illuminate\Support\Facades\Auth;

class BankController extends Controller
{
    private $param;
    public function __construct()
    {
        $this->param['icon'] = 'fa-credit-card';
    }

    public function index(Request $request)
    {
        $this->param['pageInfo'] = 'Transaksi Bank / List Transaksi Bank';
        $this->param['btnRight']['text'] = 'Tambah Data';
        $this->param['btnRight']['link'] = route('transaksi-bank.create');

        try {
            $keyword = $request->get('keyword');
            if ($keyword) {
                $this->param['bank'] = Bank::where('kode_bank', 'LIKE', "%$keyword%")->orWhere('kode_supplier', 'LIKE', "%$keyword%")->orWhere('kode_customer', 'LIKE', "%$keyword%")->orderBy('tanggal', 'DESC')->paginate(10);
            } else {
                $this->param['bank'] = Bank::orderBy('tanggal', 'DESC')->paginate(10);
            }
        } catch (\Illuminate\Database\QueryException $e) {
            return redirect()->back()->withStatus('Terjadi Kesalahan');
        }

        return \view('bank.list-transaksi-bank', $this->param);
    }

    public function create()
    {
        try {
            $this->param['pageInfo'] = 'Transaksi Bank / Tambah Transaksi Bank';
            $this->param['btnRight']['text'] = 'Lihat Data';
            $this->param['btnRight']['link'] = route('transaksi-bank.index');
            $this->param['kodeRekeningBank'] = KodeRekening::select('kode_rekening', 'kode_rekening.nama')->join('kode_induk', 'kode_induk.kode_induk', '=', 'kode_rekening.kode_induk')->where('kode_induk.nama', 'Bank')->get();

            $this->param['lawan'] = KodeRekening::select('kode_rekening', 'kode_rekening.nama')->join('kode_induk', 'kode_induk.kode_induk', '=', 'kode_rekening.kode_induk')->where('kode_induk.nama', '!=','Kas')->where('kode_induk.nama', '!=','Bank')->get();

            $this->param['supplier'] = Supplier::get();
            $this->param['customer'] = Customer::get();

        } catch (\Exception $e) {
            return redirect()->back()->withStatus('Terjadi kesalahan. : ' . $e->getMessage());
        }

        return \view('bank.tambah-transaksi-bank', $this->param);
    }

    public function getKode()
    {
        $tgl = explode('-',$_GET['tanggal']);
        $y = $tgl[0];
        $m = $tgl[1];
        $tipe = $_GET['tipe'];
        $lastKode = \DB::table('bank')
        ->select('kode_bank')
        ->where(\DB::raw('length(kode_bank)'), 13)
        ->whereMonth('tanggal', $m)
        ->whereYear('tanggal', $y)
        ->where('tipe', $tipe)
        ->orderBy('kode_bank','desc')
        ->skip(0)->take(1)
        ->get();
        $dateCreate = date_create($_GET['tanggal']);
        $date = date_format($dateCreate, 'my');
        if(count($lastKode)==0 && $tipe == 'Masuk'){
            $kode = "BBM-".$date."-0001";
        }
        elseif(count($lastKode)==0 && $tipe == 'Keluar'){
            $kode = "BBK-".$date."-0001";
        }
        else{
            $ex = explode('-', $lastKode[0]->kode_bank);
            $no = (int)$ex[2] + 1;
            $newNo = sprintf("%04s", $no);
            $kode = $ex[0].'-'.$date.'-'.$newNo;
        }
        return $kode;
    }

    public function addDetailTransaksiBank()
    {
        $next = $_GET['biggestNo'] + 1;
        $lawan = KodeRekening::select('kode_rekening', 'kode_rekening.nama')->join('kode_induk', 'kode_induk.kode_induk', '=', 'kode_rekening.kode_induk')->where('kode_induk.nama', '!=','Kas')->where('kode_induk.nama', '!=','Bank')->get();
        return view('bank.tambah-detail-transaksi-bank', ['hapus' => true, 'no' => $next, 'lawan' => $lawan]);
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'kode_bank' => 'required',
            'tanggal' => 'required',
            'tipe' => 'required',
            'kode_rekening' => 'required',
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

            $newBank = new Bank;
            $newBank->kode_bank = $request->get('kode_bank');
            $newBank->tanggal = $request->get('tanggal');
            $newBank->tipe = $request->get('tipe');
            $newBank->kode_rekening = $request->get('kode_rekening');
            $newBank->kode_supplier = $request->get('kode_supplier');
            $newBank->kode_customer = $request->get('kode_customer');
            $newBank->total = $total;
            $newBank->created_by = Auth::user()->id;
            $newBank->save();

            foreach ($_POST['subtotal'] as $key => $value) {

                $newDetail = new DetailBank;
                $newDetail->kode_bank = $request->get('kode_bank');
                $newDetail->keterangan = $_POST['keterangan'][$key];
                $newDetail->lawan = $_POST['lawan'][$key];
                $newDetail->subtotal = $value;

                $newDetail->save();

                $newJurnal = new Jurnal;
                $newJurnal->tanggal = $request->get('tanggal');
                $newJurnal->jenis_transaksi = 'Bank';
                $newJurnal->kode_transaksi = $request->get('kode_bank');
                $newJurnal->keterangan = $_POST['keterangan'][$key];
                $newJurnal->kode = $request->get('kode_rekening');
                $newJurnal->lawan = $_POST['lawan'][$key];
                $newJurnal->tipe = $request->get('tipe') == 'Masuk' ? 'Debet' : 'Kredit';
                $newJurnal->nominal = $_POST['subtotal'][$key];
                $newJurnal->id_detail = $newDetail->id;
                $newJurnal->save();

            }

            return redirect()->route('transaksi-bank.edit', $request->get('kode_bank'))->withStatus('Data berhasil ditambahkan.');
        } catch (\Exception $e) {
            return redirect()->back()->withStatus('Terjadi kesalahan. : ' . $e->getMessage());
        } catch (\Illuminate\Database\QueryException $e) {
            return redirect()->back()->withStatus('Terjadi kesalahan pada database : ' . $e->getMessage());
        }
    }

    public function edit($kode)
    {
        try {
            $this->param['pageInfo'] = 'Transaksi Bank / Edit Data';
            $this->param['btnRight']['text'] = 'Lihat Data';
            $this->param['btnRight']['link'] = route('transaksi-bank.index');
            $this->param['kodeRekeningBank'] = KodeRekening::select('kode_rekening', 'kode_rekening.nama')->join('kode_induk', 'kode_induk.kode_induk', '=', 'kode_rekening.kode_induk')->where('kode_induk.nama', 'Bank')->get();

            $this->param['lawan'] = KodeRekening::select('kode_rekening', 'kode_rekening.nama')->join('kode_induk', 'kode_induk.kode_induk', '=', 'kode_rekening.kode_induk')->where('kode_induk.nama', '!=','Kas')->where('kode_induk.nama', '!=','Bank')->get();

            $this->param['supplier'] = Supplier::get();
            $this->param['customer'] = Customer::get();

            $this->param['bank'] = Bank::find($kode);
            $this->param['detailTransaksiBank'] = DetailBank::where('kode_bank', $kode)->get();
            if(isset($_GET['page'])){
                if($this->param['bank']->kode_supplier!=''){
                    $this->param['totalBayar'] = KartuHutang::select(DB::raw('sum(nominal) as total'))->where('kode_transaksi', $kode)->get()[0];   
                    $this->param['hutang'] = PembelianBarang::select('kode_pembelian','grandtotal','terbayar','tanggal','jatuh_tempo')->where("kode_supplier",$this->param['bank']->kode_supplier)->whereRaw('terbayar != grandtotal')->get();
                }
                else{
                    $this->param['totalBayar'] = KartuPiutang::select(DB::raw('sum(nominal) as total'))->where('kode_transaksi', $kode)->get()[0];
                    $this->param['piutang'] = PenjualanLain::select('kode_penjualan','grandtotal','terbayar','tanggal','jatuh_tempo')->where("kode_customer",$this->param['bank']->kode_customer)->whereRaw('terbayar != grandtotal')->get();
                }
            }
            return \view('bank.edit-transaksi-bank', $this->param);
        } catch (\Exception $e) {
            return redirect()->back()->withStatus('Terjadi kesalahan : ' . $e->getMessage());
        } catch (\Illuminate\Database\QueryException $e) {
            return redirect()->back()->withStatus('Terjadi kesalahan pada database : ' . $e->getMessage());
        }
    }

    public function addEditDetailTransaksiBank()
    {
        $fields = array(
            'lawan' => 'lawan',
            'subtotal' => 'subtotal',
            'keterangan' => 'keterangan',
        );
        $next = $_GET['biggestNo'] + 1;
        $lawan = KodeRekening::select('kode_rekening', 'kode_rekening.nama')->join('kode_induk', 'kode_induk.kode_induk', '=', 'kode_rekening.kode_induk')->where('kode_induk.nama', '!=','Kas')->where('kode_induk.nama', '!=','Bank')->get();
        return view('bank.edit-detail-transaksi-bank', ['hapus' => true, 'no' => $next, 'lawan' => $lawan, 'fields' => $fields, 'idDetail' => '0']);
    }

    public function update(Request $request, $kode)
    {
        $validatedData = $request->validate([
            'kode_bank' => 'required',
            'tanggal' => 'required',
            'kode_rekening' => 'required',
            'lawan.*' => 'required',
            'subtotal.*' => 'required|numeric|gt:0',
            'keterangan.*' => 'required',
        ]);

        try {

            $transaksiBank = Bank::where('kode_bank', $kode)->get()[0];

            $bulanTransaksiBank = date('m-Y', strtotime($transaksiBank->tanggal));
            $editBulanTransaksiBank = date('m-Y', strtotime($request->get('tanggal')));

            if ($bulanTransaksiBank != $editBulanTransaksiBank) {
                return redirect()->back()->withStatus('Tidak dapat merubah bulan transaksi');
            }

            $tipe = $transaksiBank->tipe;
            // $grandtotal = $transaksiBank->grandtotal;
            // $kodeSupplier = $transaksiBank->kode_supplier;

            $newTotal = 0;

            foreach ($_POST['lawan'] as $key => $value) {
                // cek apakah penambahan detail baru atau tidak
                if ($_POST['id_detail'][$key] != 0) { // perubahan pada detail tanpa menambah detail baru
                    $getDetail = DetailBank::select('lawan', 'keterangan', 'subtotal')->where('id', $_POST['id_detail'][$key])->get()[0];

                    // cek apakah terdapat perubahan pada detail
                    if ($_POST['lawan'][$key] != $getDetail['lawan'] || $_POST['keterangan'][$key] != $getDetail['keterangan'] || $_POST['subtotal'][$key] != $getDetail['subtotal']) { 
                        //update detail
                        DetailBank::where('id', $_POST['id_detail'][$key])
                        ->update([
                            'lawan' => $_POST['lawan'][$key],
                            'subtotal' => $_POST['subtotal'][$key],
                            'keterangan' => $_POST['keterangan'][$key],
                        ]);

                    // update jurnal
                        Jurnal::where('id_detail', $_POST['id_detail'][$key])
                        ->where('kode_transaksi', $kode)
                        ->update([
                            'tanggal' => $_POST['tanggal'],
                            'keterangan' => $_POST['keterangan'][$key],
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
                    $newDetail = DetailBank::create([
                            'kode_bank' => $_POST['kode_bank'],
                            'keterangan' => $_POST['keterangan'][$key],
                            'lawan' => $_POST['lawan'][$key],
                            'subtotal' => $_POST['subtotal'][$key],
                        ]);
                    
                    // update kartu stock
                    Jurnal::insert([
                            'tanggal' => $_POST['tanggal'],
                            'jenis_transaksi' => 'Bank',
                            'kode_transaksi' => $_POST['kode_bank'],
                            'keterangan' => $_POST['keterangan'][$key],
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
                    DetailBank::where('id', $value)->delete();

                    //delete kartu stock
                    Jurnal::where('id_detail', $value)->where('kode_transaksi', $kode)->delete();
                }
            }

            //update bank
            Bank::where('kode_bank', $kode)
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
            $bank = Bank::findOrFail($kode);
            // delete detail
            DetailBank::where('kode_bank', $kode)->delete();
            
            // delete jurnal
            Jurnal::where('kode_transaksi', $kode)->delete();
            // delete bank
            // insert log activity delete
            $newActivity = new LogActivity;
            $newActivity->id_user = Auth::user()->id;
            $newActivity->jenis_transaksi = 'Bank';
            $newActivity->tipe = 'Delete';
            $newActivity->keterangan = 'Hapus Bank dengan kode '. $kode .' dengan total '. $bank->total;
            $newActivity->save();


            $bank->delete();
            //reset hutang/piutang
            //update hutang/piutang pada customer/supplier

            return redirect()->route('transaksi-bank.index')->withStatus('Data berhasil dihapus.');
        } catch (\Exception $e) {
            return redirect()->back()->withStatus('Terjadi kesalahan. : ' . $e->getMessage());
        } catch (\Illuminate\Database\QueryException $e) {
            return redirect()->back()->withStatus('Terjadi kesalahan pada database : ' . $e->getMessage());
        }
    }

    public function reportBank()
    {
        try{
            $this->param['kodeRekeningBank'] = KodeRekening::select('kode_rekening', 'kode_rekening.nama')->join('kode_induk', 'kode_induk.kode_induk', '=', 'kode_rekening.kode_induk')->where('kode_induk.nama', 'Bank')->get();
            $this->param['report'] = null;

            return view('bank.laporan-bank', $this->param);
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
            $this->param['kodeRekeningBank'] = KodeRekening::select('kode_rekening', 'kode_rekening.nama')->join('kode_induk', 'kode_induk.kode_induk', '=', 'kode_rekening.kode_induk')->where('kode_induk.nama', 'Bank')->get();
            $this->param['report'] = Bank::select(
                                        'bank.kode_bank',
                                        'bank.tanggal',
                                        'bank.kode_rekening',
                                        'bank.tipe',
                                        'bank.total',
                                        'detail.keterangan',
                                        'detail.lawan',
                                        'detail.subtotal'
                                    )
                                    ->join('detail_bank AS detail', 'detail.kode_bank', 'bank.kode_bank')
                                    ->where('bank.kode_rekening', $request->get('kode_perkiraan'))
                                    ->whereBetween('bank.tanggal', [$request->get('start'), $request->get('end')])
                                    ->get();
            return view('bank.laporan-bank', $this->param);
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
            $this->param['kodeRekeningBank'] = KodeRekening::select('kode_rekening', 'kode_rekening.nama')->join('kode_induk', 'kode_induk.kode_induk', '=', 'kode_rekening.kode_induk')->where('kode_induk.nama', 'Bank')->get();
            $this->param['report'] = Bank::select(
                                        'bank.kode_bank',
                                        'bank.tanggal',
                                        'bank.kode_rekening',
                                        'bank.tipe',
                                        'bank.total',
                                        'detail.keterangan',
                                        'detail.lawan',
                                        'detail.subtotal'
                                    )
                                    ->join('detail_bank AS detail', 'detail.kode_bank', 'bank.kode_bank')
                                    ->where('bank.kode_rekening', $request->get('kode_perkiraan'))
                                    ->whereBetween('bank.tanggal', [$request->get('start'), $request->get('end')])
                                    ->get();
            return view('bank.print-laporan-bank', $this->param);
        }
        catch(\Exception $e){
            return redirect()->back()->withStatus('Terjadi kesalahan. : ' . $e->getMessage());
        }
        catch(\Illuminate\Database\QueryException $e){
            return redirect()->back()->withStatus('Terjadi kesalahan pada database : ' . $e->getMessage());
        }
    }
}
