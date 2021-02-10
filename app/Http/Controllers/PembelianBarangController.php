<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use \App\Models\PembelianBarang;
use \App\Models\DetailPembelianBarang;
use \App\Models\Supplier;
use \App\Models\Barang;
use \App\Models\KartuHutang;
use \App\Models\KartuStock;

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
            } else {
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
            $this->param['pageInfo'] = 'Pembelian Barang / Tambah Pembelian Barang';
            $this->param['btnRight']['text'] = 'Lihat Data';
            $this->param['btnRight']['link'] = route('pembelian-barang.index');
            $this->param['supplier'] = Supplier::get();
            $this->param['barang'] = Barang::get();
        } catch (\Exception $e) {
            return redirect()->back()->withStatus('Terjadi kesalahan. : ' . $e->getMessage());
        }

        return \view('pembelian.pembelian-barang.tambah-pembelian-barang', $this->param);
    }

    public function getKode()
    {
        $tgl = explode('-', $_GET['tanggal']);
        $y = $tgl[0];
        $m = $tgl[1];
        $lastKode = PembelianBarang::select('kode_pembelian')
            ->whereMonth('tanggal', $m)
            ->whereYear('tanggal', $y)
            ->orderBy('kode_pembelian', 'desc')
            ->skip(0)->take(1)
            ->get();
        if (count($lastKode) == 0) {
            $dateCreate = date_create($_GET['tanggal']);
            $date = date_format($dateCreate, 'my');
            $kode = "PB" . $date . "-0001";
        } else {
            $ex = explode('-', $lastKode[0]->kode_pembelian);
            $no = (int)$ex[1] + 1;
            $newNo = sprintf("%04s", $no);
            $kode = $ex[0] . '-' . $newNo;
        }
        return $kode;
    }

    public function addDetailPembelian()
    {
        $next = $_GET['biggestNo'] + 1;
        $barang = Barang::select('kode_barang', 'nama')->get();
        return view('pembelian.pembelian-barang.tambah-detail-pembelian-barang', ['hapus' => true, 'no' => $next, 'barang' => $barang]);
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
            $totalPpn = 0;
            foreach ($_POST['qty'] as $key => $value) {
                $ttlQty = $ttlQty + $value;
                $total = $total + $_POST['subtotal'][$key];
            }

            if ($request->get('status_ppn') == 'Tanpa') {
                $totalPpn = 0;
            } elseif ($request->get('status_ppn') == 'Belum') {
                $totalPpn = 10 / 100 * $total;
            } else {
                $total = (100 / 110) * $total;
                $totalPpn = 10 / 100 * $total;
            }

            $grandtotal = $total + $totalPpn;

            $newPembelian = new PembelianBarang;
            $newPembelian->kode_pembelian = $request->get('kode_pembelian');
            $newPembelian->kode_supplier = $request->get('kode_supplier');
            $newPembelian->tanggal = $request->get('tanggal');
            $newPembelian->status_ppn = $request->get('status_ppn');
            $newPembelian->jatuh_tempo = $request->get('jatuh_tempo');
            $newPembelian->total_qty = $ttlQty;
            $newPembelian->total = $total;
            $newPembelian->total_ppn = $totalPpn;
            $newPembelian->grandtotal = $grandtotal;
            $newPembelian->terbayar = 0;

            $newPembelian->save();
            //update hutang supplier
            Supplier::where('kode_supplier', $request->get('kode_supplier'))
                            ->update([
                                'hutang' => \DB::raw('hutang+' . $grandtotal),
                            ]);
                            
            //insert ke kartu hutang
            $kartuHutang = new KartuHutang;
            $kartuHutang->tanggal = $request->get('tanggal');
            $kartuHutang->kode_supplier = $request->get('kode_supplier');
            $kartuHutang->kode_transaksi = $request->get('kode_pembelian');
            $kartuHutang->nominal = $grandtotal;
            $kartuHutang->tipe = 'Pembelian';
            $kartuHutang->save();

            foreach ($_POST['kode_barang'] as $key => $value) {
                $ppn = 0;
                $subtotal = $_POST['subtotal'][$key];
                if ($request->get('status_ppn') == 'Tanpa') {
                    $ppn = 0;
                } elseif ($request->get('status_ppn') == 'Belum') {
                    $ppn = 10 / 100 * $_POST['subtotal'][$key];
                } else {
                    $subtotal = (100 / 110) * $subtotal;
                    $ppn = 10 / 100 * $subtotal;
                }

                //save ke tabel detail pembelian
                $newDetail = new DetailPembelianBarang;
                $newDetail->kode_pembelian = $request->get('kode_pembelian');
                $newDetail->kode_barang = $value;
                $newDetail->harga_satuan = $_POST['harga_satuan'][$key];
                $newDetail->qty = $_POST['qty'][$key];
                $newDetail->subtotal = $subtotal;
                $newDetail->ppn = $ppn;

                $newDetail->save();

                //update stock barang
                $barang = Barang::select('stock', 'saldo')->where('kode_barang', $value)->get()[0];

                $updateBarang = Barang::findOrFail($value);
                $updateBarang->stock = $barang->stock + $_POST['qty'][$key];
                $updateBarang->saldo = $barang->saldo + $subtotal;

                $updateBarang->save();

                //insert ke table kartu stock
                $kartuStock = new KartuStock;
                $kartuStock->tanggal = $request->get('tanggal');
                $kartuStock->kode_barang = $value;
                $kartuStock->kode_transaksi = $request->get('kode_pembelian');
                $kartuStock->id_detail = $newDetail->id;
                $kartuStock->qty = $_POST['qty'][$key];
                $kartuStock->nominal = $subtotal;
                $kartuStock->tipe = 'Masuk';
                $kartuStock->save();
            }

            return redirect()->route('pembelian-barang.index')->withStatus('Data berhasil ditambahkan.');
        } catch (\Exception $e) {
            return redirect()->back()->withStatus('Terjadi kesalahan. : ' . $e->getMessage());
        } catch (\Illuminate\Database\QueryException $e) {
            return redirect()->back()->withStatus('Terjadi kesalahan pada database : ' . $e->getMessage());
        }
    }

    public function edit($kode)
    {
        try {
            $this->param['pageInfo'] = 'Pembelian Barang / Edit Data';
            $this->param['btnRight']['text'] = 'Lihat Data';
            $this->param['btnRight']['link'] = route('pembelian-barang.index');
            $this->param['supplier'] = Supplier::get();
            $this->param['barang'] = Barang::get();
            $this->param['pembelian'] = PembelianBarang::find($kode);
            $this->param['detailPembelian'] = DetailPembelianBarang::where('kode_pembelian', $kode)->get();

            return \view('pembelian.pembelian-barang.edit-pembelian-barang', $this->param);
        } catch (\Exception $e) {
            return redirect()->back()->withStatus('Terjadi kesalahan : ' . $e->getMessage());
        } catch (\Illuminate\Database\QueryException $e) {
            return redirect()->back()->withStatus('Terjadi kesalahan pada database : ' . $e->getMessage());
        }
    }

    public function addEditDetailPembelian()
    {
        $fields = array(
            'kode_barang' => 'kode_barang',
            'qty' => 'qty',
            'harga_satuan' => 'harga_satuan',
            'subtotal' => 'subtotal',
            'ppn' => 'ppn',
        );
        $next = $_GET['biggestNo'] + 1;
        $barang = Barang::select('kode_barang', 'nama')->get();
        return view('pembelian.pembelian-barang.edit-detail-pembelian-barang', ['hapus' => true, 'no' => $next, 'barang' => $barang, 'fields' => $fields, 'idDetail' => '0']);
    }

    public function update(Request $request, $kode)
    {
        $validatedData = $request->validate([
            'tanggal' => 'required',
            'kode_supplier' => 'required',
            'kode_barang.*' => 'required',
            'qty.*' => 'required|min:1',
            'harga_satuan.*' => 'required|min:1',
        ]);

        try {

            $pembelianBarang = PembelianBarang::select('tanggal', 'kode_supplier','status_ppn', 'grandtotal')->where('kode_pembelian', $kode)->get()[0];

            $bulanPembelian = date('m-Y', strtotime($pembelianBarang->tanggal));
            $editBulanPembelian = date('m-Y', strtotime($request->get('tanggal')));

            if ($bulanPembelian != $editBulanPembelian) {
                return redirect()->back()->withStatus('Tidak dapat merubah bulan transaksi');
            }

            $statusPpn = $pembelianBarang->status_ppn;
            $grandtotal = $pembelianBarang->grandtotal;
            $kodeSupplier = $pembelianBarang->kode_supplier;

            $newTotalQty = 0;
            $newTotal = 0;
            $newTotalPpn = 0;
            $newGrandtotal = 0;

            foreach ($_POST['kode_barang'] as $key => $value) {
                $ppn = 0;
                if ($statusPpn == 'Sudah') {
                    // subtotal nya dikurangi ppn terlebih dahulu
                    $ppn = 10 / 110 * $_POST['subtotal'][$key];
                    $_POST['subtotal'][$key] = (100 / 110 * $_POST['subtotal'][$key]);

                }
                elseif ($statusPpn == 'Belum') {
                    $ppn = 10 / 100 * $_POST['subtotal'][$key];
                }
                // cek apakah penambahan detail baru atau tidak
                if ($_POST['id_detail'][$key] != 0) { // perubahan pada detail tanpa menambah detail baru
                    $getDetail = DetailPembelianBarang::select('kode_barang', 'qty', 'subtotal')->where('id', $_POST['id_detail'][$key])->get()[0];

                    // cek apakah terdapat perubahan pada detail
                    if ($_POST['kode_barang'][$key] != $getDetail['kode_barang'] || $_POST['qty'][$key] != $getDetail['qty'] || $_POST['subtotal'][$key] != $getDetail['subtotal']) { 

                        if ($_POST['kode_barang'][$key] == $getDetail['kode_barang']) { //jika kode barang tidak berubah, dan yang lain berubah

                            //kembalikan stock & saldo barang
                            Barang::where('kode_barang', $_POST['kode_barang'][$key])
                            ->update([
                                'stock' => \DB::raw('stock-' . $getDetail->qty),
                                'saldo' => \DB::raw('saldo-' . $getDetail->subtotal),
                            ]);

                            //perbarui stock
                            Barang::where('kode_barang', $_POST['kode_barang'][$key])
                                ->update([
                                    'stock' => \DB::raw('stock+' . $_POST['qty'][$key]),
                                    'saldo' => \DB::raw('saldo+' . $_POST['subtotal'][$key]),
                                ]);

                            //update detail
                            DetailPembelianBarang::where('id', $_POST['id_detail'][$key])
                                ->update([
                                    'harga_satuan' => $_POST['harga_satuan'][$key],
                                    'qty' => $_POST['qty'][$key],
                                    'subtotal' => $_POST['subtotal'][$key],
                                    'ppn' => $ppn,
                                ]);
                            // update kartu stock
                            KartuStock::where('id_detail', $_POST['id_detail'][$key])
                                ->where('kode_transaksi', $kode)
                                ->update([
                                    'tanggal' => $_POST['tanggal'],
                                    'qty' => $_POST['qty'][$key],
                                    'nominal' => $_POST['subtotal'][$key],
                                ]);
                        }
                        else { //jika terdapat perubahan pada kode barang 

                            //kembalikan stock & saldo pada kode barang yang lama 
                            Barang::where('kode_barang', $getDetail['kode_barang'])
                            ->update([
                                'stock' => \DB::raw('stock-' . $getDetail->qty),
                                'saldo' => \DB::raw('saldo-' . $getDetail->subtotal),
                            ]);

                            //perbarui stock & saldo kode barang yang baru
                            Barang::where('kode_barang', $_POST['kode_barang'][$key])
                                ->update([
                                    'stock' => \DB::raw('stock+' . $_POST['qty'][$key]),
                                    'saldo' => \DB::raw('saldo+' . $_POST['subtotal'][$key]),
                                ]);

                            //update detail
                            DetailPembelianBarang::where('id', $_POST['id_detail'][$key])
                                ->update([
                                    'kode_barang' => $_POST['kode_barang'][$key],
                                    'harga_satuan' => $_POST['harga_satuan'][$key],
                                    'qty' => $_POST['qty'][$key],
                                    'subtotal' => $_POST['subtotal'][$key],
                                    'ppn' => $ppn,
                                ]);
                            
                            // update kartu stock
                            KartuStock::where('id_detail', $_POST['id_detail'][$key])
                                ->where('kode_transaksi', $kode)
                                ->update([
                                    'tanggal' => $_POST['tanggal'],
                                    'kode_barang' => $_POST['kode_barang'][$key],
                                    'qty' => $_POST['qty'][$key],
                                    'nominal' => $_POST['subtotal'][$key],
                                ]);
                        }
                        
                    }
                    else{ //hanya mengupdate tanggal di kartu stock
                        // update kartu stock
                        KartuStock::where('id_detail', $_POST['id_detail'][$key])
                        ->where('kode_transaksi', $kode)
                        ->update([
                            'tanggal' => $_POST['tanggal'],
                        ]);
                    }
                    
                } 
                else { //perubahan pada detail dengan menambah detail baru

                    //update barang
                    Barang::where('kode_barang', $_POST['kode_barang'][$key])
                        ->update([
                            'stock' => \DB::raw('stock+' . $_POST['qty'][$key]),
                            'saldo' => \DB::raw('saldo+' . $_POST['subtotal'][$key]),
                        ]);

                    //insert to detail
                    $newDetail = DetailPembelianBarang::create([
                            'kode_pembelian' => $_POST['kode_pembelian'],
                            'kode_barang' => $_POST['kode_barang'][$key],
                            'harga_satuan' => $_POST['harga_satuan'][$key],
                            'qty' => $_POST['qty'][$key],
                            'subtotal' => $_POST['subtotal'][$key],
                            'ppn' => $ppn,
                        ]);
                    
                    // update kartu stock
                    KartuStock::insert([
                            'tanggal' => $_POST['tanggal'],
                            'kode_barang' => $_POST['kode_barang'][$key],
                            'kode_transaksi' => $_POST['kode_pembelian'],
                            'id_detail' => $newDetail->id,
                            'qty' => $_POST['qty'][$key],
                            'nominal' => $_POST['subtotal'][$key],
                            'tipe' => 'Masuk',
                        ]);
                }
                $newTotalQty = $newTotalQty + $_POST['qty'][$key];
                $newTotal = $newTotal + $_POST['subtotal'][$key];
                $newTotalPpn = $newTotalPpn + $ppn;
                $newGrandtotal = $newTotal + $newTotalPpn;
            }

            if (isset($_POST['id_delete'])) {
                foreach ($_POST['id_delete'] as $key => $value) {
                    $getDetail = DetailPembelianBarang::select('kode_barang', 'qty', 'subtotal')->where('id', $value)->get()[0];

                    //update barang
                    Barang::where('kode_barang', $getDetail->kode_barang)
                        ->update([
                            'stock' => \DB::raw('stock-' . $getDetail->qty),
                            'saldo' => \DB::raw('saldo-' . $getDetail->subtotal),
                        ]);

                    //delete detail
                    DetailPembelianBarang::where('id', $value)->delete();

                    //delete kartu stock
                    KartuStock::where('id_detail', $value)->where('tipe', 'Masuk')->delete();
                }
            }

            //update pembelian
            PembelianBarang::where('kode_pembelian', $_POST['kode_pembelian'])
                ->update([
                    'tanggal' => $_POST['tanggal'],
                    'total_qty' => $newTotalQty,
                    'total' => $newTotal,
                    'total_ppn' => $newTotalPpn,
                    'grandtotal' => $newGrandtotal,
                    'kode_supplier' => $_POST['kode_supplier'],
                ]);

            Supplier::where('kode_supplier', $kodeSupplier)
                        ->update([
                            'hutang' => \DB::raw('hutang-' . $grandtotal),
                        ]);
            
            Supplier::where('kode_supplier', $_POST['kode_supplier'])
                        ->update([
                            'hutang' => \DB::raw('hutang+' . $newGrandtotal),
                        ]);
            
            //update kartu hutang
            KartuHutang::where('kode_transaksi', $_POST['kode_pembelian'])
                ->update([
                    'tanggal' => $_POST['tanggal'],
                    'kode_supplier' => $_POST['kode_supplier'],
                    'nominal' => $newGrandtotal,
                ]);
            
            return redirect()->route('pembelian-barang.index')->withStatus('Data berhasil diperbarui.');
        } catch (\Exception $e) {
            return redirect()->back()->withStatus('Terjadi kesalahan. : ' . $e->getMessage());
        } catch (\Illuminate\Database\QueryException $e) {
            return redirect()->back()->withStatus('Terjadi kesalahan pada database : ' . $e->getMessage());
        }
    }

    public function destroy($kode)
    {
        try {
            $pembelianBarang = PembelianBarang::findOrFail($kode);

            $detail = DetailPembelianBarang::where('kode_pembelian', $kode)->get();

            foreach ($detail as $key => $value) {
                Barang::where('kode_barang', $value->kode_barang)
                        ->update([
                            'stock' => \DB::raw('stock-' . $value->qty),
                            'saldo' => \DB::raw('saldo-' . $value->subtotal),
                        ]);

                DetailPembelianBarang::where('id', $value->id)->delete();

                // delete kartu stock
                KartuStock::where('id_detail', $value->id)->where('tipe', 'Masuk')->delete();
            }

            KartuHutang::where('kode_transaksi', $kode)->delete();

            Supplier::where('kode_supplier', $pembelianBarang->kode_supplier)
                        ->update([
                            'hutang' => \DB::raw('hutang-' . $pembelianBarang->grandtotal),
                        ]);

            $pembelianBarang->delete();

            return redirect()->route('pembelian-barang.index')->withStatus('Data berhasil dihapus.');
        } catch (\Exception $e) {
            return redirect()->back()->withStatus('Terjadi kesalahan. : ' . $e->getMessage());
        } catch (\Illuminate\Database\QueryException $e) {
            return redirect()->back()->withStatus('Terjadi kesalahan pada database : ' . $e->getMessage());
        }
    }

    public function reportPembelianBarang()
    {
        try {
            $this->param['pageInfo'] = 'Pembelian Barang / List Pembelian Barang';
            $this->param['supplier'] = Supplier::get();
            $this->param['barang'] = Barang::get();
            $this->param['report'] = null;
        } catch (\Exception $e) {
            return redirect()->back()->withStatus('Terjadi kesalahan. : ' . $e->getMessage());
        }
        catch(\Illuminate\Database\QueryException $e){
            return redirect()->back()->withStatus('Terjadi kesalahan pada database. : ' . $e->getMessage());
        }
        return \view('pembelian.pembelian-barang.laporan-pembelian-barang', $this->param);
    }

    public function getReport(Request $request)
    {
        try {
            $this->param['pageInfo'] = 'Pembelian Barang / List Pembelian Barang';
            $this->param['supplier'] = Supplier::get();
            $this->param['barang'] = Barang::get();
            $whereSupplier = null;
            if($request->get('kode_supplier') != ''){
                $whereSupplier = 'pembelian_barang.kode_supplier';
            }
            if($request->nilai == 'Rekap'){
                $this->param['nilai'] = 'Rekap';
                $this->param['report'] = PembelianBarang::select('pembelian_barang.*', 's.kode_supplier', 's.nama')
                                    ->join('supplier AS s', 's.kode_supplier', 'pembelian_barang.kode_supplier')
                                    ->where($whereSupplier, $request->get('kode_supplier'))
                                    ->whereBetween('tanggal', [$request->get('start'), $request->get('end')])
                                    ->orderBy('pembelian_barang.'.$request->get('order'), 'ASC')
                                    ->get();
            }
            else{
                $this->param['nilai'] = 'Detail';
                $this->param['report'] = PembelianBarang::select('pembelian_barang.*', 's.kode_supplier', 's.nama', 'detail.*', 'b.nama', 'b.satuan')
                    ->join('detail_pembelian_barang AS detail', 'detail.kode_pembelian', 'pembelian_barang.kode_pembelian')
                    ->join('supplier AS s', 's.kode_supplier', 'pembelian_barang.kode_supplier')
                    ->join('barang AS b', 'b.kode_barang', 'detail.kode_barang')
                    ->where($whereSupplier, $request->get('kode_supplier'))
                    ->whereBetween('tanggal', [$request->get('start'), $request->get('end')])
                    ->orderBy('pembelian_barang.'.$request->get('order'), 'ASC')
                    ->get();
            }    
        } catch (\Exception $e) {
            return redirect()->back()->withError('Terjadi kesalahan. : ' . $e->getMessage());
        }
        catch(\Illuminate\Database\QueryException $e){
            return redirect()->back()->withError('Terjadi kesalahan pada database. : ' . $e->getMessage());
        }
        return \view('pembelian.pembelian-barang.laporan-pembelian-barang', $this->param);
    }

    public function printReport(Request $request)
    {
        try {
            if($request->nilai == 'Rekap'){
                $this->param['nilai'] = 'Rekap';
                $this->param['report'] = PembelianBarang::select('pembelian_barang.*', 's.kode_supplier', 's.nama')
                                    ->join('supplier AS s', 's.kode_supplier', 'pembelian_barang.kode_supplier')
                                    ->where('pembelian_barang.kode_supplier', $request->get('kode_supplier'))
                                    ->whereBetween('tanggal', [$request->get('start'), $request->get('end')])
                                    ->orderBy('pembelian_barang.'.$request->get('order'), 'ASC')
                                    ->get();
            }
            else{
                $this->param['nilai'] = 'Detail';
                $this->param['report'] = PembelianBarang::select('pembelian_barang.*', 's.kode_supplier', 's.nama', 'detail.*', 'b.nama', 'b.satuan')
                    ->join('detail_pembelian_barang AS detail', 'detail.kode_pembelian', 'pembelian_barang.kode_pembelian')
                    ->join('supplier AS s', 's.kode_supplier', 'pembelian_barang.kode_supplier')
                    ->join('barang AS b', 'b.kode_barang', 'detail.kode_barang')
                    ->where('pembelian_barang.kode_supplier', $request->get('kode_supplier'))
                    ->whereBetween('tanggal', [$request->get('start'), $request->get('end')])
                    ->orderBy('pembelian_barang.'.$request->get('order'), 'ASC')
                    ->get();
            }    
        } catch (\Exception $e) {
            return redirect()->back()->withError('Terjadi kesalahan. : ' . $e->getMessage());
        }
        catch(\Illuminate\Database\QueryException $e){
            return redirect()->back()->withError('Terjadi kesalahan pada database. : ' . $e->getMessage());
        }
        return \view('pembelian.pembelian-barang.print-laporan-pembelian-barang', $this->param);
    }
}
