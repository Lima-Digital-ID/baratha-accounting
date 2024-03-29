<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use \App\Models\PenjualanLain;
use \App\Models\Customer;
use \App\Models\KartuPiutang;
use \App\Models\Jurnal;
use \App\Models\LogActivity;
use Illuminate\Support\Facades\Auth;

class PenjualanCateringController extends Controller
{
    public function index(Request $request)
    {
        $this->param['pageInfo'] = 'Penjualan Catering / List Penjualan Catering';
        $this->param['btnRight']['text'] = 'Tambah Data';
        $this->param['btnRight']['link'] = route('penjualan-catering.create');

        try {
            $keyword = $request->get('keyword');
            if ($keyword) {
                $penjualanCatering = PenjualanLain::with('customer')->where('tipe_penjualan','catering')->where('kode_penjualan', 'LIKE', "%$keyword%")->orWhere('kode_customer', 'LIKE', "%$keyword%")->paginate(10);
            } else {
                $penjualanCatering = PenjualanLain::with('customer')->where('tipe_penjualan','catering')->paginate(10);
            }
        } catch (\Illuminate\Database\QueryException $e) {
            return redirect()->back()->withStatus('Terjadi Kesalahan');
        }

        return \view('penjualan.penjualan-catering.list-penjualan-catering', ['penjualanCatering' => $penjualanCatering], $this->param);
    }

    public function create()
    {
        try {
            $this->param['pageInfo'] = 'Penjualan Catering / List Penjualan Catering';
            $this->param['btnRight']['text'] = 'Lihat Data';
            $this->param['btnRight']['link'] = route('penjualan-catering.index');
            $this->param['customer'] = Customer::get();

            $lastKode = PenjualanLain::select('kode_penjualan')
            ->whereMonth('tanggal', date('m'))
            ->whereYear('tanggal', date('Y'))
            ->orderBy('kode_penjualan', 'desc')
            ->skip(0)->take(1)
            ->get();
            if (count($lastKode) == 0) {
                // $dateCreate = date_create($_GET['tanggal']);
                $date = date('my');
                $kode = "PJ" . $date . "-0001";
            } else {
                $ex = explode('-', $lastKode[0]->kode_penjualan);
                $no = (int)$ex[1] + 1;
                $newNo = sprintf("%04s", $no);
                $kode = $ex[0] . '-' . $newNo;
            }
            $this->param['kodePenjualan'] = $kode;

        } catch (\Exception $e) {
            return redirect()->back()->withStatus('Terjadi kesalahan. : ' . $e->getMessage());
        }

        return \view('penjualan.penjualan-catering.tambah-penjualan-catering', $this->param);
    }

