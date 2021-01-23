<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use \App\Models\PembelianBarang;
use \App\Models\DetailPembelianBarang;
use \App\Models\Supplier;
use \App\Models\Barang;

class PembelianBarangController extends Controller
{
    private $param;
    public function __construct()
    {
        $this->param['icon'] = 'fa-shopping-cart';
    }

    public function index(Request $request)
    {
        $this->param['pageInfo'] = 'Pembelian Barang / List Pembelian Barang';
        $this->param['btnRight']['text'] = 'Tambah Data';
        $this->param['btnRight']['link'] = route('pembelian-barang.create');

        try {
            $keyword = $request->get('keyword');
            if ($keyword) {
                $pembelianBarang = PembelianBarang::with('supplier')->where('kode_pembelian', 'LIKE', "%$keyword%")->orWhere('kode_supplier', 'LIKE', "%$keyword%")->paginate(10);
            }
            else{
                $pembelianBarang = PembelianBarang::with('supplier')->paginate(10);
            }
        } catch (\Illuminate\Database\QueryException $e) {
            return redirect()->back()->withStatus('Terjadi Kesalahan');
        }
                
        return \view('pembelian.pembelian-barang.list-pembelian-barang', ['pembelianBarang' => $pembelianBarang], $this->param);
    }

    public function create()
    {
        try {
            $this->param['pageInfo'] = 'Pembelian Barang / List Pembelian Barang';
            $this->param['btnRight']['text'] = 'Lihat Data';
            $this->param['btnRight']['link'] = route('pembelian-barang.index');
            $this->param['supplier'] = Supplier::get();
            $this->param['barang'] = Barang::get();
        } catch (\Exception $e) {
            return redirect()->back()->withStatus('Terjadi kesalahan. : '. $e->getMessage());
        }

        return \view('pembelian.pembelian-barang.tambah-pembelian-barang', $this->param);
    }

    public function getKode()
    {
        $tgl = explode('-',$_GET['tanggal']);
        $y = $tgl[0];
        $m = $tgl[1];
        $lastKode = PembelianBarang::select('kode_pembelian')
        ->whereMonth('tanggal', $m)
        ->whereYear('tanggal', $y)
        ->orderBy('kode_pembelian','desc')
        ->skip(0)->take(1)
        ->get();
        if(count($lastKode)==0){
            $dateCreate = date_create($_GET['tanggal']);
            $date = date_format($dateCreate, 'my');
            $kode = "PB".$date."-0001";
        }
        else{
            $ex = explode('-', $lastKode[0]->kode_pembelian);
            $no = (int)$ex[1] + 1;
            $newNo = sprintf("%04s", $no);
            $kode = $ex[0].'-'.$newNo;
        }
        return $kode;
    }

    public function addDetailPembelian()
    {   
        $next = $_GET['biggestNo']+1;
        $barang = Barang::select('kode_barang','nama')->get();
        return view('pembelian.pembelian-barang.tambah-detail-pembelian-barang',['hapus' => true, 'no' => $next, 'barang' => $barang]);
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'tanggal' => 'required',
            'kode_supplier' => 'required',
            'kode_barang.*' => 'required',
            'qty.*' => 'required|min:1',
            'harga_satuan.*' => 'required|min:1',
        ]);

        try {
            $ttlQty = 0;
            $total = 0;
            foreach ($_POST['qty'] as $key => $value) {
                $ttlQty = $ttlQty + $value;
                $total = $total + $_POST['subtotal'][$key];
            }
            
            $newPembelian = new PembelianBarang;
            $newPembelian->kode_pembelian = $request->get('kode_pembelian');
            $newPembelian->kode_supplier = $request->get('kode_supplier');
            $newPembelian->tanggal = $request->get('tanggal');
            $newPembelian->status_ppn = $request->get('status_ppn');
            $newPembelian->jatuh_tempo = $request->get('jatuh_tempo');
            $newPembelian->total_qty = $ttlQty;
            $newPembelian->total = $total;
            $newPembelian->total_ppn = 0;
            $newPembelian->grandtotal = $total;
            $newPembelian->terbayar = 0;

            $newPembelian->save();

            foreach ($_POST['kode_barang'] as $key => $value) {
                $newDetail = new DetailPembelianBarang;
                $newDetail->kode_pembelian = $request->get('kode_pembelian');
                $newDetail->kode_barang = $value;
                $newDetail->harga_satuan = $_POST['harga_satuan'][$key];
                $newDetail->qty = $_POST['qty'][$key];
                $newDetail->subtotal = $_POST['subtotal'][$key];

                $newDetail->save();

                $barang = Barang::select('stock','saldo')->where('kode_barang',$value)->get()[0];

                $updateBarang = Barang::findOrFail($value);
                $updateBarang->stock = $barang->stock + $_POST['qty'][$key];
                $updateBarang->saldo = $barang->saldo + $_POST['subtotal'][$key];

                $updateBarang->save();
            }

            return redirect()->route('pembelian-barang.index')->withStatus('Data berhasil ditambahkan.');
        } catch(\Exception $e){
            return redirect()->back()->withStatus('Terjadi kesalahan. : '. $e->getMessage());
        }
        catch(\Illuminate\Database\QueryException $e){
            return redirect()->back()->withStatus('Terjadi kesalahan pada database : '. $e->getMessage());
        }
    }
}
