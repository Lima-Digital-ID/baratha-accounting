<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use \App\Models\Barang;
use \App\Models\KategoriBarang;
use Carbon\Carbon;
use Illuminate\Support\Facades\Date;

class BarangController extends Controller
{
    private $param;
    public function __construct()
    {
        $this->param['icon'] = 'fa-boxes';
    }

    public function index(Request $request)
    {
        $this->param['pageInfo'] = 'Manage Barang / List Barang';
        $this->param['btnRight']['text'] = 'Tambah Data';
        $this->param['btnRight']['link'] = route('barang.create');
        $this->param['kategoriBarang'] = KategoriBarang::get();

        try {
            $keyword = $request->get('keyword');
            $kategori = $request->get('kategori');
            $getBarang = Barang::select('*')->selectRaw('coalesce((select harga_satuan from detail_pembelian_barang where detail_pembelian_barang.kode_barang = barang.kode_barang order by created_at desc limit 1),0) as harga_satuan')->with('kategoriBarang');
            // if ($kategori) {
            //     if ($keyword) {
            //         $barang = Barang::with('kategoriBarang')->where('id_kategori', $kategori)->where('kode_barang', 'LIKE', "%$keyword%")->orWhere('nama', 'LIKE', "%$keyword%")->paginate(10);
            //     }
            //     else{
            //         $barang = Barang::with('kategoriBarang')->where('id_kategori', $kategori)->paginate(10);
            //     }
            // }
            if ($kategori) {
                $getBarang->where('id_kategori', $kategori);
            }

            if($keyword){
                $getBarang->where('kode_barang', 'LIKE', "%$keyword%")->orWhere('nama', 'LIKE', "%$keyword%");
            }
            
            $barang = $getBarang->paginate(10);
            
        } catch (\Illuminate\Database\QueryException $e) {
            return redirect()->back()->withStatus('Terjadi Kesalahan');
        }
                
        return \view('persediaan.barang.list-barang', ['barang' => $barang], $this->param);
    }

