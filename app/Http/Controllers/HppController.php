<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use \App\Models\Hpp;

class HppController extends Controller
{
    private $param;
    public function __construct()
    {
        $this->param['icon'] = 'fa-cogs';
    }

    public function index(Request $request)
    {
        $this->param['pageInfo'] = 'Hpp / List Hpp';
        $this->param['btnRight']['text'] = 'Tambah Data';
        $this->param['btnRight']['link'] = route('hpp.create');

        try {
            $date = $request->get('date');
            if ($date) {
                $hpp = Hpp::where('tanggal', $date)->paginate(10);
            }
            else{
                $hpp = Hpp::paginate(10);
            }
        } catch (\Illuminate\Database\QueryException $e) {
            return redirect()->back()->withStatus('Terjadi Kesalahan');
        }
                
        return \view('penjualan.hpp.list-hpp', ['hpp' => $hpp], $this->param);
    }

    public function create()
    {
        $this->param['pageInfo'] = 'Hpp / Tambah Data';
        $this->param['btnRight']['text'] = 'Lihat Data';
        $this->param['btnRight']['link'] = route('hpp.index');

        return \view('penjualan.hpp.tambah-hpp', $this->param);
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'tanggal' => 'required|unique:hpp',
            'nominal_hpp' => 'required|gt:0',
        ]);
        
        try{
            $newHpp = new Hpp;
    
            $newHpp->tanggal = $request->get('tanggal');
            $newHpp->nominal_hpp = $request->get('nominal_hpp');
            $newHpp->keterangan = $request->get('keterangan');
    
            $newHpp->save();
    
            return back()->withStatus('Data berhasil ditambahkan.');
        }
        catch(\Exception $e){
            return back()->withStatus('Terjadi kesalahan. : '. $e->getMessage());
        }
        catch(\Illuminate\Database\QueryException $e){
            return back()->withStatus('Terjadi kesalahan pada database : '. $e->getMessage());
        }
    }

    public function edit($id)
    {
        try{
            $this->param['pageInfo'] = 'Hpp / Edit Data';
            $this->param['btnRight']['text'] = 'Lihat Data';
            $this->param['btnRight']['link'] = route('hpp.index');
            $this->param['hpp'] = Hpp::find($id);

            return \view('penjualan.hpp.edit-hpp', $this->param);
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
        $hpp = Hpp::find($id);

        $isDateUnique = $hpp->tanggal == $request->get('tanggal') ? '' : '|unique:hpp';

        $validatedData = $request->validate([
            'tanggal' => 'required'.$isDateUnique,
            'nominal_hpp' => 'required',
        ]);
        try{
            $hpp->tanggal = $request->get('tanggal');
            $hpp->nominal_hpp = $request->get('nominal_hpp');
            $hpp->keterangan = $request->get('keterangan');
            $hpp->save();

            return back()->withStatus('Data berhasil diperbarui.');
        }
        catch(\Exception $e){
            return back()->withError('Terjadi kesalahan : '. $e->getMessage());
        }
        catch(\Illuminate\Database\QueryException $e){
            return back()->withError('Terjadi kesalahan pada database : '. $e->getMessage());
        }
    }

    public function destroy($id)
    {
        try{
            $hpp = Hpp::findOrFail($id);
            $hpp->delete();

            return redirect()->route('hpp.index')->withStatus('Data berhasil dihapus.');
        }
        catch(\Exception $e){
            return $e->getMessage();
            return redirect()->route('hpp.index')->withError('Terjadi kesalahan : '. $e->getMessage());
        }
        catch(\Illuminate\Database\QueryException $e){
            return redirect()->route('hpp.index')->withError('Terjadi kesalahan pada database : '. $e->getMessage());
        }
    }
}
