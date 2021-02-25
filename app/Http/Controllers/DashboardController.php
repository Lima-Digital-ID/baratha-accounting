<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use \App\Models\KategoriBarang;
use \App\Models\Barang;
use App\Models\Customer;
use App\Models\Supplier;

class DashboardController extends Controller
{
    public function index()
    {
        $this->param['kategori'] = KategoriBarang::count();
        $this->param['barang'] = Barang::count();
        $this->param['customer'] = Customer::count();
        $this->param['supplier'] = Supplier::count();
        return view('dashboard', $this->param);
    }
}
