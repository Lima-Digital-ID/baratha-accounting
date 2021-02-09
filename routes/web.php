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
        Route::resource('kunci-transaksi', 'KunciTransaksiController');
    });
    
    Route::group(['prefix' => 'persediaan'], function () {
        Route::resource('kategori-barang', 'KategoriBarangController');
        Route::resource('barang', 'BarangController');

        Route::get('pemakaian-barang/getKode', 'PemakaianBarangController@getKode');
        Route::get('pemakaian-barang/getStock', 'PemakaianBarangController@getStock');
        Route::get('pemakaian-barang/addDetailPemakaian', 'PemakaianBarangController@addDetailPemakaian');
        Route::get('pemakaian-barang/addEditDetailPemakaian', 'PemakaianBarangController@addEditDetailPemakaian');
        Route::resource('pemakaian-barang', 'PemakaianBarangController');

        Route::get('kartu-stock', 'KartuStockController@index');
        Route::get('posisi-stock', 'KartuStockController@posisiStock');
    });

    Route::group(['prefix' => 'pembelian'], function(){
        Route::resource('supplier', 'SupplierController');
        
        Route::get('pembelian-barang/getKode', 'PembelianBarangController@getKode');
        Route::get('pembelian-barang/addDetailPembelian', 'PembelianBarangController@addDetailPembelian');
        Route::get('pembelian-barang/addEditDetailPembelian', 'PembelianBarangController@addEditDetailPembelian');
        Route::resource('pembelian-barang', 'PembelianBarangController');
    });
    
    Route::group(['prefix' => 'penjualan'], function(){
        Route::get('rekap-hotel', 'RekapHotelController@index');
        Route::get('rekap-hotel/save', 'RekapHotelController@save');
        Route::get('rekap-resto', 'RekapRestoController@index');
        Route::get('rekap-resto/save', 'RekapRestoController@save');
        Route::resource('rekap-resto', 'RekapRestoController');
        Route::resource('customer', 'CustomerController');
    });

    Route::group(['prefix' => 'kas'], function () {
        Route::get('transaksi-kas/getKode', 'KasController@getKode');
        Route::get('transaksi-kas/addDetailTransaksiKas', 'KasController@addDetailTransaksiKas');
        Route::get('transaksi-kas/addEditDetailTransaksiKas', 'KasController@addEditDetailTransaksiKas');
        Route::resource('transaksi-kas', 'KasController');
    });
});