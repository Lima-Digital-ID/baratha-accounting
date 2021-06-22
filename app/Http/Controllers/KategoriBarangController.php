<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use \App\Models\KategoriBarang;
class KategoriBarangController extends Controller
{
    private $param;
    public function __construct()
    {
        $this->param['icon'] = 'fa-boxes';
    }

    public function index(Request $request)
    {
        $this->param['pageInfo'] = 'Manage Kategori Barang / List Kategori Barang';
        $this->param['btnRight']['text'] = 'Tambah Data';
        $this->param['btnRight']['link'] = route('kategori-barang.create');

        try {
            $keyword = $request->get('keyword');
            if ($keyword) {
                $kategoriBarang = KategoriBarang::where('nama', 'LIKE', "%$keyword%")->paginate(10);
            }
            else{
                $kategoriBarang = KategoriBarang::paginate(10);
            }
        } catch (\Illuminate\Database\QueryException $e) {
            return redirect()->back()->withStatus('Terjadi Kesalahan');
        }
                
        return \view('persediaan.kategori-barang.list-kategori-barang', ['kategoriBarang' => $kategoriBarang], $this->param);
    }

    public function create()
    {
        $this->param['pageInfo'] = 'Manage Kategori Barang / Tambah Data';
        $this->param['btnRight']['text'] = 'Lihat Data';
        $this->param['btnRight']['link'] = route('kategori-barang.index');

        return \view('persediaan.kategori-barang.tambah-kategori-barang', $this->param);
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'nama' => 'required|unique:kategori_barang',
        ],
        [
            'required' => ':attribute harus diisi.',
            'unique' => ':attribute telah terpakai.'
         ],
         [
            'nama' => 'Nama'
         ]);
        try{    
            $newKategoriBarang = new KategoriBarang;
    
            $newKategoriBarang->nama = ucwords($request->get('nama'));
    
            $newKategoriBarang->save();
    
            return redirect()->back()->withStatus('Data berhasil ditambahkan.');
        }
        catch(\Exception $e){
            return redirect()->back()->withStatus('Terjadi kesalahan. : '. $e->getMessage());
        }
        catch(\Illuminate\Database\QueryException $e){
            return redirect()->back()->withStatus('Terjadi kesalahan pada database : '. $e->getMessage());
        }
    }

    public function edit($id)
    {
        try{
            $this->param['pageInfo'] = 'Manage Kategori Barang / Edit Data';
            $this->param['btnRight']['text'] = 'Lihat Data';
            $this->param['btnRight']['link'] = route('kategori-barang.index');
            $this->param['kategoriBarang'] = KategoriBarang::find($id);

            return \view('persediaan.kategori-barang.edit-kategori-barang', $this->param);
        }
        catch(\Exception $e){
            return redirect()->back()->withError('Terjadi kesalahan : '. $e->getMessage());
        }
        catch(\Illuminate\Database\QueryException $e){
            return redirect()->back()->withError('Terjadi kesalahan pada database : '. $e->getMessage());
        }
    }

    public function update(Request $request, $id)
    {
        $kategoriBarang = KategoriBarang::find($id);

        $isUniqueNama = $kategoriBarang->nama == $request->nama ? '' : '|unique:kategori_barang';

        $validatedData = $request->validate([
            'nama' => 'required'.$isUniqueNama,
        ],
        [
            'required' => ':attribute harus diisi.',
            'unique' => ':attribute telah terpakai.'
         ],
         [
            'nama' => 'Nama'
         ]);
        try{
            // $kategoriBarang->kategori_barang = $request->get('kategori_barang');
            $kategoriBarang->nama = ucwords($request->get('nama'));
            
            $kategoriBarang->save();

            return redirect()->back()->withStatus('Data berhasil diperbarui.');
        }
        catch(\Exception $e){
            return redirect()->back()->withError('Terjadi kesalahan : '. $e->getMessage());
        }
        catch(\Illuminate\Database\QueryException $e){
            return redirect()->back()->withError('Terjadi kesalahan pada database : '. $e->getMessage());
        }
    }

    public function destroy($id)
    {
        try{
            $kategoriBarang = KategoriBarang::findOrFail($id);

            $kategoriBarang->delete();

            return redirect()->route('kategori-barang.index')->withStatus('Data berhasil dihapus.');
        }
        catch(\Exception $e){
            return redirect()->route('kategori-barang.index')->withError('Terjadi kesalahan : '. $e->getMessage());
        }
        catch(\Illuminate\Database\QueryException $e){
            return redirect()->route('kategori-barang.index')->withError('Terjadi kesalahan pada database : '. $e->getMessage());
        }
        
    }
}