    public function create()
    {
        try {
            $this->param['pageInfo'] = 'Manage Barang / Tambah Data';
            $this->param['btnRight']['text'] = 'Lihat Data';
            $this->param['btnRight']['link'] = route('barang.index');
            $this->param['kategoriBarang'] = KategoriBarang::get();
            $kodeBarang = null;
            $data = Barang::orderBy('kode_barang', 'DESC')->get();

            if($data->count() > 0){
                $lastkodeBarang = $data[0]->kode_barang;
    
                $lastIncrement = substr($lastkodeBarang, 2);
    
                $kodeBarang = 'BR'.str_pad($lastIncrement + 1, 5, 0, STR_PAD_LEFT);
            }
            else{
                $kodeBarang = "BR00001";
            }
            $this->param['kode_barang'] = $kodeBarang;
        } catch (\Exception $e) {
            return redirect()->back()->withStatus('Terjadi kesalahan. : '. $e->getMessage());
        }

        return \view('persediaan.barang.tambah-barang', $this->param);
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'kode_barang' => 'required|unique:barang',
            'nama' => 'required',
            'satuan' => 'required',
            'id_kategori' => 'required|not_in:',
            'exp_date' => 'after:'.date('Y-m-d')
        ],
        [
           'required' => ':attribute harus diisi.',
           'unique' => ':attribute telah terpakai.',
           'after' => ':attribute tidak boleh tanggal sebelum hari ini.' 
        ],
        [
            'kode_barang' => 'Kode Barang',
            'nama' => 'Nama',
            'satuan' => 'Satuan',
            'id_kategori' => 'Kategori',
            'exp_date' => 'Expired Date'
        ]);

        try{
            $newBarang = new Barang;
    
            $newBarang->kode_barang = $request->get('kode_barang');
            $newBarang->nama = ucwords($request->get('nama'));
            $newBarang->satuan = $request->get('satuan');
            $newBarang->stock_awal = $request->get('stock_awal');
            $newBarang->saldo_awal = $request->get('saldo_awal');
            $newBarang->stock += $request->get('stock_awal');
            $newBarang->saldo += $request->get('saldo_awal');
            $newBarang->exp_date = $request->get('exp_date');
            $newBarang->keterangan = str_replace(' ', '-', ucwords($request->get('keterangan')));
            $newBarang->tempat_penyimpanan = $request->get('tempat_penyimpanan');
            $newBarang->minimum_stock = $request->get('minimum_stock');
            $newBarang->id_kategori = $request->get('id_kategori');
    
            $newBarang->save();
    
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
            $this->param['pageInfo'] = 'Manage Barang / Edit Data';
            $this->param['btnRight']['text'] = 'Lihat Data';
            $this->param['btnRight']['link'] = route('barang.index');
            $this->param['barang'] = Barang::find($id);
            $this->param['kategoriBarang'] = KategoriBarang::get();

            return \view('persediaan.barang.edit-barang', $this->param);
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
            'satuan' => 'required',
            'id_kategori' => 'required',
            'exp_date' => 'after:yesterday'
        ],
        [
            'required' => ':attribute harus diisi.',
            'after' => ':attribute tidak boleh tanggal sebelum hari ini.' 
         ],
         [
             'nama' => 'Nama',
             'satuan' => 'Satuan',
             'id_kategori' => 'Kategori',
             'exp_date' => 'Expired Date'
         ]);
        
        try{
                
            $barang = Barang::find($id);
            
            // mengembalikan stock dan saldo terlebih dahulu
            if ($barang->stock > 0.00) {
                $barang->stock -= $barang->stock_awal;
                $barang->saldo -= $barang->saldo_awal;
                $barang->save();
            }

            // $barang->kode_barang = $request->get('kode_barang');
            $barang->nama = ucwords($request->get('nama'));
            $barang->satuan = $request->get('satuan');
            $barang->stock_awal = $request->get('stock_awal');
            $barang->saldo_awal = $request->get('saldo_awal');
            $barang->stock += $request->get('stock_awal');
            $barang->saldo += $request->get('saldo_awal');
            $barang->exp_date = $request->get('exp_date');
            $barang->keterangan = str_replace(' ', '-', ucwords($request->get('keterangan')));
            $barang->tempat_penyimpanan = $request->get('tempat_penyimpanan');
            $barang->minimum_stock = $request->get('minimum_stock');
            $barang->id_kategori = $request->get('id_kategori');
            
            $barang->save();

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
            $barang = Barang::findOrFail($id);

            $barang->delete();

            return redirect()->route('barang.index')->withStatus('Data berhasil dihapus.');
        }
        catch(\Exception $e){
            return redirect()->route('barang.index')->withError('Terjadi kesalahan : '. $e->getMessage());
        }
        catch(\Illuminate\Database\QueryException $e){
            return redirect()->route('barang.index')->withError('Terjadi kesalahan pada database : '. $e->getMessage());
        }
        
    }

    public function barangMinim()
    {
        $this->param['pageInfo'] = 'Barang / List Barang Minimum Stok';

        try {
            $this->param['barang'] = Barang::where('minimum_stock', '!=', NULL)->whereRaw('stock <= minimum_stock')->paginate(10);
        } catch (\Illuminate\Database\QueryException $e) {
            return redirect()->back()->withErrors('Terjadi Kesalahan');
        }

        return \view('persediaan.barang.barang-minim-stock', $this->param);
    }

    public function barangExpired()
    {
        $this->param['pageInfo'] = 'Barang / List Barang Kadaluarsa';

        try {
            $this->param['barang'] = Barang::where('exp_date', '<=', Date('Y-m-d'))->paginate(10);
        } catch (\Illuminate\Database\QueryException $e) {
            return redirect()->back()->withErrors('Terjadi Kesalahan');
        }

        return \view('persediaan.barang.barang-expired', $this->param);
    }

    public function barangHampirExpired()
    {
        $this->param['pageInfo'] = 'Barang / List Barang Hampir Kadaluarsa';

        try {
            $exp_date = Carbon::createFromFormat('Y-m-d', Date('Y-m-d'))->addDays(7)->format('Y-m-d');
            $this->param['barang'] = Barang::where('exp_date', '<=', $exp_date)->where('exp_date', '!=', Date('Y-m-d'))->paginate(10);
        } catch (\Illuminate\Database\QueryException $e) {
            return redirect()->back()->withErrors('Terjadi Kesalahan');
        }

        return \view('persediaan.barang.barang-will-expired', $this->param);
    }
}
