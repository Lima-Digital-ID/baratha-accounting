<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use \App\Models\PemakaianBarang;
use \App\Models\DetailPemakaianBarang;
use \App\Models\Supplier;
use \App\Models\Barang;
use \App\Models\KartuStock;
use App\Models\KodeBiaya;
use \App\Models\Jurnal;

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
            $start = $request->get('start');
            $end = $request->get('end');
            // $pemakaianBarang = PemakaianBarang::where('kode_pemakaian', 'LIKE', "%$keyword%")->orWhere('kode_supplier', 'LIKE', "%$keyword%")->paginate(10);
            if ($keyword) {
                $pemakaianBarang = PemakaianBarang::where('kode_pemakaian', 'LIKE', "%$keyword%")->paginate(10);
            }elseif($keyword == null && $start != null && $end != null){
                $pemakaianBarang = PemakaianBarang::whereBetween('tanggal', [$start, $end])->paginate(10);
            }elseif($keyword && $start && $end){
                $pemakaianBarang = PemakaianBarang::whereBetween('tanggal', [$start, $end])->where('kode_pemakaian', 'LIKE', "%$keyword%")->paginate(10);
            }else {
                $pemakaianBarang = PemakaianBarang::paginate(10);
            }
        } catch (\Illuminate\Database\QueryException $e) {
            return $e;
            return redirect()->back()->withErrors('Terjadi Kesalahan');
            // return redirect()->back()->withStatus('Terjadi Kesalahan');
        }

        return \view('persediaan.pemakaian-barang.list-pemakaian-barang', ['pemakaianBarang' => $pemakaianBarang], $this->param);
    }

    public function create()
    {
        try {
            $this->param['pageInfo'] = 'Pemakaian Barang / Tambah Pemakaian Barang';
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
            'qty.*' => 'required|numeric|gt:0|lte:stock.*',
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

                // save jurnal pemakaian
                $getKodeBiaya = KodeBiaya::select('nama','kode_rekening')->where('kode_biaya', $_POST['kode_biaya'][$key])->get()[0];

                $newJurnal = new Jurnal;
                $newJurnal->tanggal = $request->get('tanggal');
                $newJurnal->jenis_transaksi = 'Pemakaian';
                $newJurnal->kode_transaksi = $request->get('kode_pemakaian');
                $newJurnal->keterangan = 'Pemakaian Barang ' . $getKodeBiaya->nama;
                $newJurnal->kode = '1130.0001';
                $newJurnal->lawan = $getKodeBiaya->kode_rekening;
                $newJurnal->tipe = 'Kredit';
                $newJurnal->nominal = $subtotal;
                $newJurnal->id_detail = $newDetail->id;
                $newJurnal->save();
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
            $this->param['detailPemakaian'] = \DB::table('detail_pemakaian_barang AS dt')
                                                ->select('dt.id','dt.kode_barang', 'dt.qty', 'dt.subtotal', 'dt.keterangan', 'dt.kode_biaya', \DB::raw('b.stock + dt.qty AS stock'), \DB::raw('b.saldo + dt.subtotal AS saldo'))
                                                ->join('barang AS b', 'b.kode_barang', '=', 'dt.kode_barang')
                                                ->where('kode_pemakaian', $kode)
                                                ->get();

            return \view('persediaan.pemakaian-barang.edit-pemakaian-barang', $this->param);
        } catch (\Exception $e) {
            return redirect()->back()->withStatus('Terjadi kesalahan : ' . $e->getMessage());
        } catch (\Illuminate\Database\QueryException $e) {
            return redirect()->back()->withStatus('Terjadi kesalahan pada database : ' . $e->getMessage());
        }
    }

    public function addEditDetailPemakaian()
    {   
        $fields = array(
            'kode_barang' => 'kode_barang',
            'stock' => 'stock',
            'saldo' => 'saldo',
            'qty' => 'qty',
            'kode_biaya' => 'kode_biaya',
            'keterangan' => 'keterangan',
        );
        $next = $_GET['biggestNo']+1;
        $barang = Barang::select('kode_barang','nama')->get();
        $kodeBiaya = KodeBiaya::select('kode_biaya','nama')->get();
        return view('persediaan.pemakaian-barang.edit-detail-pemakaian-barang',['hapus' => true, 'no' => $next, 'barang' => $barang,'kodeBiaya' => $kodeBiaya, 'fields' => $fields,'idDetail' => '0']);
    }

    public function update(Request $request, $kode)
    {
        $validatedData = $request->validate(
            [
                'tanggal' => 'required',
                'kode_barang.*' => 'required',
                'qty.*' => 'required|numeric|gt:0|lte:stock.*',
                'kode_biaya.*' => 'required',
            ],
            [
                'lte' => 'Quantity tidak boleh melebihi stock.'
            ] 
        );

        try {

            $pemakaianBarang = PemakaianBarang::select('tanggal', 'total_pemakaian')->where('kode_pemakaian', $kode)->get()[0];

            $bulanPemakaian = date('m-Y', strtotime($pemakaianBarang->tanggal));
            $editBulanPemakaian = date('m-Y', strtotime($request->get('tanggal')));

            if ($bulanPemakaian != $editBulanPemakaian) {
                return redirect()->back()->withStatus('Tidak dapat merubah bulan transaksi');
            }

            $totalPemakaian = $pemakaianBarang->total_pemakaian;

            $newTotalQty = 0;
            $newTotalPemakaian = 0;

            foreach ($_POST['kode_barang'] as $key => $value) {

                $subtotal = ($_POST['saldo'][$key] / $_POST['stock'][$key]) * $_POST['qty'][$key];
                // cek apakah penambahan detail baru atau tidak
                if ($_POST['id_detail'][$key] != 0) { // perubahan pada detail tanpa menambah detail baru
                    $getDetail = DetailPemakaianBarang::select('kode_barang', 'qty', 'subtotal', 'kode_biaya', 'keterangan')->where('id', $_POST['id_detail'][$key])->get()[0];


                    // cek apakah terdapat perubahan pada detail
                    if ($_POST['kode_barang'][$key] != $getDetail['kode_barang'] || $_POST['qty'][$key] != $getDetail['qty'] || $_POST['kode_biaya'][$key] != $getDetail['kode_biaya'] || $_POST['keterangan'][$key] != $getDetail['keterangan']) { 
                    

                        if ($_POST['kode_barang'][$key] == $getDetail['kode_barang']) { //jika kode barang tidak berubah, dan yang lain berubah

                            //kembalikan stock & saldo barang
                            Barang::where('kode_barang', $_POST['kode_barang'][$key])
                            ->update([
                                'stock' => \DB::raw('stock+' . $getDetail->qty),
                                'saldo' => \DB::raw('saldo+' . $getDetail->subtotal),
                            ]);

                            //perbarui stock
                            Barang::where('kode_barang', $_POST['kode_barang'][$key])
                                ->update([
                                    'stock' => \DB::raw('stock-' . $_POST['qty'][$key]),
                                    'saldo' => \DB::raw('saldo-' . $subtotal),
                                ]);

                            //update detail
                            DetailPemakaianBarang::where('id', $_POST['id_detail'][$key])
                                ->update([
                                    'qty' => $_POST['qty'][$key],
                                    'subtotal' => $subtotal,
                                    'kode_biaya' => $_POST['kode_biaya'][$key],
                                    'keterangan' => $_POST['keterangan'][$key],
                                ]);
                            // update kartu stock
                            KartuStock::where('id_detail', $_POST['id_detail'][$key])
                                ->where('kode_transaksi', $kode)
                                ->update([
                                    'tanggal' => $_POST['tanggal'],
                                    'qty' => $_POST['qty'][$key],
                                    'nominal' => $subtotal,
                                ]);
                        }
                        else { //jika terdapat perubahan pada kode barang 

                            //kembalikan stock & saldo pada kode barang yang lama 
                            Barang::where('kode_barang', $getDetail['kode_barang'])
                            ->update([
                                'stock' => \DB::raw('stock+' . $getDetail->qty),
                                'saldo' => \DB::raw('saldo+' . $getDetail->subtotal),
                            ]);

                            //perbarui stock & saldo kode barang yang baru
                            Barang::where('kode_barang', $_POST['kode_barang'][$key])
                                ->update([
                                    'stock' => \DB::raw('stock-' . $_POST['qty'][$key]),
                                    'saldo' => \DB::raw('saldo-' . $subtotal),
                                ]);

                            //update detail
                            DetailPemakaianBarang::where('id', $_POST['id_detail'][$key])
                                ->update([
                                    'kode_barang' => $_POST['kode_barang'][$key],
                                    'qty' => $_POST['qty'][$key],
                                    'subtotal' => $subtotal,
                                    'kode_biaya' => $_POST['kode_biaya'][$key],
                                    'keterangan' => $_POST['keterangan'][$key],
                                ]);
                            
                            // update kartu stock
                            KartuStock::where('id_detail', $_POST['id_detail'][$key])
                                ->where('kode_transaksi', $kode)
                                ->update([
                                    'tanggal' => $_POST['tanggal'],
                                    'kode_barang' => $_POST['kode_barang'][$key],
                                    'qty' => $_POST['qty'][$key],
                                    'nominal' => $subtotal,
                                ]);
                        }

                        //update jurnal pemakaian
                        $getKodeBiaya = KodeBiaya::select('nama','kode_rekening')->where('kode_biaya', $_POST['kode_biaya'][$key])->get()[0];
                        
                        Jurnal::where('kode_transaksi', $kode)->where('id_detail', $_POST['id_detail'][$key])
                        ->update([
                            'tanggal' => $_POST['tanggal'],
                            'keterangan' => 'Pemakaian Barang ' . $getKodeBiaya->nama,
                            'lawan' => $getKodeBiaya->kode_rekening,
                            'nominal' => $subtotal,
                        ]);
                    }
                    else{ //hanya update tanggal di kartu stock apabila tidak ada perubahan pada detail
                        KartuStock::where('id_detail', $_POST['id_detail'][$key])
                                ->where('kode_transaksi', $kode)
                                ->update([
                                    'tanggal' => $_POST['tanggal'],
                                ]);

                        //update jurnal pemakaian
                        Jurnal::where('kode_transaksi', $kode)->where('id_detail', $_POST['id_detail'][$key])
                        ->update([
                            'tanggal' => $_POST['tanggal'],
                        ]);
                    }
                    
                } 
                else { //perubahan pada detail dengan menambah detail baru

                    //update barang
                    Barang::where('kode_barang', $_POST['kode_barang'][$key])
                        ->update([
                            'stock' => \DB::raw('stock-' . $_POST['qty'][$key]),
                            'saldo' => \DB::raw('saldo-' . $subtotal),
                        ]);

                    //insert to detail
                    $newDetail = DetailPemakaianBarang::create([
                        'kode_pemakaian' => $kode,
                        'kode_barang' => $_POST['kode_barang'][$key],
                        'qty' => $_POST['qty'][$key],
                        'subtotal' => $subtotal,
                        'kode_biaya' => $_POST['kode_biaya'][$key],
                        'keterangan' => $_POST['keterangan'][$key],
                    ]);
                    
                    // update kartu stock
                    KartuStock::insert([
                            'tanggal' => $_POST['tanggal'],
                            'kode_barang' => $_POST['kode_barang'][$key],
                            'kode_transaksi' => $kode,
                            'id_detail' => $newDetail->id,
                            'qty' => $_POST['qty'][$key],
                            'nominal' => $subtotal,
                            'tipe' => 'Keluar',
                        ]);

                    // save jurnal pemakaian
                    $getKodeBiaya = KodeBiaya::select('nama','kode_rekening')->where('kode_biaya', $_POST['kode_biaya'][$key])->get()[0];

                    $newJurnal = new Jurnal;
                    $newJurnal->tanggal = $_POST['tanggal'];
                    $newJurnal->jenis_transaksi = 'Pemakaian';
                    $newJurnal->kode_transaksi = $kode;
                    $newJurnal->keterangan = 'Pemakaian Barang ' . $getKodeBiaya->nama;
                    $newJurnal->kode = '1130.0001';
                    $newJurnal->lawan = $getKodeBiaya->kode_rekening;
                    $newJurnal->tipe = 'Kredit';
                    $newJurnal->nominal = $subtotal;
                    $newJurnal->id_detail = $newDetail->id;
                    $newJurnal->save();
                }
                $newTotalQty = $newTotalQty + $_POST['qty'][$key];
                $newTotalPemakaian = $newTotalPemakaian + $subtotal;
            }

            if (isset($_POST['id_delete'])) {
                foreach ($_POST['id_delete'] as $key => $value) {
                    $getDetail = DetailPemakaianBarang::select('kode_barang', 'qty', 'subtotal')->where('id', $value)->get()[0];

                    //update barang
                    Barang::where('kode_barang', $getDetail->kode_barang)
                        ->update([
                            'stock' => \DB::raw('stock+' . $getDetail->qty),
                            'saldo' => \DB::raw('saldo+' . $getDetail->subtotal),
                        ]);

                    //delete detail
                    DetailPemakaianBarang::where('id', $value)->delete();

                    //delete kartu stock
                    KartuStock::where('id_detail', $value)->where('tipe', 'Keluar')->delete();
                    // delete jurnal
                    Jurnal::where('kode_transaksi', $kode)->where('id_detail', $value)->delete();
                }
            }

            //update pemakaian
            PemakaianBarang::where('kode_pemakaian', $kode)
                ->update([
                    'tanggal' => $_POST['tanggal'],
                    'total_qty' => $newTotalQty,
                    'total_pemakaian' => $newTotalPemakaian,
                ]);

            return redirect()->route('pemakaian-barang.index')->withStatus('Data berhasil diperbarui.');
        } catch (\Exception $e) {
            return redirect()->back()->withStatus('Terjadi kesalahan. : ' . $e->getMessage());
        } catch (\Illuminate\Database\QueryException $e) {
            return redirect()->back()->withStatus('Terjadi kesalahan pada database : ' . $e->getMessage());
        }
    }

    public function destroy($kode)
    {
        try {
            $pemakaianBarang = PemakaianBarang::findOrFail($kode);

            $detail = DetailPemakaianBarang::where('kode_pemakaian', $kode)->get();

            foreach ($detail as $key => $value) {
                Barang::where('kode_barang', $value->kode_barang)
                        ->update([
                            'stock' => \DB::raw('stock+' . $value->qty),
                            'saldo' => \DB::raw('saldo+' . $value->subtotal),
                        ]);

                DetailPemakaianBarang::where('id', $value->id)->delete();

                // delete kartu stock
                KartuStock::where('id_detail', $value->id)->where('tipe', 'Keluar')->delete();
            }

            $pemakaianBarang->delete();

            return redirect()->route('pemakaian-barang.index')->withStatus('Data berhasil dihapus.');
        } catch (\Exception $e) {
            return redirect()->back()->withStatus('Terjadi kesalahan. : ' . $e->getMessage());
        } catch (\Illuminate\Database\QueryException $e) {
            return redirect()->back()->withStatus('Terjadi kesalahan pada database : ' . $e->getMessage());
        }
    }

    public function reportPemakaianBarang()
    {
        try {
            $this->param['pageInfo'] = 'Pemakaian Barang / List Pemakaian Barang';
            $this->param['kode_biaya'] = KodeBiaya::get();
            $this->param['barang'] = Barang::get();
            $this->param['report'] = null;
        } catch (\Exception $e) {
            return redirect()->back()->withStatus('Terjadi kesalahan. : ' . $e->getMessage());
        }
        catch(\Illuminate\Database\QueryException $e){
            return redirect()->back()->withStatus('Terjadi kesalahan pada database. : ' . $e->getMessage());
        }
        return \view('persediaan.pemakaian-barang.laporan-pemakaian-barang', $this->param);
    }

    public function getReport(Request $request)
    {
        try {
            $this->param['pageInfo'] = 'Pemakaian Barang / List Pemakaian Barang';
            $this->param['kode_biaya'] = KodeBiaya::get();
            $this->param['barang'] = Barang::get();
            $order = null;
            if($request->get('order') == 'tanggal'){
                $order = 'pemakaian_barang.'.$request->get('order');
            }
            else{
                $order = 'detail.'.$request->get('order');
            }
            if($request->kode_stok != null && $request->kode_biaya == null){ //Jika kode stok dipilih dan kode biaya tidak dipilih
                $this->param['report'] = PemakaianBarang::select(
                                            'pemakaian_barang.kode_pemakaian',
                                            'pemakaian_barang.tanggal',
                                            'detail.kode_pemakaian',
                                            'detail.kode_barang',
                                            'detail.qty',
                                            'detail.subtotal',
                                            'detail.kode_biaya',
                                            'detail.keterangan',
                                            'b.nama',
                                            'b.satuan'
                                        )
                                        ->join('detail_pemakaian_barang AS detail', 'detail.kode_pemakaian', 'pemakaian_barang.kode_pemakaian')
                                        ->join('barang AS b', 'b.kode_barang', 'detail.kode_barang')
                                        ->whereBetween('pemakaian_barang.tanggal', [$request->get('start'), $request->get('end')])
                                        ->where('detail.kode_barang', $request->get('kode_stok'))
                                        ->orderBy($order, 'ASC')
                                        ->get();
            }
            elseif($request->kode_stok == null && $request->kode_biaya != null){ //Jika kode stok tidak dipilih dan kode biaya dipilih
                $this->param['report'] = PemakaianBarang::select(
                                            'pemakaian_barang.kode_pemakaian',
                                            'pemakaian_barang.tanggal',
                                            'detail.kode_pemakaian',
                                            'detail.kode_barang',
                                            'detail.qty',
                                            'detail.subtotal',
                                            'detail.kode_biaya',
                                            'detail.keterangan',
                                            'b.nama',
                                            'b.satuan'
                                        )
                                        ->join('detail_pemakaian_barang AS detail', 'detail.kode_pemakaian', 'pemakaian_barang.kode_pemakaian')
                                        ->join('barang AS b', 'b.kode_barang', 'detail.kode_barang')
                                        ->whereBetween('pemakaian_barang.tanggal', [$request->get('start'), $request->get('end')])
                                        ->where('detail.kode_biaya', $request->kode_biaya)
                                        ->orderBy($order, 'ASC')
                                        ->get();
            }
            elseif($request->kode_stok != null && $request->kode_biaya != null){ //Jika kode stok dan kode biaya dipilih
                $this->param['report'] = PemakaianBarang::select(
                                            'pemakaian_barang.kode_pemakaian',
                                            'pemakaian_barang.tanggal',
                                            'detail.kode_pemakaian',
                                            'detail.kode_barang',
                                            'detail.qty',
                                            'detail.subtotal',
                                            'detail.kode_biaya',
                                            'detail.keterangan',
                                            'b.nama',
                                            'b.satuan'
                                        )
                                        ->join('detail_pemakaian_barang AS detail', 'detail.kode_pemakaian', 'pemakaian_barang.kode_pemakaian')
                                        ->join('barang AS b', 'b.kode_barang', 'detail.kode_barang')
                                        ->whereBetween('pemakaian_barang.tanggal', [$request->get('start'), $request->get('end')])
                                        ->where([
                                            ['detail.kode_biaya', $request->get('kode_biaya')],
                                            ['detail.kode_barang', $request->get('kode_stok')]
                                        ])
                                        ->orderBy($order, 'ASC')
                                        ->get();
            }
            else{ //Jika tidak memilih kode stok dan kode biaya
                $this->param['report'] = PemakaianBarang::select(
                                            'pemakaian_barang.kode_pemakaian',
                                            'pemakaian_barang.tanggal',
                                            'detail.kode_pemakaian',
                                            'detail.kode_barang',
                                            'detail.qty',
                                            'detail.subtotal',
                                            'detail.kode_biaya',
                                            'detail.keterangan',
                                            'b.nama',
                                            'b.satuan'
                                        )
                                        ->join('detail_pemakaian_barang AS detail', 'detail.kode_pemakaian', 'pemakaian_barang.kode_pemakaian')
                                        ->join('barang AS b', 'b.kode_barang', 'detail.kode_barang')
                                        ->whereBetween('pemakaian_barang.tanggal', [$request->get('start'), $request->get('end')])
                                        ->orderBy($order, 'ASC')
                                        ->get();
            }
            return \view('persediaan.pemakaian-barang.laporan-pemakaian-barang', $this->param);
        } catch (\Exception $e) {
            return redirect()->back()->withError('Terjadi kesalahan. : ' . $e->getMessage());
        }
        catch(\Illuminate\Database\QueryException $e){
            return redirect()->back()->withError('Terjadi kesalahan pada database. : ' . $e->getMessage());
        }
    }

    public function printReport(Request $request)
    {
        try {
            $order = null;
            if($request->get('order') == 'tanggal'){
                $order = 'pemakaian_barang.'.$request->get('order');
            }
            else{
                $order = 'detail.'.$request->get('order');
            }
            if($request->kode_stok != null && $request->kode_biaya == null){ //Jika kode stok dipilih dan kode biaya tidak dipilih
                $this->param['report'] = PemakaianBarang::select(
                                            'pemakaian_barang.kode_pemakaian',
                                            'pemakaian_barang.tanggal',
                                            'detail.kode_pemakaian',
                                            'detail.kode_barang',
                                            'detail.qty',
                                            'detail.subtotal',
                                            'detail.kode_biaya',
                                            'detail.keterangan',
                                            'b.nama',
                                            'b.satuan'
                                        )
                                        ->join('detail_pemakaian_barang AS detail', 'detail.kode_pemakaian', 'pemakaian_barang.kode_pemakaian')
                                        ->join('barang AS b', 'b.kode_barang', 'detail.kode_barang')
                                        ->whereBetween('pemakaian_barang.tanggal', [$request->get('start'), $request->get('end')])
                                        ->where('detail.kode_barang', $request->get('kode_stok'))
                                        ->orderBy($order, 'ASC')
                                        ->get();
            }
            elseif($request->kode_stok == null && $request->kode_biaya != null){ //Jika kode stok tidak dipilih dan kode biaya dipilih
                $this->param['report'] = PemakaianBarang::select(
                                            'pemakaian_barang.kode_pemakaian',
                                            'pemakaian_barang.tanggal',
                                            'detail.kode_pemakaian',
                                            'detail.kode_barang',
                                            'detail.qty',
                                            'detail.subtotal',
                                            'detail.kode_biaya',
                                            'detail.keterangan',
                                            'b.nama',
                                            'b.satuan'
                                        )
                                        ->join('detail_pemakaian_barang AS detail', 'detail.kode_pemakaian', 'pemakaian_barang.kode_pemakaian')
                                        ->join('barang AS b', 'b.kode_barang', 'detail.kode_barang')
                                        ->whereBetween('pemakaian_barang.tanggal', [$request->get('start'), $request->get('end')])
                                        ->where('detail.kode_biaya', $request->kode_biaya)
                                        ->orderBy($order, 'ASC')
                                        ->get();
            }
            elseif($request->kode_stok != null && $request->kode_biaya != null){ //Jika kode stok dan kode biaya dipilih
                $this->param['report'] = PemakaianBarang::select(
                                            'pemakaian_barang.kode_pemakaian',
                                            'pemakaian_barang.tanggal',
                                            'detail.kode_pemakaian',
                                            'detail.kode_barang',
                                            'detail.qty',
                                            'detail.subtotal',
                                            'detail.kode_biaya',
                                            'detail.keterangan',
                                            'b.nama',
                                            'b.satuan'
                                        )
                                        ->join('detail_pemakaian_barang AS detail', 'detail.kode_pemakaian', 'pemakaian_barang.kode_pemakaian')
                                        ->join('barang AS b', 'b.kode_barang', 'detail.kode_barang')
                                        ->whereBetween('pemakaian_barang.tanggal', [$request->get('start'), $request->get('end')])
                                        ->where([
                                            ['detail.kode_biaya', $request->get('kode_biaya')],
                                            ['detail.kode_barang', $request->get('kode_stok')]
                                        ])
                                        ->orderBy($order, 'ASC')
                                        ->get();
            }
            else{ //Jika tidak memilih kode stok dan kode biaya
                $this->param['report'] = PemakaianBarang::select(
                                            'pemakaian_barang.kode_pemakaian',
                                            'pemakaian_barang.tanggal',
                                            'detail.kode_pemakaian',
                                            'detail.kode_barang',
                                            'detail.qty',
                                            'detail.subtotal',
                                            'detail.kode_biaya',
                                            'detail.keterangan',
                                            'b.nama',
                                            'b.satuan'
                                        )
                                        ->join('detail_pemakaian_barang AS detail', 'detail.kode_pemakaian', 'pemakaian_barang.kode_pemakaian')
                                        ->join('barang AS b', 'b.kode_barang', 'detail.kode_barang')
                                        ->whereBetween('pemakaian_barang.tanggal', [$request->get('start'), $request->get('end')])
                                        ->orderBy($order, 'ASC')
                                        ->get();
            }
            return \view('persediaan.pemakaian-barang.print-laporan-pemakaian-barang', $this->param);
        } catch (\Exception $e) {
            return redirect()->back()->withError('Terjadi kesalahan. : ' . $e->getMessage());
        }
        catch(\Illuminate\Database\QueryException $e){
            return redirect()->back()->withError('Terjadi kesalahan pada database. : ' . $e->getMessage());
        }
    }
}
