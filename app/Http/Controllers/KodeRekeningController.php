<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use \App\Models\KodeRekening;
use \App\Models\KodeInduk;

class KodeRekeningController extends Controller
{
    private $param;
    public function __construct()
    {
        $this->param['icon'] = 'fa-money-bill-wave';
    }

    public function index(Request $request)
    {
        $this->param['pageInfo'] = 'Manage Kode Rekening / List Kode Rekening';
        $this->param['btnRight']['text'] = 'Tambah Data';
        $this->param['btnRight']['link'] = route('kode-rekening.create');

        try {
            $keyword = $request->get('keyword');
            if ($keyword) {
                $kodeRekening = KodeRekening::where('nama', 'LIKE', "%$keyword%")->orWhere('kode_rekening', 'LIKE', "%$keyword%")->paginate(10);
            }
            else{
                $kodeRekening = KodeRekening::paginate(10);
            }
        } catch (\Illuminate\Database\QueryException $e) {
            return redirect()->back()->withStatus('Terjadi Kesalahan');
        }
                
        return \view('master-akuntansi.kode-rekening.list-kode-rekening', ['kodeRekening' => $kodeRekening], $this->param);
    }

    public function create()
    {
        try {
            //code...
            $this->param['pageInfo'] = 'Manage Kode Rekening / Tambah Data';
            $this->param['btnRight']['text'] = 'Lihat Data';
            $this->param['btnRight']['link'] = route('kode-rekening.index');
            $this->param['kodeInduk'] = KodeInduk::get();
        } catch (\Exception $e) {
            return redirect()->back()->withStatus('Terjadi kesalahan. : '. $e->getMessage());
        }

        return \view('master-akuntansi.kode-rekening.tambah-kode-rekening', $this->param);
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'kode_induk' => 'required',
            'kode_rekening' => 'required|unique:kode_rekening',
            'nama' => 'required|unique:kode_rekening',
            'tipe' => 'required',
        ],
        [
            'required' => ':attribute harus diisi.',
            'unique' => ':attribute telah terpakai.'
        ],
        [
            'kode_induk' => 'Kode Induk',
            'kode_rekening' => 'Kode Rekening',
            'nama' => 'Nama',
            'tipe' => 'Tipe'
        ]);
        try{
    
            $newKodeRekening = new KodeRekening;
    
            $newKodeRekening->kode_rekening = $request->get('kode_rekening');
            $newKodeRekening->nama = str_replace(' ', '-', ucwords($request->get('nama')));
            $newKodeRekening->tipe = $request->get('tipe');
            $newKodeRekening->saldo_awal = $request->get('saldo_awal');
            $newKodeRekening->kode_induk = $request->get('kode_induk');
    
            $newKodeRekening->save();
    
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
            $this->param['pageInfo'] = 'Manage Kode Rekening / Edit Data';
            $this->param['btnRight']['text'] = 'Lihat Data';
            $this->param['btnRight']['link'] = route('kode-rekening.index');
            $this->param['kodeRekening'] = KodeRekening::find($id);
            $this->param['kodeInduk'] = KodeInduk::get();

            return \view('master-akuntansi.kode-rekening.edit-kode-rekening', $this->param);
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
        $kodeRekening = KodeRekening::find($id);

        $isUnique = $kodeRekening->kode_rekening == $request->kode_rekening ? '' : '|unique:kode_rekening';
        $isUniqueNama = $kodeRekening->nama == $request->nama ? '' : '|unique:kode_rekening';

        $validatedData = $request->validate([
            'kode_rekening' => 'required'.$isUnique,
            'nama' => 'required'.$isUniqueNama,
            'tipe' => 'required',
        ],
        [
            'required' => ':attribute harus diisi.',
            'unique' => ':attribute telah terpakai.'
        ],
        [
            'kode_induk' => 'Kode Induk',
            'kode_rekening' => 'Kode Rekening',
            'nama' => 'Nama',
            'tipe' => 'Tipe'
        ]);
        try{

            // $kodeRekening->kode_rekening = $request->get('kode_rekening');
            $kodeRekening->nama = str_replace(' ', '-', ucwords($request->get('nama')));
            $kodeRekening->tipe = $request->get('tipe');
            $kodeRekening->saldo_awal = $request->get('saldo_awal');
            // $kodeRekening->kode_induk = $request->get('kode_induk');
            
            $kodeRekening->save();

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
            $kodeRekening = KodeRekening::findOrFail($id);

            $kodeRekening->delete();

            return redirect()->route('kode-rekening.index')->withStatus('Data berhasil dihapus.');
        }
        catch(\Exception $e){
            return redirect()->route('kode-rekening.index')->withError('Terjadi kesalahan : '. $e->getMessage());
        }
        catch(\Illuminate\Database\QueryException $e){
            return redirect()->route('kode-rekening.index')->withError('Terjadi kesalahan pada database : '. $e->getMessage());
        }
        
    }
}
