<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Route::middleware(['auth:sanctum', 'verified'])->get('/dashboard', function () {
    return view('dashboard');
})->name('dashboard');

Route::group(['middleware' => ['auth:sanctum', 'verified']], function () {
    Route::group(['prefix' => 'data-master'], function () {
        Route::resource('user', 'UserController');
        Route::resource('perusahaan', 'PerusahaanController');
    });

    Route::group(['prefix' => 'master-akuntansi'], function () {
        Route::resource('kode-induk', 'KodeIndukController');
        Route::resource('kode-rekening', 'KodeRekeningController');
        Route::resource('kode-biaya', 'KodeBiayaController');
    });
    
    Route::group(['prefix' => 'persediaan'], function () {
        Route::resource('kategori-barang', 'KategoriBarangController');
        Route::resource('barang', 'BarangController');
    });

    Route::group(['prefix' => 'pembelian'], function(){
        Route::resource('supplier', 'SupplierController');
    });

    Route::group(['prefix' => 'penjualan'], function(){
        Route::resource('customer', 'CustomerController');
    });
});