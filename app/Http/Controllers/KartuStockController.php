<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use \App\Models\Barang;
use \App\Models\KartuStock;

class KartuStockController extends Controller
{
    private $param;
    public function __construct()
    {
        $this->param['icon'] = 'fa-boxes';
    }

    public function index(Request $request)
    {
        $this->param['pageInfo'] = '-';
        
        try {
            $this->param['barang'] = Barang::orderBy('kode_barang', 'ASC')->get();

            $kodeBarangDari = $request->get('kodeBarangDari');
            $kodeBarangSampai = $request->get('kodeBarangSampai');
            $tanggalDari = $request->get('tanggalDari');
            $tanggalSampai = $request->get('tanggalSampai');

            // if ($tanggalDari < $tanggalSampai) {
            //     return redirect('persediaan/kartu-stock')->withStatus('Rentang waktu tidak valid.');
            // }

            // if (is_null($kodeBarangDari) || is_null($tanggalDari) || is_null($tanggalDari)) {
            //     return redirect('persediaan/kartu-stock')->withStatus('Pilih filter terlebih dahulu.');
            // }

            if (!is_null($kodeBarangDari) && !is_null($kodeBarangSampai) && !is_null($tanggalDari) && !is_null($tanggalSampai) ) {
                // $subQueryPembelianAwal = KartuStock::select(\DB::raw('sum(nominal) AS pembelian_awal'))->from('kartu_stock')->where('tanggal', '<', $tanggalDari)->where('tipe', 'Masuk');
                // $this->param['data'] = \DB::table('kartu_stock AS ks')
                //                         ->select('ks.id', 'ks.tanggal', 'ks.kode_barang', 'ks.kode_transaksi', 'ks.qty', 'ks.nominal', 'ks.tipe', 'b.nama', 'b.stock_awal', 'b.saldo_awal', \DB::raw("(select sum(nominal) FROM kartu_stock WHERE tanggal < '$tanggalDari' AND tipe = 'Masuk' AND kode_barang = '$kodeBarangDari') AS pembelian_awal"))
                //                         ->join('barang AS b', 'b.kode_barang', '=', 'ks.kode_barang')
                //                         ->where('ks.kode_barang', $kodeBarangDari)
                //                         ->whereBetween('ks.tanggal', [$tanggalDari, $tanggalSampai])
                //                         ->orderBy('ks.tanggal', 'ASC')
                //                         ->get();
                // $this->param['data'] = \DB::table('kartu_stock AS ks')
                //                         ->select('ks.id', 'ks.tanggal', 'ks.kode_barang', 'ks.kode_transaksi', 'ks.qty', 'ks.nominal', 'ks.tipe', 'b.nama', 'b.stock_awal', 'b.saldo_awal', \DB::raw("($subQueryPembelianAwal->toSql()})"))
                //                         ->mergeBindings($subQueryPembelianAwal->getQuery())
                //                         ->join('barang AS b', 'b.kode_barang', '=', 'ks.kode_barang')
                //                         ->where('ks.kode_barang', $kodeBarangDari)
                //                         ->whereBetween('ks.tanggal', [$tanggalDari, $tanggalSampai])
                //                         ->orderBy('ks.tanggal', 'ASC')
                //                         ->get();
                $this->param['kodeBarang'] = Barang::select('kode_barang', 'nama', 'stock_awal', 'saldo_awal')->whereBetween('kode_barang', [$kodeBarangDari, $kodeBarangSampai])->orderBy('kode_barang', 'ASC')->get();
            }
        } catch (\Illuminate\Database\QueryException $e) {
            return redirect()->back()->withStatus($e->getMessage());
        }

        return \view('persediaan.kartu-stock.kartu-stock', $this->param);
    }

    public function posisiStock(Request $request)
    {
        $this->param['pageInfo'] = '-';
        
        try {
            $this->param['barang'] = Barang::orderBy('kode_barang', 'ASC')->get();

            $kodeBarangDari = $request->get('kodeBarangDari');
            $kodeBarangSampai = $request->get('kodeBarangSampai');
            $tanggalDari = $request->get('tanggalDari');
            $tanggalSampai = $request->get('tanggalSampai');

            // if ($tanggalDari < $tanggalSampai) {
            //     return redirect('persediaan/kartu-stock')->withStatus('Rentang waktu tidak valid.');
            // }

            // if (is_null($kodeBarangDari) || is_null($tanggalDari) || is_null($tanggalDari)) {
            //     return redirect('persediaan/kartu-stock')->withStatus('Pilih filter terlebih dahulu.');
            // }

            if (!is_null($kodeBarangDari) && !is_null($kodeBarangSampai) && !is_null($tanggalDari) && !is_null($tanggalSampai) ) {
                // $subQueryPembelianAwal = KartuStock::select(\DB::raw('sum(nominal) AS pembelian_awal'))->from('kartu_stock')->where('tanggal', '<', $tanggalDari)->where('tipe', 'Masuk');
                // $this->param['data'] = \DB::table('kartu_stock AS ks')
                //                         ->select('ks.id', 'ks.tanggal', 'ks.kode_barang', 'ks.kode_transaksi', 'ks.qty', 'ks.nominal', 'ks.tipe', 'b.nama', 'b.stock_awal', 'b.saldo_awal', \DB::raw("(select sum(nominal) FROM kartu_stock WHERE tanggal < '$tanggalDari' AND tipe = 'Masuk' AND kode_barang = '$kodeBarangDari') AS pembelian_awal"))
                //                         ->join('barang AS b', 'b.kode_barang', '=', 'ks.kode_barang')
                //                         ->where('ks.kode_barang', $kodeBarangDari)
                //                         ->whereBetween('ks.tanggal', [$tanggalDari, $tanggalSampai])
                //                         ->orderBy('ks.tanggal', 'ASC')
                //                         ->get();
                // $this->param['data'] = \DB::table('kartu_stock AS ks')
                //                         ->select('ks.id', 'ks.tanggal', 'ks.kode_barang', 'ks.kode_transaksi', 'ks.qty', 'ks.nominal', 'ks.tipe', 'b.nama', 'b.stock_awal', 'b.saldo_awal', \DB::raw("($subQueryPembelianAwal->toSql()})"))
                //                         ->mergeBindings($subQueryPembelianAwal->getQuery())
                //                         ->join('barang AS b', 'b.kode_barang', '=', 'ks.kode_barang')
                //                         ->where('ks.kode_barang', $kodeBarangDari)
                //                         ->whereBetween('ks.tanggal', [$tanggalDari, $tanggalSampai])
                //                         ->orderBy('ks.tanggal', 'ASC')
                //                         ->get();
                $this->param['kodeBarang'] = Barang::select('kode_barang', 'nama', 'satuan','stock_awal', 'saldo_awal')->whereBetween('kode_barang', [$kodeBarangDari, $kodeBarangSampai])->orderBy('kode_barang', 'ASC')->get();
            }
        } catch (\Illuminate\Database\QueryException $e) {
            return redirect()->back()->withStatus($e->getMessage());
        }

        return \view('persediaan.posisi-stock.posisi-stock', $this->param);
    }
}
