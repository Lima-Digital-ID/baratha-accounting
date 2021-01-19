<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use \App\Models\Perusahaan;

class PerusahaanController extends Controller
{
    private $param;
    public function __construct()
    {
        $this->param['icon'] = 'fa-cog';
    }

    public function index(Request $request)
    {
        try{
            $this->param['pageInfo'] = 'Setup Perusahaan';
            // $this->param['btnRight']['text'] = 'Lihat Data';
            // $this->param['btnRight']['link'] = route('perusahaan.index');
            $this->param['perusahaan'] = Perusahaan::first();

            return \view('data-master.perusahaan.edit-perusahaan', $this->param);
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
            'nama' => 'required',
            'alamat' => 'required',
            'kota' => 'required',
            'provinsi' => 'required',
            'telepon' => 'required',
            'email' => 'required|email',
            ]);

        try{
                
            $perusahaan = Perusahaan::find($id);
            // $perusahaan->kode_induk = $request->get('kode_induk');
            $perusahaan->nama = $request->get('nama');
            $perusahaan->alamat = $request->get('alamat');
            $perusahaan->kota = $request->get('kota');
            $perusahaan->provinsi = $request->get('provinsi');
            $perusahaan->telepon = $request->get('telepon');
            $perusahaan->email = $request->get('email');
            
            $perusahaan->save();

            return redirect()->back()->withStatus('Data berhasil diperbarui.');
        }
        catch(\Exception $e){
            return redirect()->back()->withError('Terjadi kesalahan : '. $e->getMessage());
        }
        catch(\Illuminate\Database\QueryException $e){
            return redirect()->back()->withError('Terjadi kesalahan pada database : '. $e->getMessage());
        }
    }
}
