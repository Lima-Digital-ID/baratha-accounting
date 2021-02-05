<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use \App\Models\PemakaianBarang;
use \App\Models\DetailPemakaianBarang;
use \App\Models\Supplier;
use \App\Models\Barang;
use \App\Models\KartuStock;
use App\Models\KodeBiaya;
use \App\Models\KartuHutang;

class PemakaianBarangController extends Controller
{
    private $param;
    public function __construct()
    {
        $this->param['icon'] = 'fa-boxes';
    }

    public function index(Request $request)
    {
        $this->param['pageInfo'] = 'Pemakaian Barang / List Pemakaian Barang';
        $this->param['btnRight']['text'] = 'Tambah Data';
        $this->param['btnRight']['link'] = route('pemakaian-barang.create');

        try {
            $keyword = $request->get('keyword');
            if ($keyword) {
                $pemakaianBarang = PemakaianBarang::where('kode_pemakaian', 'LIKE', "%$keyword%")->orWhere('kode_supplier', 'LIKE', "%$keyword%")->paginate(10);
            } else {
                $pemakaianBarang = PemakaianBarang::paginate(10);
            }
        } catch (\Illuminate\Database\QueryException $e) {
            return redirect()->back()->withStatus('Terjadi Kesalahan');
        }

        return \view('persediaan.pemakaian-barang.list-pemakaian-barang', ['pemakaianBarang' => $pemakaianBarang], $this->param);
    }

    public function create()
    {
        try {
            $this->param['pageInfo'] = 'Pemakaian Barang / List Pemakaian Barang';
            $this->param['btnRight']['text'] = 'Lihat Data';
            $this->param['btnRight']['link'] = route('pemakaian-barang.index');
            $this->param['barang'] = Barang::get();
            $this->param['kodeBiaya'] = KodeBiaya::get();
        } catch (\Exception $e) {
            return redirect()->back()->withStatus('Terjadi kesalahan. : ' . $e->getMessage());
        }

        return \view('persediaan.pemakaian-barang.tambah-pemakaian-barang', $this->param);
    }

    public function getKode()
    {
        $tgl = explode('-', $_GET['tanggal']);
        $y = $tgl[0];
        $m = $tgl[1];
        $lastKode = PemakaianBarang::select('kode_pemakaian')
            ->whereMonth('tanggal', $m)
            ->whereYear('tanggal', $y)
            ->orderBy('kode_pemakaian', 'desc')
            ->skip(0)->take(1)
            ->get();
        if (count($lastKode) == 0) {
            $dateCreate = date_create($_GET['tanggal']);
            $date = date_format($dateCreate, 'my');
            $kode = "PK" . $date . "-0001";
        } else {
            $ex = explode('-', $lastKode[0]->kode_pemakaian);
            $no = (int)$ex[1] + 1;
            $newNo = sprintf("%04s", $no);
            $kode = $ex[0] . '-' . $newNo;
        }
        return $kode;
    }

    public function addDetailPemakaian()
    {
        $next = $_GET['biggestNo'] + 1;
        $barang = Barang::select('kode_barang', 'nama')->get();
        $kodeBiaya = KodeBiaya::select('kode_biaya', 'nama')->get();
        return view('persediaan.pemakaian-barang.tambah-detail-pemakaian-barang', ['hapus' => true, 'no' => $next, 'barang' => $barang, 'kodeBiaya' => $kodeBiaya]);
    }

    public function getStock()
    {
        $kodeBarang = $_GET['kodeBarang'];

        $stock = Barang::select('stock',  'saldo')->where('kode_barang', $kodeBarang)->get()[0];

        return $stock;
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'kode_pemakaian' => 'required',
            'tanggal' => 'required',
            'kode_barang.*' => 'required',
            'qty.*' => 'required|min:1|lte:stock.*',
            'kode_biaya.*' => 'required',
            ],
            [
                'required' => 'The :attribute field is required.',
                'lte' => 'Quantity tidak boleh melebihi stock.'
            ] 
        );

        try {
            $ttlQty = 0;
            $totalPemakaian = 0;
            
            foreach ($_POST['qty'] as $key => $value) {
                $ttlQty = $ttlQty + $value;
                $totalPemakaian = $totalPemakaian + (($_POST['saldo'][$key] / $_POST['stock'][$key]) * $value );
            }

            $newPemakaian = new PemakaianBarang;
            $newPemakaian->kode_pemakaian = $request->get('kode_pemakaian');
            $newPemakaian->tanggal = $request->get('tanggal');
            $newPemakaian->total_qty = $ttlQty;
            $newPemakaian->total_pemakaian = $totalPemakaian;

            $newPemakaian->save();

            foreach ($_POST['kode_barang'] as $key => $value) {

                $subtotal = ($_POST['saldo'][$key] / $_POST['stock'][$key] ) * $_POST['qty'][$key];
                //save ke tabel detail pemakaian
                $newDetail = new DetailPemakaianBarang;
                $newDetail->kode_pemakaian = $request->get('kode_pemakaian');
                $newDetail->kode_barang = $value;
                $newDetail->qty = $_POST['qty'][$key];
                $newDetail->subtotal = $subtotal;
                $newDetail->kode_biaya = $_POST['kode_biaya'][$key];
                $newDetail->keterangan = $_POST['keterangan'][$key];

                $newDetail->save();

                //update stock barang
                $barang = Barang::select('stock', 'saldo')->where('kode_barang', $value)->get()[0];

                $updateBarang = Barang::findOrFail($value);
                $updateBarang->stock = $barang->stock - $_POST['qty'][$key];
                $updateBarang->saldo = $barang->saldo - $subtotal;

                $updateBarang->save();

                //insert ke table kartu stock
                $kartuStock = new KartuStock;
                $kartuStock->tanggal = $request->get('tanggal');
                $kartuStock->kode_barang = $value;
                $kartuStock->kode_transaksi = $request->get('kode_pemakaian');
                $kartuStock->id_detail = $newDetail->id;
                $kartuStock->qty = $_POST['qty'][$key];
                $kartuStock->nominal = $subtotal;
                $kartuStock->tipe = 'Keluar';
                $kartuStock->save();
            }

            return redirect()->route('pemakaian-barang.index')->withStatus('Data berhasil ditambahkan.');
        } catch (\Exception $e) {
            return redirect()->back()->withStatus('Terjadi kesalahan. : ' . $e->getMessage());
        } catch (\Illuminate\Database\QueryException $e) {
            return redirect()->back()->withStatus('Terjadi kesalahan pada database : ' . $e->getMessage());
        }
    }

    public function edit($kode)
    {
        try {
            $this->param['pageInfo'] = 'Pemakaian Barang / Edit Data';
            $this->param['btnRight']['text'] = 'Lihat Data';
            $this->param['btnRight']['link'] = route('pemakaian-barang.index');
            $this->param['barang'] = Barang::get();
            $this->param['kodeBiaya'] = KodeBiaya::get();
            $this->param['pemakaian'] = PemakaianBarang::find($kode);
            $this->param['detailPemakaian'] = DetailPemakaianBarang::where('kode_pemakaian', $kode)->get();

            return \view('persediaan.pemakaian-barang.edit-pemakaian-barang', $this->param);
        } catch (\Exception $e) {
            return redirect()->back()->withError('Terjadi kesalahan : ' . $e->getMessage());
        } catch (\Illuminate\Database\QueryException $e) {
            return redirect()->back()->withError('Terjadi kesalahan pada database : ' . $e->getMessage());
        }
    }
}
