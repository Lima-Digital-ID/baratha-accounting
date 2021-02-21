<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use \App\Models\KodeInduk;

class KodeIndukController extends Controller
{
    private $param;
    public function __construct()
    {
        $this->param['icon'] = 'fa-money-bill-wave';
    }

    public function index(Request $request)
    {
        $this->param['pageInfo'] = 'Manage Kode Induk / List Kode Induk';
        $this->param['btnRight']['text'] = 'Tambah Data';
        $this->param['btnRight']['link'] = route('kode-induk.create');

        try {
            $keyword = $request->get('keyword');
            if ($keyword) {
                $kodeInduk = KodeInduk::where('nama', 'LIKE', "%$keyword%")->orWhere('kode_induk', 'LIKE', "%$keyword%")->paginate(10);
            }
            else{
                $kodeInduk = KodeInduk::paginate(10);
            }
        } catch (\Illuminate\Database\QueryException $e) {
            return redirect()->back()->withStatus('Terjadi Kesalahan');
        }
                
        return \view('master-akuntansi.kode-induk.list-kode-induk', ['kodeInduk' => $kodeInduk], $this->param);
    }

    public function create()
    {
        $this->param['pageInfo'] = 'Manage Kode Induk / Tambah Data';
        $this->param['btnRight']['text'] = 'Lihat Data';
        $this->param['btnRight']['link'] = route('kode-induk.index');

        return \view('master-akuntansi.kode-induk.tambah-kode-induk', $this->param);
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'kode_induk' => 'required|unique:kode_induk',
            'nama' => 'required|unique:kode_induk',
        ],
        [
            'required' => ':attribute harus diisi.',
            'unique' => ':attribute telah terpakai.'
        ],
        [
            'kode_induk' => 'Kode Induk',
            'nama' => 'Nama'
        ]);
        try{
    
            $newKodeInduk = new KodeInduk;
    
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

    public function edit($id)
    {
        try{
            $this->param['pageInfo'] = 'Manage Kode Induk / Edit Data';
            $this->param['btnRight']['text'] = 'Lihat Data';
            $this->param['btnRight']['link'] = route('kode-induk.index');
            $this->param['kodeInduk'] = KodeInduk::find($id);

            return \view('master-akuntansi.kode-induk.edit-kode-induk', $this->param);
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
        $kodeInduk = KodeInduk::find($id);

        $isUnique = $kodeInduk->kode_induk == $request->kode_induk ? '' : '|unique:kode_induk';
        $isUniqueNama = $kodeInduk->nama == $request->nama ? '' : '|unique:kode_induk';

        $validatedData = $request->validate([
            'kode_induk' => 'required'.$isUnique,
            'nama' => 'required'.$isUniqueNama,
        ],
        [
            'required' => ':attribute harus diisi.',
            'unique' => ':attribute telah terpakai.'
        ],
        [
            'kode_induk' => 'Kode Induk',
            'nama' => 'Nama'
        ]);
        try{

            // $kodeInduk->kode_induk = $request->get('kode_induk');
            $kodeInduk->nama = $request->get('nama');
            
            $kodeInduk->save();

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
            $member = KodeInduk::findOrFail($id);

            $member->delete();

            return redirect()->route('kode-induk.index')->withStatus('Data berhasil dihapus.');
        }
        catch(\Exception $e){
            return redirect()->route('kode-induk.index')->withError('Terjadi kesalahan : '. $e->getMessage());
        }
        catch(\Illuminate\Database\QueryException $e){
            return redirect()->route('kode-induk.index')->withError('Terjadi kesalahan pada database : '. $e->getMessage());
        }
        
    }
}
