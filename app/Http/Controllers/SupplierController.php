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
                
        return \view('supplier.list-supplier', ['supplier' => $supplier], $this->param);
    }

    public function create()
    {
        $this->param['pageInfo'] = 'Manage Supplier / Tambah Data';
        $this->param['btnRight']['text'] = 'Lihat Data';
        $this->param['btnRight']['link'] = route('supplier.index');

        $data = Supplier::all();
        $kode_supplier = null;
        if($data->count() > 0){
            $lastnoSupplier = Supplier::orderBy('kode_supplier', 'desc')->first()->kode_supplier;
            $lastIncreament = substr($lastnoSupplier, 1);
            $kode_supplier = str_pad($lastIncreament + 1, 3, 0, STR_PAD_LEFT);
            $kode_supplier = 'S-'.$kode_supplier;
        }
        else{
            $kode_supplier = 'S-001';
        }
        $this->param['kode_supplier'] = $kode_supplier;

        return \view('supplier.tambah-supplier', $this->param);
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'kode_supplier' => 'required|unique:kode_supplier',
            'nama' => 'required',
            'alamat' => 'required',
            'no_hp' => 'required',
            'hutang' => 'required'
        ]);
        try{
    
            $newKodeInduk = new Supplier;
    
            $newKodeInduk->kode_induk = $request->get('kode_induk');
            $newKodeInduk->nama = $request->get('nama');
    
            $newKodeInduk->save();
    
            return redirect()->back()->withStatus('Data berhasil ditambahkan.');
        }
        catch(\Exception $e){
            return redirect()->back()->withStatus('Terjadi kesalahan. : '. $e->getMessage());
        }
        catch(\Illuminate\Database\QueryException $e){
            return redirect()->back()->withStatus('Terjadi kesalahan pada database : '. $e->getMessage());
        }
    }
}
