<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use \App\Models\Jurnal;
use \App\Models\Kas;
use \App\Models\DetailKas;
use \App\Models\KodeRekening;
use \App\Models\Supplier;
use \App\Models\Customer;

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
            $this->param['kodeRekeningKas'] = KodeRekening::select('kode_rekening', 'kode_rekening.nama')->join('kode_induk', 'kode_induk.kode_induk', '=', 'kode_rekening.kode_induk')->where('kode_induk.nama', 'Kas')->get();

            $this->param['lawan'] = KodeRekening::select('kode_rekening', 'kode_rekening.nama')->join('kode_induk', 'kode_induk.kode_induk', '=', 'kode_rekening.kode_induk')->where('kode_induk.nama', '!=','Kas')->where('kode_induk.nama', '!=','Bank')->get();

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
            $kode = "BBM-".$date."-0001";
        }
        elseif(count($lastKode)==0 && $tipe == 'Keluar'){
            $kode = "BBK-".$date."-0001";
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
        $lawan = KodeRekening::select('kode_rekening', 'kode_rekening.nama')->join('kode_induk', 'kode_induk.kode_induk', '=', 'kode_rekening.kode_induk')->where('kode_induk.nama', '!=','Kas')->where('kode_induk.nama', '!=','Bank')->get();
        return view('kas.tambah-detail-transaksi-kas', ['hapus' => true, 'no' => $next, 'lawan' => $lawan]);
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'kode_kas' => 'required',
            'tanggal' => 'required',
            'tipe' => 'required',
            'kode_rekening' => 'required',
            'lawan.*' => 'required',
            'subtotal.*' => 'required|min:1',
            'keterangan.*' => 'required',
            ]
        );

        try {
            $total = 0;
            
            foreach ($_POST['subtotal'] as $key => $value) {
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
            $newKas->save();

            foreach ($_POST['subtotal'] as $key => $value) {

                $newDetail = new DetailKas;
                $newDetail->kode_kas = $request->get('kode_kas');
                $newDetail->keterangan = $_POST['keterangan'][$key];
                $newDetail->lawan = $_POST['lawan'][$key];
                $newDetail->subtotal = $value;

                $newDetail->save();

                $newJurnal = new Jurnal;
                $newJurnal->tanggal = $request->get('tanggal');
                $newJurnal->jenis_transaksi = 'Kas';
                $newJurnal->kode_transaksi = $request->get('kode_kas');
                $newJurnal->keterangan = $_POST['keterangan'][$key];
                $newJurnal->kode = $request->get('kode_rekening');
                $newJurnal->lawan = $_POST['lawan'][$key];
                $newJurnal->tipe = $request->get('tipe') == 'Masuk' ? 'Debet' : 'Kredit';
                $newJurnal->nominal = $_POST['subtotal'][$key];
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
}
