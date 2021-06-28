<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use \App\Models\KategoriBarang;
use \App\Models\Barang;
use App\Models\Customer;
use App\Models\Supplier;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        try {
            $this->param['kategori'] = KategoriBarang::count();
            $this->param['barang'] = Barang::count();
            $this->param['customer'] = Customer::count();
            $this->param['supplier'] = Supplier::count();
            $this->param['minim_stock'] = Barang::where('minimum_stock', '!=', NULL)->whereRaw('stock <= minimum_stock')->paginate(10);
            $exp_date = Carbon::createFromFormat('Y-m-d', Date('Y-m-d'))->addDays(7)->format('Y-m-d');
            $this->param['barang_will_expired'] = Barang::where('exp_date', '<=', $exp_date)->where('exp_date', '!=', Date('Y-m-d'))->get();
            $this->param['barang_expired'] = Barang::where('exp_date', '=', Date('Y-m-d'))->get();
        } catch (\Illuminate\Database\QueryException $e) {
            return redirect()->back()->withErrors('Terjadi Kesalahan');
        }
        return view('dashboard', $this->param);
    }

    // public function cekNotif()
    // {
    //     $count = Customer::all()->count();
    //     echo $count;
    // }

    // public function cekDetailNotif()
    // {
    //     # code...
    // }
}
