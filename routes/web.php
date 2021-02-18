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
    return view('auth.login');
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
        Route::group(['prefix' => 'laporan-pemakaian-barang'], function(){
            Route::get('/', 'PemakaianBarangController@reportPemakaianBarang');
            Route::get('result', 'PemakaianBarangController@getReport')->name('laporan-pemakaian');
            Route::get('print', 'PemakaianBarangController@printReport')->name('print-pemakaian');
        });

        Route::get('kartu-stock', 'KartuStockController@index');
        Route::get('posisi-stock', 'KartuStockController@posisiStock');
    });

    Route::group(['prefix' => 'pembelian'], function(){
        Route::get('supplier/hutang', 'SupplierController@getHutang');
        Route::post('pembayaran-hutang', 'SupplierController@pembayaranHutang');

        Route::resource('supplier', 'SupplierController');
        
        Route::get('pembelian-barang/getKode', 'PembelianBarangController@getKode');
        Route::get('pembelian-barang/addDetailPembelian', 'PembelianBarangController@addDetailPembelian');
        Route::get('pembelian-barang/addEditDetailPembelian', 'PembelianBarangController@addEditDetailPembelian');
        Route::group(['prefix' => 'laporan-pembelian-barang'], function(){
            Route::get('/', 'PembelianBarangController@reportPembelianBarang');
            Route::get('result', 'PembelianBarangController@getReport')->name('laporan-pembelian');
            Route::get('print', 'PembelianBarangController@printReport')->name('print-pembelian');
        });
        Route::resource('pembelian-barang', 'PembelianBarangController');
    });
    
    Route::group(['prefix' => 'penjualan'], function(){
        Route::get('rekap-hotel', 'RekapHotelController@index');
        Route::get('rekap-hotel/save', 'RekapHotelController@save');
        Route::get('rekap-resto', 'RekapRestoController@index');
        Route::get('rekap-resto/save', 'RekapRestoController@save');
        Route::post('pembayaran-piutang', 'CustomerController@pembayaranPiutang');
        Route::resource('rekap-resto', 'RekapRestoController');
        Route::resource('customer', 'CustomerController');
        Route::get('penjualan-catering/getKode', 'PenjualanCateringController@getKode');
        Route::resource('penjualan-catering', 'PenjualanCateringController');
    });

    Route::group(['prefix' => 'kas'], function () {
        Route::get('transaksi-kas/getKode', 'KasController@getKode');
        Route::get('transaksi-kas/addDetailTransaksiKas', 'KasController@addDetailTransaksiKas');
        Route::get('transaksi-kas/addEditDetailTransaksiKas', 'KasController@addEditDetailTransaksiKas');
        Route::resource('transaksi-kas', 'KasController');
        Route::group(['prefix' => 'laporan-kas'], function(){
            Route::get('/', 'KasController@reportKas');
            Route::get('result', 'KasController@getReport')->name('laporan-kas');
            Route::get('print', 'KasController@printReport')->name('print-kas');
        });
    });
    
    Route::group(['prefix' => 'bank'], function () {
        Route::get('transaksi-bank/getKode', 'BankController@getKode');
        Route::get('transaksi-bank/addDetailTransaksiBank', 'BankController@addDetailTransaksiBank');
        Route::get('transaksi-bank/addEditDetailTransaksiBank', 'BankController@addEditDetailTransaksiBank');
        Route::resource('transaksi-bank', 'BankController');
        Route::group(['prefix' => 'laporan-bank'], function(){
            Route::get('/', 'BankController@reportBank');
            Route::get('result', 'BankController@getReport')->name('laporan-bank');
            Route::get('print', 'BankController@printReport')->name('print-bank');
        });
    });
    
    Route::group(['prefix' => 'memorial'], function () {
        Route::get('memorial/getKode', 'MemorialController@getKode');
        Route::get('memorial/addDetailMemorial', 'MemorialController@addDetailMemorial');
        Route::get('memorial/addEditDetailMemorial', 'MemorialController@addEditDetailMemorial');
        Route::resource('memorial', 'MemorialController');
        Route::group(['prefix' => 'laporan-memorial'], function(){
            Route::get('/', 'MemorialController@reportMemorial');
            Route::get('result', 'MemorialController@getReport')->name('laporan-memorial');
            Route::get('print', 'MemorialController@printReport')->name('print-memorial');
        });
    });

    Route::group(['prefix' => 'general-ledger'], function () {
        Route::get('buku-besar', 'BukuBesarController@index');
        Route::get('neraca', 'NeracaController@index');
    });
});