    public function getKode()
    {
        $tgl = explode('-', $_GET['tanggal']);
        $y = $tgl[0];
        $m = $tgl[1];
        $lastKode = PenjualanLain::select('kode_penjualan')
            ->whereMonth('tanggal', $m)
            ->whereYear('tanggal', $y)
            ->orderBy('kode_penjualan', 'desc')
            ->skip(0)->take(1)
            ->get();
        if (count($lastKode) == 0) {
            $dateCreate = date_create($_GET['tanggal']);
            $date = date_format($dateCreate, 'my');
            $kode = "PJ" . $date . "-0001";
        } else {
            $ex = explode('-', $lastKode[0]->kode_penjualan);
            $no = (int)$ex[1] + 1;
            $newNo = sprintf("%04s", $no);
            $kode = $ex[0] . '-' . $newNo;
        }
        return $kode;
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'kode_penjualan' => 'required',
            'tanggal' => 'required',
            'kode_customer' => 'required',
            'status_ppn' => 'required',
            'qty' => 'required|numeric|gt:0',
            'harga_satuan' => 'required|numeric|gt:0',
            'keterangan' => 'required',
        ]);

        try {
            $totalPpn = 0;

            $total = $request->get('qty') * $request->get('harga_satuan');

            if ($request->get('status_ppn') == 'Tanpa') {
                $totalPpn = 0;
            } elseif ($request->get('status_ppn') == 'Belum') {
                $totalPpn = 10 / 100 * $total;
            } else {
                $total = (100 / 110) * $total;
                $totalPpn = 10 / 100 * $total;
            }

            $grandtotal = $total + $totalPpn;

            $newPenjualan = new PenjualanLain;
            $newPenjualan->kode_penjualan = $request->get('kode_penjualan');
            $newPenjualan->kode_customer = $request->get('kode_customer');
            $newPenjualan->tanggal = $request->get('tanggal');
            $newPenjualan->status_ppn = $request->get('status_ppn');
            $newPenjualan->jatuh_tempo = $request->get('jatuh_tempo');
            $newPenjualan->qty = $request->get('qty');
            $newPenjualan->harga_satuan = $request->get('harga_satuan');
            $newPenjualan->keterangan = str_replace(' ', '-', ucwords($request->get('keterangan')));
            $newPenjualan->total = $total;
            $newPenjualan->total_ppn = $totalPpn;
            $newPenjualan->grandtotal = $grandtotal;
            $newPenjualan->terbayar = 0;
            $newPenjualan->tipe_penjualan = 'catering';
            $newPenjualan->created_by = Auth::user()->id;

            $newPenjualan->save();

            // insert log activity insert
            $newActivity = new LogActivity;
            $newActivity->id_user = Auth::user()->id;
            $newActivity->jenis_transaksi = 'Penjualan Catering';
            $newActivity->tipe = 'Insert';
            $newActivity->keterangan = 'Input Penjualan Catering dengan kode '. $request->get('kode_penjualan') .' dengan grandtotal '. $grandtotal;
            $newActivity->save();

            // save jurnal penjualan
            $newJurnal = new Jurnal;
            $newJurnal->tanggal = $request->get('tanggal');
            $newJurnal->jenis_transaksi = 'Penjualan Catering';
            $newJurnal->kode_transaksi = $request->get('kode_penjualan');
            $newJurnal->keterangan = 'Penjualan Catering';
            $newJurnal->kode = '1103'; //piutang
            $newJurnal->lawan = '4101'; //pendapatan
            $newJurnal->tipe = 'Debet';
            $newJurnal->nominal = $total;
            $newJurnal->id_detail = '';
            $newJurnal->save();

            // save jurnal ppn
            if ($request->get('status_ppn') != 'Tanpa') {
                // save jurnal ppn penjualan
                $newJurnal = new Jurnal;
                $newJurnal->tanggal = $request->get('tanggal');
                $newJurnal->jenis_transaksi = 'Penjualan Catering';
                $newJurnal->kode_transaksi = $request->get('kode_penjualan');
                $newJurnal->keterangan = 'PPN Penjualan Catering';
                $newJurnal->kode = '1103'; //piutang
                $newJurnal->lawan = '2105'; //ppn masukan
                $newJurnal->tipe = 'Debet';
                $newJurnal->nominal = $totalPpn;
                $newJurnal->id_detail = '';
                $newJurnal->save();
            }

            //update piutang supplier
            Customer::where('kode_customer', $request->get('kode_customer'))
                            ->update([
                                'piutang' => \DB::raw('piutang+' . $grandtotal),
                            ]);
                            
            //insert ke kartu piutang
            $kartuPiutang = new KartuPiutang;
            $kartuPiutang->kode_customer = $request->get('kode_customer');
            $kartuPiutang->tipe = 'Penjualan';
            $kartuPiutang->kode_transaksi = $request->get('kode_penjualan');
            $kartuPiutang->nominal = $grandtotal;
            $kartuPiutang->tanggal = $request->get('tanggal');
            $kartuPiutang->save();

            return redirect()->route('penjualan-catering.index')->withStatus('Data berhasil ditambahkan.');

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
            $this->param['btnRight']['link'] = route('penjualan-catering.index');
            $this->param['customer'] = Customer::get();
            $this->param['penjualan'] = PenjualanLain::find($kode);

            return \view('penjualan.penjualan-catering.edit-penjualan-catering', $this->param);
        } catch (\Exception $e) {
            return redirect()->back()->withStatus('Terjadi kesalahan : ' . $e->getMessage());
        } catch (\Illuminate\Database\QueryException $e) {
            return redirect()->back()->withStatus('Terjadi kesalahan pada database : ' . $e->getMessage());
        }
    }

    public function update(Request $request, $kode)
    {
        $validatedData = $request->validate([
            'kode_penjualan' => 'required',
            'tanggal' => 'required',
            'kode_customer' => 'required',
            'qty' => 'required|numeric|gt:0',
            'harga_satuan' => 'required|numeric|gt:0',
            'keterangan' => 'required',
        ]);

        try {

            $penjualan = PenjualanLain::select('tanggal', 'kode_customer','status_ppn', 'grandtotal')->where('kode_penjualan', $kode)->get()[0];

            $bulanPembelian = date('m-Y', strtotime($penjualan->tanggal));
            $editBulanPembelian = date('m-Y', strtotime($request->get('tanggal')));

            if ($bulanPembelian != $editBulanPembelian) {
                return redirect()->back()->withStatus('Tidak dapat merubah bulan transaksi');
            }

            $statusPpn = $penjualan->status_ppn;
            $grandtotal = $penjualan->grandtotal;
            $kodeCustomer = $penjualan->kode_customer;
        
            $newTotal = $request->get('qty') * $request->get('harga_satuan');

            if ($statusPpn == 'Sudah') {
                $newTotalPpn = 10 / 110 * $newTotal;
                $newTotal = (100 / 110 * $newTotal);
            }
            elseif ($statusPpn == 'Belum') {
                $newTotalPpn = 10 / 100 * $newTotal;
            }else{
                $newTotalPpn = 0;
            }

            $newGrandtotal = $newTotal + $newTotalPpn;

            //update penjualan
            PenjualanLain::where('kode_penjualan', $kode)
                ->update([
                    'kode_customer' => $request->get('kode_customer'),
                    'tanggal' => $request->get('tanggal'),
                    'jatuh_tempo' => $request->get('jatuh_tempo'),
                    'qty' => $request->get('qty'),
                    'harga_satuan' => $request->get('harga_satuan'),
                    'keterangan' => str_replace(' ', '-', ucwords($request->get('keterangan'))),
                    'total' => $newTotal,
                    'total_ppn' => $newTotalPpn,
                    'grandtotal' => $newGrandtotal,
                ]);

            // insert log activity update
            $newActivity = new LogActivity;
            $newActivity->id_user = Auth::user()->id;
            $newActivity->jenis_transaksi = 'Penjualan Catering';
            $newActivity->tipe = 'Update';
            $newActivity->keterangan = 'Update Penjualan Catering dengan kode '. $kode .' dengan grandtotal awal '. $grandtotal . ' menjadi ' . $newGrandtotal;
            $newActivity->save();

            //update jurnal penjualan
            Jurnal::where('kode_transaksi', $kode)->where('keterangan', 'Penjualan Catering')
            ->update([
                'tanggal' => $request->get('tanggal'),
                'nominal' => $newTotal,
            ]);
            
            if ($statusPpn != 'Tanpa') {
                //update jurnal ppn penjualan
                Jurnal::where('kode_transaksi', $kode)->where('keterangan', 'PPN Penjualan Catering')
                    ->update([
                        'tanggal' => $request->get('tanggal'),
                        'nominal' => $newTotalPpn,
                    ]);

            }

            Customer::where('kode_customer', $kodeCustomer)
                        ->update([
                            'piutang' => \DB::raw('piutang-' . $grandtotal),
                        ]);
            
            Customer::where('kode_customer', $request->get('kode_customer'))
                        ->update([
                            'piutang' => \DB::raw('piutang+' . $newGrandtotal),
                        ]);
            
            //update kartu piutang
            KartuPiutang::where('kode_transaksi', $kode)
                ->update([
                    'tanggal' => $request->get('tanggal'),
                    'kode_customer' => $request->get('kode_customer'),
                    'nominal' => $newGrandtotal,
                ]);
            
            return redirect()->route('penjualan-catering.index')->withStatus('Data berhasil diperbarui.');
        } catch (\Exception $e) {
            return redirect()->back()->withStatus('Terjadi kesalahan. : ' . $e->getMessage());
        } catch (\Illuminate\Database\QueryException $e) {
            return redirect()->back()->withStatus('Terjadi kesalahan pada database : ' . $e->getMessage());
        }
    }

    public function destroy($kode)
    {
        try {
            $penjualan = PenjualanLain::findOrFail($kode);

            KartuPiutang::where('kode_transaksi', $kode)->delete();

            Customer::where('kode_customer', $penjualan->kode_customer)
                        ->update([
                            'piutang' => \DB::raw('piutang-' . $penjualan->grandtotal),
                        ]);
            // insert log activity delete
            $newActivity = new LogActivity;
            $newActivity->id_user = Auth::user()->id;
            $newActivity->jenis_transaksi = 'Penjualan Catering';
            $newActivity->tipe = 'Delete';
            $newActivity->keterangan = 'Hapus Penjualan Catering dengan kode '. $kode .' dengan grandtotal '. $penjualan->grandtotal;
            $newActivity->save();
            $penjualan->delete();

            // delete jurnal

            Jurnal::where('kode_transaksi', $kode)->delete();

            return redirect()->route('penjualan-catering.index')->withStatus('Data berhasil dihapus.');
        } catch (\Exception $e) {
            return redirect()->back()->withStatus('Terjadi kesalahan. : ' . $e->getMessage());
        } catch (\Illuminate\Database\QueryException $e) {
            return redirect()->back()->withStatus('Terjadi kesalahan pada database : ' . $e->getMessage());
        }
    }

    public function penjualanJatuhTempo()
    {
        $this->param['pageInfo'] = 'Penjualan Barang / List Penjualan Barang Jatuh Tempo';

        try {
            $this->param['penjualanBarang'] = PenjualanLain::where('tanggal', '<=', Date('Y-m-d'))->orWhere('jatuh_tempo', Date('Y-m-d'))->paginate(10);
        } catch (\Illuminate\Database\QueryException $e) {
            return redirect()->back()->withErrors('Terjadi Kesalahan');
        }

        return \view('penjualan.penjualan-jatuh-tempo', $this->param);
    }

    public function kartuPiutang()
    {
        try {
            $this->param['customer'] = Customer::orderBy('kode_customer', 'ASC')->get();
        } catch (\Illuminate\Database\QueryException $e) {
            return redirect()->back()->withErrors('Terjadi Kesalahan');
        }

        return \view('penjualan.kartu-piutang', $this->param);
    }

    public function getKartuPiutang(Request $request)
    {
        try{
            $this->param['customer'] = Customer::orderBy('kode_customer', 'ASC')->get();
            $this->param['selectedCustomer'] = Customer::whereBetween('kode_customer', [$request->get('kodeCustomerDari'), $request->get('kodeCustomerSampai')])->orderBy('kode_customer', 'ASC')->get();
        } catch (\Illuminate\Database\QueryException $e) {
            return redirect()->back()->withErrors('Terjadi Kesalahan');
        }
        return \view('penjualan.kartu-piutang', $this->param);
    }

    public function reportPenjualanCatering()
    {
        try {
            $this->param['pageInfo'] = 'Penjualan Catering / List Penjualan Catering';
            $this->param['customer'] = Customer::get();
            $this->param['report'] = null;
        } catch (\Exception $e) {
            return redirect()->back()->withStatus('Terjadi kesalahan. : ' . $e->getMessage());
        }
        catch(\Illuminate\Database\QueryException $e){
            return redirect()->back()->withStatus('Terjadi kesalahan pada database. : ' . $e->getMessage());
        }
        return \view('penjualan.penjualan-catering.laporan-penjualan-catering', $this->param);
    }

    public function getReport(Request $request)
    {
        try {
            $this->param['pageInfo'] = 'Penjualan Catering / List Penjualan Catering';
            $this->param['customer'] = Customer::get();
            $whereCustomer = null;
            if($request->get('kode_customer') != ''){
                $whereCustomer = 'penjualan_lain.kode_customer';
            }
            $this->param['report'] = PenjualanLain::select('penjualan_lain.*', 'c.kode_customer', 'c.nama')
                                                    ->join('customer AS c', 'c.kode_customer', 'penjualan_lain.kode_customer')
                                                    ->where($whereCustomer, $request->get('kode_customer'))
                                                    ->where('penjualan_lain.tipe_penjualan', 'catering')
                                                    ->whereBetween('tanggal', [$request->get('start'), $request->get('end')])
                                                    ->orderBy('penjualan_lain.'.$request->get('order'), 'ASC')
                                                    ->get();
            
        } catch (\Exception $e) {
            return redirect()->back()->withError('Terjadi kesalahan. : ' . $e->getMessage());
        }
        catch(\Illuminate\Database\QueryException $e){
            return redirect()->back()->withError('Terjadi kesalahan pada database. : ' . $e->getMessage());
        }
        return \view('penjualan.penjualan-catering.laporan-penjualan-catering', $this->param);
    }

    public function printReport(Request $request)
    {
        try {
            $this->param['customer'] = Customer::get();

            $whereCustomer = null;
            if($request->get('kode_customer') != ''){
                $whereCustomer = 'penjualan_lain.kode_customer';
            }
            $this->param['report'] = PenjualanLain::select('penjualan_lain.*', 'c.kode_customer', 'c.nama')
                                                    ->join('customer AS c', 'c.kode_customer', 'penjualan_lain.kode_customer')
                                                    ->where($whereCustomer, $request->get('kode_customer'))
                                                    ->where('penjualan_lain.tipe_penjualan', 'catering')
                                                    ->whereBetween('tanggal', [$request->get('start'), $request->get('end')])
                                                    ->orderBy('penjualan_lain.'.$request->get('order'), 'ASC')
                                                    ->get();

            return \view('penjualan.penjualan-catering.print-laporan-catering', $this->param);
        } catch (\Exception $e) {
            return $e;
            return redirect()->back()->withError('Terjadi kesalahan. : ' . $e->getMessage());
        }
        catch(\Illuminate\Database\QueryException $e){
            return $e;
            return redirect()->back()->withError('Terjadi kesalahan pada database. : ' . $e->getMessage());
        }
    }

    public function printInvoice(Request $request)
    {
        try {            
            $this->param['report'] = PenjualanLain::select('penjualan_lain.*', 'c.kode_customer', 'c.nama')
                                                    ->join('customer AS c', 'c.kode_customer', 'penjualan_lain.kode_customer')
                                                    ->where('penjualan_lain.kode_penjualan', $request->get('kode_penjualan'))
                                                    ->first();

            // return $this->param['report'];
            return \view('penjualan.penjualan-catering.print-invoice-catering', $this->param);
        } catch (\Exception $e) {
            return $e;
            return redirect()->back()->withError('Terjadi kesalahan. : ' . $e->getMessage());
        }
        catch(\Illuminate\Database\QueryException $e){
            return $e;
            return redirect()->back()->withError('Terjadi kesalahan pada database. : ' . $e->getMessage());
        }
    }
    
    public function getTtlPiutang()
    {
        $ttl = PenjualanLain::selectRaw('sum(grandtotal-terbayar) as ttl')->where('kode_customer',$_GET['kode'])->whereRaw('terbayar != grandtotal')->get();
        $data['ttl'] = $ttl[0]['ttl']==null ? 0 : $ttl[0]['ttl'];
        $data['kode_rekening'] = '1103';
        echo json_encode($data);
    }
}
