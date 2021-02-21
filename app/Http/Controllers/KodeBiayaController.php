<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use \App\Models\KodeBiaya;
use \App\Models\KodeRekening;

class KodeBiayaController extends Controller
{
    private $param;
    public function __construct()
    {
        $this->param['icon'] = 'fa-money-bill-wave';
    }

    public function index(Request $request)
    {
        $this->param['pageInfo'] = 'Manage Kode Biaya / List Kode Biaya';
        $this->param['btnRight']['text'] = 'Tambah Data';
        $this->param['btnRight']['link'] = route('kode-biaya.create');

        try {
            $keyword = $request->get('keyword');
            if ($keyword) {
                $kodeBiaya = KodeBiaya::where('nama', 'LIKE', "%$keyword%")->orWhere('kode_biaya', 'LIKE', "%$keyword%")->paginate(10);
            }
            else{
                $kodeBiaya = KodeBiaya::paginate(10);
            }
        } catch (\Illuminate\Database\QueryException $e) {
            return redirect()->back()->withStatus('Terjadi Kesalahan');
        }
                
        return \view('master-akuntansi.kode-biaya.list-kode-biaya', ['kodeBiaya' => $kodeBiaya], $this->param);
    }

    public function create()
    {
        try {
            //code...
            $this->param['pageInfo'] = 'Manage Kode Biaya / Tambah Data';
            $this->param['btnRight']['text'] = 'Lihat Data';
            $this->param['btnRight']['link'] = route('kode-biaya.index');
            $this->param['kodeRekening'] = KodeRekening::get();
        } catch (\Exception $e) {
            return redirect()->back()->withStatus('Terjadi kesalahan. : '. $e->getMessage());
        }

        return \view('master-akuntansi.kode-biaya.tambah-kode-biaya', $this->param);
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'kode_biaya' => 'required|unique:kode_biaya',
            'nama' => 'required',
            'kode_rekening' => 'required|not_in:',
        ],
        [
           'required' => ':attribute harus diisi.',
           'unique' => ':attribute telah terpakai.'
        ],
        [
           'kode_biaya' => 'Kode biaya',
           'nama' => 'Nama',
           'kode_rekening' => 'Kode Rekening' 
        ]);
        try{
    
            $newKodeBiaya = new KodeBiaya;
    
            $newKodeBiaya->kode_biaya = $request->get('kode_biaya');
            $newKodeBiaya->nama = $request->get('nama');
            $newKodeBiaya->kode_rekening = $request->get('kode_rekening');
    
            $newKodeBiaya->save();
    
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
            $this->param['pageInfo'] = 'Manage Kode Biaya / Edit Data';
            $this->param['btnRight']['text'] = 'Lihat Data';
            $this->param['btnRight']['link'] = route('kode-biaya.index');
            $this->param['kodeBiaya'] = KodeBiaya::find($id);
            $this->param['kodeRekening'] = KodeRekening::get();

            return \view('master-akuntansi.kode-biaya.edit-kode-biaya', $this->param);
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
        $validatedData = $request->validate([
            'kode_biaya' => 'required',
            'nama' => 'required',
            'kode_rekening' => 'required',
        ],
        [
            'required' => ':attribute harus diisi.',
            'unique' => ':attribute telah terpakai.'
         ],
         [
            'kode_biaya' => 'Kode biaya',
            'nama' => 'Nama',
            'kode_rekening' => 'Kode Rekening' 
         ]);
        try{
            $kodeBiaya = KodeBiaya::find($id);
            // $kodeBiaya->kode_biaya = $request->get('kode_biaya');
            $kodeBiaya->nama = $request->get('nama');
            $kodeBiaya->kode_rekening = $request->get('kode_rekening');
            
            $kodeBiaya->save();

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
            $kodeBiaya = KodeBiaya::findOrFail($id);

            $kodeBiaya->delete();

            return redirect()->route('kode-biaya.index')->withStatus('Data berhasil dihapus.');
        }
        catch(\Exception $e){
            return redirect()->route('kode-biaya.index')->withError('Terjadi kesalahan : '. $e->getMessage());
        }
        catch(\Illuminate\Database\QueryException $e){
            return redirect()->route('kode-biaya.index')->withError('Terjadi kesalahan pada database : '. $e->getMessage());
        }
        
    }
}
