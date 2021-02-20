<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\KartuPiutang;
use App\Models\PenjualanLain;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
    private $param;
    public function __construct()
    {
        $this->param['icon'] = 'fa-cogs';
    }

    public function index(Request $request)
    {
        $this->param['pageInfo'] = 'Manage Customer / List Customer';
        $this->param['btnRight']['text'] = 'Tambah Data';
        $this->param['btnRight']['link'] = route('customer.create');

        try {
            $keyword = $request->get('keyword');
            if ($keyword) {
                $customer = Customer::where('kode_customer', 'LIKE', "%$keyword%")->orWhere('nama', 'LIKE', "%$keyword%")->paginate(10);
            }
            else{
                $customer = Customer::paginate(10);
            }
        } catch (\Illuminate\Database\QueryException $e) {
            return redirect()->back()->withStatus('Terjadi Kesalahan');
        }
                
        return \view('penjualan.customer.list-customer', ['customer' => $customer], $this->param);
    }

    public function create()
    {
        $this->param['pageInfo'] = 'Manage Customer / Tambah Data';
        $this->param['btnRight']['text'] = 'Lihat Data';
        $this->param['btnRight']['link'] = route('customer.index');

        return \view('penjualan.customer.tambah-customer', $this->param);
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'kode_customer' => 'required|unique:customer',
            'nama' => 'required',
            'alamat' => 'required',
            'no_hp' => 'required|unique:customer',
            'piutang' => 'required'
        ]);
        
        try{
            $newCustomer = new Customer;
    
            $newCustomer->kode_customer = $request->get('kode_customer');
            $newCustomer->nama = $request->get('nama');
            $newCustomer->alamat = $request->get('alamat');
            $newCustomer->no_hp = $request->get('no_hp');
            $newCustomer->piutang = $request->get('piutang');
    
            $newCustomer->save();
    
            return redirect()->back()->withStatus('Data berhasil ditambahkan.');
        }
        catch(\Exception $e){
            return redirect()->back()->withStatus('Terjadi kesalahan. : '. $e->getMessage());
        }
        catch(\Illuminate\Database\QueryException $e){
            return redirect()->back()->withStatus('Terjadi kesalahan pada database : '. $e->getMessage());
        }
    }

    public function edit($kode_customer)
    {
        try{
            $this->param['pageInfo'] = 'Manage Customer / Edit Data';
            $this->param['btnRight']['text'] = 'Lihat Data';
            $this->param['btnRight']['link'] = route('customer.index');
            $this->param['customer'] = Customer::where('kode_customer', $kode_customer)->first();

            return \view('penjualan.customer.edit-customer', $this->param);
        }
        catch(\Exception $e){
            return redirect()->back()->withError('Terjadi kesalahan : '. $e->getMessage());
        }
        catch(\Illuminate\Database\QueryException $e){
            return redirect()->back()->withError('Terjadi kesalahan pada database : '. $e->getMessage());
        }   
    }

    public function update(Request $request, $kode_customer)
    {
        $customer = Customer::where('kode_customer', $kode_customer)->first();

        $isKodeUnique = $customer->kode_customer == $kode_customer ? '' : '|unique:customer';
        $isPhoneUnique = $customer->no_hp == $request->no_hp ? '' : '|unique:customer';

        $validatedData = $request->validate([
            'kode_customer' => 'required'.$isKodeUnique,
            'nama' => 'required',
            'alamat' => 'required',
            'no_hp' => 'required'.$isPhoneUnique,
            'piutang' => 'required'
        ]);
        try{
            $customer->kode_customer = $request->get('kode_customer');
            $customer->nama = $request->get('nama');
            $customer->alamat = $request->get('alamat');
            $customer->no_hp = $request->get('no_hp');
            $customer->piutang = $request->get('piutang');
            $customer->save();

            return redirect()->back()->withStatus('Data berhasil diperbarui.');
        }
        catch(\Exception $e){
            return redirect()->back()->withError('Terjadi kesalahan : '. $e->getMessage());
        }
        catch(\Illuminate\Database\QueryException $e){
            return redirect()->back()->withError('Terjadi kesalahan pada database : '. $e->getMessage());
        }
    }

    public function destroy($kode_customer)
    {
        try{
            $customer = Customer::findOrFail($kode_customer);
            $customer->delete();

            return redirect()->route('customer.index')->withStatus('Data berhasil dihapus.');
        }
        catch(\Exception $e){
            return $e->getMessage();
            return redirect()->route('customer.index')->withError('Terjadi kesalahan : '. $e->getMessage());
        }
        catch(\Illuminate\Database\QueryException $e){
            return redirect()->route('customer.index')->withError('Terjadi kesalahan pada database : '. $e->getMessage());
        }
    }
    public function pembayaranPiutang(Request $request)
    {
        try {
            //insert ke kartu hutang
            $kartuHutang = new KartuPiutang;
            $kartuHutang->tanggal = date('Y-m-d');
            $kartuHutang->kode_customer = $request->get('kode_customer');
            $kartuHutang->kode_transaksi = $request->get('kode_transaksi');
            $kartuHutang->nominal = $request->get('nominal_bayar');
            $kartuHutang->tipe = 'Pelunasan';
            $kartuHutang->save();

            //update terbayar penjualan barang
            PenjualanLain::where('kode_penjualan', $request->get('kode_penjualan'))
            ->update([
                'terbayar' => \DB::raw('terbayar+' . $request->get('nominal_bayar')),
            ]);

            //update piutang customer
            Customer::where('kode_customer', $request->get('kode_customer'))
            ->update([
                'piutang' => \DB::raw('piutang-' . $request->get('nominal_bayar')),
            ]);
            return redirect()->back()->withStatus('Pembayaran Piutang Berhasil.');
        } catch (\Exception $e) {
            return redirect()->back()->withStatus('Terjadi kesalahan. : ' . $e->getMessage());
        } catch (\Illuminate\Database\QueryException $e) {
            return redirect()->back()->withStatus('Terjadi kesalahan pada database : ' . $e->getMessage());
        }    
    }

}
