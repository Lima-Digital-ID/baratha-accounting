<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use \App\Models\PemakaianBarang;
use \App\Models\DetailPemakaianBarang;
use \App\Models\Supplier;
use \App\Models\Barang;
use \App\Models\KartuHutang;
use \App\Models\KartuStock;
use App\Models\KodeBiaya;

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
                $pemakaianBarang = PemakaianBarang::with('supplier')->where('kode_pemakaian', 'LIKE', "%$keyword%")->orWhere('kode_supplier', 'LIKE', "%$keyword%")->paginate(10);
            } else {
                $pemakaianBarang = PemakaianBarang::with('supplier')->paginate(10);
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
            $kode = "PB" . $date . "-0001";
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
}
