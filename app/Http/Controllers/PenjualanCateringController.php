<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use \App\Models\PenjualanCatering;
use \App\Models\Customer;
use \App\Models\KartuPiutang;

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
                $penjualanCatering = PenjualanCatering::with('customer')->where('kode_penjualan', 'LIKE', "%$keyword%")->orWhere('kode_customer', 'LIKE', "%$keyword%")->paginate(10);
            } else {
                $penjualanCatering = PenjualanCatering::with('customer')->paginate(10);
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
        $lastKode = PenjualanCatering::select('kode_penjualan')
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
            'qty' => 'required|min:1',
            'harga_satuan' => 'required|min:1',
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

            $newPenjualan = new PenjualanCatering;
            $newPenjualan->kode_penjualan = $request->get('kode_penjualan');
            $newPenjualan->kode_customer = $request->get('kode_customer');
            $newPenjualan->tanggal = $request->get('tanggal');
            $newPenjualan->status_ppn = $request->get('status_ppn');
            $newPenjualan->jatuh_tempo = $request->get('jatuh_tempo');
            $newPenjualan->qty = $request->get('qty');
            $newPenjualan->harga_satuan = $request->get('harga_satuan');
            $newPenjualan->keterangan = $request->get('keterangan');
            $newPenjualan->total = $total;
            $newPenjualan->total_ppn = $totalPpn;
            $newPenjualan->grandtotal = $grandtotal;
            $newPenjualan->terbayar = 0;

            $newPenjualan->save();
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
            $this->param['penjualan'] = PenjualanCatering::find($kode);

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
            'qty' => 'required|min:1',
            'harga_satuan' => 'required|min:1',
            'keterangan' => 'required',
        ]);

        try {

            $penjualan = PenjualanCatering::select('tanggal', 'kode_customer','status_ppn', 'grandtotal')->where('kode_penjualan', $kode)->get()[0];

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
            }

            $newGrandtotal = $newTotal + $newTotalPpn;

            //update penjualan
            PenjualanCatering::where('kode_penjualan', $kode)
                ->update([
                    'kode_customer' => $request->get('kode_customer'),
                    'tanggal' => $request->get('tanggal'),
                    'jatuh_tempo' => $request->get('jatuh_tempo'),
                    'qty' => $request->get('qty'),
                    'harga_satuan' => $request->get('harga_satuan'),
                    'keterangan' => $request->get('keterangan'),
                    'total' => $newTotal,
                    'total_ppn' => $newTotalPpn,
                    'grandtotal' => $newGrandtotal,
                ]);

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
            $penjualan = PenjualanCatering::findOrFail($kode);

            KartuPiutang::where('kode_transaksi', $kode)->delete();

            Customer::where('kode_customer', $penjualan->kode_customer)
                        ->update([
                            'piutang' => \DB::raw('piutang-' . $penjualan->grandtotal),
                        ]);

            $penjualan->delete();

            return redirect()->route('penjualan-catering.index')->withStatus('Data berhasil dihapus.');
        } catch (\Exception $e) {
            return redirect()->back()->withStatus('Terjadi kesalahan. : ' . $e->getMessage());
        } catch (\Illuminate\Database\QueryException $e) {
            return redirect()->back()->withStatus('Terjadi kesalahan pada database : ' . $e->getMessage());
        }
    }
}