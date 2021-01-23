<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use \App\Models\KunciTransaksi;

class KunciTransaksiController extends Controller
{
    private $param;
    public function __construct()
    {
        $this->param['icon'] = 'fa-money-bill-wave';
    }

    public function index(Request $request)
    {
        $this->param['pageInfo'] = 'Manage Kunci Transaksi / List Kunci Transaksi';
        $this->param['btnRight']['text'] = 'Tambah Data';
        $this->param['btnRight']['link'] = route('kunci-transaksi.create');

        try {
            $keyword = $request->get('keyword');
            if ($keyword) {
                $kunciTransaksi = KunciTransaksi::where('jenis_transaksi', 'LIKE', "%$keyword%")->paginate(10);
            }
            else{
                $kunciTransaksi = KunciTransaksi::paginate(10);
            }
        } catch (\Illuminate\Database\QueryException $e) {
            return redirect()->back()->withStatus('Terjadi Kesalahan');
        }
                
        return \view('master-akuntansi.kunci-transaksi.list-kunci-transaksi', ['kunciTransaksi' => $kunciTransaksi], $this->param);
    }

    public function edit($id)
    {
        try{
            $this->param['pageInfo'] = 'Manage Kode Induk / Edit Data';
            $this->param['btnRight']['text'] = 'Lihat Data';
            $this->param['btnRight']['link'] = route('kunci-transaksi.index');
            $this->param['data'] = KunciTransaksi::all();
            $this->param['kunciTransaksi'] = KunciTransaksi::find($id);

            return \view('master-akuntansi.kunci-transaksi.edit-kunci-transaksi', $this->param);
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
        $kunciTransaksi = KunciTransaksi::find($id);

        $validatedData = $request->validate([
            'jenis_transaksi' => 'required',
            'tanggal_kunci' => 'required',
        ]);

        try{
            $kunciTransaksi->tanggal_kunci = $request->get('tanggal_kunci');
            
            $kunciTransaksi->save();

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
