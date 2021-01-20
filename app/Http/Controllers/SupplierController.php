<?php

namespace App\Http\Controllers;

use App\Models\Supplier;
use Illuminate\Http\Request;

class SupplierController extends Controller
{
    private $param;
    public function __construct()
    {
        $this->param['icon'] = 'fa-cogs';
    }

    public function index(Request $request)
    {
        $this->param['pageInfo'] = 'Manage Supplier / List Supplier';
        $this->param['btnRight']['text'] = 'Tambah Data';
        $this->param['btnRight']['link'] = route('supplier.create');

        try {
            $keyword = $request->get('keyword');
            if ($keyword) {
                $supplier = Supplier::where('kode_supplier', 'LIKE', "%$keyword%")->orWhere('nama', 'LIKE', "%$keyword%")->paginate(10);
            }
            else{
                $supplier = Supplier::paginate(10);
            }
        } catch (\Illuminate\Database\QueryException $e) {
            return redirect()->back()->withStatus('Terjadi Kesalahan');
        }
                
        return \view('pembelian.supplier.list-supplier', ['supplier' => $supplier], $this->param);
    }

    public function create()
    {
        $this->param['pageInfo'] = 'Manage Supplier / Tambah Data';
        $this->param['btnRight']['text'] = 'Lihat Data';
        $this->param['btnRight']['link'] = route('supplier.index');

        return \view('pembelian.supplier.tambah-supplier', $this->param);
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'kode_supplier' => 'required|unique:supplier',
            'nama' => 'required',
            'alamat' => 'required',
            'no_hp' => 'required|unique:supplier',
            'hutang' => 'required'
        ]);
        
        try{
            $newSupplier = new Supplier;
    
            $newSupplier->kode_supplier = $request->get('kode_supplier');
            $newSupplier->nama = $request->get('nama');
            $newSupplier->alamat = $request->get('alamat');
            $newSupplier->no_hp = $request->get('no_hp');
            $newSupplier->hutang = $request->get('hutang');
    
            $newSupplier->save();
    
            return redirect()->back()->withStatus('Data berhasil ditambahkan.');
        }
        catch(\Exception $e){
            return redirect()->back()->withStatus('Terjadi kesalahan. : '. $e->getMessage());
        }
        catch(\Illuminate\Database\QueryException $e){
            return redirect()->back()->withStatus('Terjadi kesalahan pada database : '. $e->getMessage());
        }
    }

    public function edit($kode_supplier)
    {
        try{
            $this->param['pageInfo'] = 'Manage Supplier / Edit Data';
            $this->param['btnRight']['text'] = 'Lihat Data';
            $this->param['btnRight']['link'] = route('supplier.index');
            $this->param['supplier'] = Supplier::where('kode_supplier', $kode_supplier)->first();

            return \view('pembelian.supplier.edit-supplier', $this->param);
        }
        catch(\Exception $e){
            return redirect()->back()->withError('Terjadi kesalahan : '. $e->getMessage());
        }
        catch(\Illuminate\Database\QueryException $e){
            return redirect()->back()->withError('Terjadi kesalahan pada database : '. $e->getMessage());
        }   
    }

    public function update(Request $request, $kode_supplier)
    {
        $supplier = Supplier::where('kode_supplier', $kode_supplier)->first();

        $isKodeUnique = $supplier->kode_supplier == $kode_supplier ? '' : '|unique:supplier';
        $isPhoneUnique = $supplier->no_hp == $request->no_hp ? '' : '|unique:supplier';

        $validatedData = $request->validate([
            'kode_supplier' => 'required'.$isKodeUnique,
            'nama' => 'required',
            'alamat' => 'required',
            'no_hp' => 'required'.$isPhoneUnique,
            'hutang' => 'required'
        ]);
        try{
            $supplier->kode_supplier = $request->get('kode_supplier');
            $supplier->nama = $request->get('nama');
            $supplier->alamat = $request->get('alamat');
            $supplier->no_hp = $request->get('no_hp');
            $supplier->hutang = $request->get('hutang');
            $supplier->save();

            return redirect()->back()->withStatus('Data berhasil diperbarui.');
        }
        catch(\Exception $e){
            return redirect()->back()->withError('Terjadi kesalahan : '. $e->getMessage());
        }
        catch(\Illuminate\Database\QueryException $e){
            return redirect()->back()->withError('Terjadi kesalahan pada database : '. $e->getMessage());
        }
    }

    public function destroy($kode_supplier)
    {
        try{
            $supplier = Supplier::findOrFail($kode_supplier);
            $supplier->delete();

            return redirect()->route('pembelian.supplier.index')->withStatus('Data berhasil dihapus.');
        }
        catch(\Exception $e){
            return $e->getMessage();
            return redirect()->route('pembelian.supplier.index')->withError('Terjadi kesalahan : '. $e->getMessage());
        }
        catch(\Illuminate\Database\QueryException $e){
            return redirect()->route('pembelian.supplier.index')->withError('Terjadi kesalahan pada database : '. $e->getMessage());
        }
    }
}
