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

Route::middleware(['auth:sanctum', 'verified'])->get('/dashboard', 'DashboardController@index')->name('dashboard');

Route::group(['middleware' => ['auth:sanctum', 'verified']], function () {
    Route::get('dashboard/cekNotif', 'DashboardController@cekNotif');
    Route::get('dashboard/cekDetailNotif', 'DashboardController@cekDetailNotif');

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
        Route::get('barang-minim', 'BarangController@barangMinim');
        Route::get('barang-expired', 'BarangController@barangExpired');
        Route::get('barang-hampir-expired', 'BarangController@barangHampirExpired');

        Route::get('kartu-stock', 'KartuStockController@index');
        Route::get('posisi-stock', 'KartuStockController@posisiStock');
    });

    Route::group(['prefix' => 'pembelian'], function(){
        Route::get('supplier/hutang', 'SupplierController@getHutang');
        Route::get('supplier/hutang/{kodeSupplier}', 'SupplierController@hutang')->name('hutang-supplier');

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
        Route::get('pembelian-jatuh-tempo', 'PembelianBarangController@pembelianJatuhTempo');
        Route::get('pembelian-jatuh-tempo/detail/{kode}', 'PembelianBarangController@detailPembelianJatuhTempo');
        Route::get('kartu-hutang', 'PembelianBarangController@kartuHutang');
        Route::get('kartu-hutang/get', 'PembelianBarangController@getKartuHutang');
    });
    
    Route::group(['prefix' => 'penjualan'], function(){
        Route::get('piutang-resto', 'PiutangRestoController@index');
        Route::post('piutang-resto/store', 'PiutangRestoController@store');
        Route::get('rekap-hotel', 'RekapHotelController@index');
        Route::get('rekap-hotel/save', 'RekapHotelController@save');
        Route::get('rekap-resto', 'RekapRestoController@index');
        Route::get('rekap-resto/save', 'RekapRestoController@save');
        Route::post('pembayaran-piutang', 'CustomerController@pembayaranPiutang');
        Route::resource('rekap-resto', 'RekapRestoController');
        Route::get('customer/piutang/{kodeCustomer}', 'CustomerController@piutang')->name('piutang-customer');
        Route::resource('customer', 'CustomerController');
        Route::get('penjualan-catering/getKode', 'PenjualanCateringController@getKode');
        Route::resource('penjualan-catering', 'PenjualanCateringController');
        Route::get('penjualan-jatuh-tempo', 'PenjualanCateringController@penjualanJatuhTempo');
        Route::get('kartu-piutang', 'PenjualanCateringController@kartuPiutang');
        Route::get('kartu-piutang/get', 'PenjualanCateringController@getKartuPiutang');
        Route::resource('hpp', 'HppController');
        Route::group(['prefix' => 'laporan-penjualan-catering'], function(){
            Route::get('/', 'PenjualanCateringController@reportPenjualanCatering');
            Route::get('result', 'PenjualanCateringController@getReport')->name('laporan-penjualan-catering');
            Route::get('print', 'PenjualanCateringController@printReport')->name('print-penjualan-catering');
            Route::get('print-invoice', 'PenjualanCateringController@printInvoice')->name('print-invoice-catering');
        });
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
        Route::get('buku-besar/print', 'BukuBesarController@print');
        Route::get('neraca', 'NeracaController@index');
        Route::get('neraca/print', 'NeracaController@print');
        Route::get('laba-rugi', 'LabaRugiController@index');
        Route::get('laba-rugi/print', 'LabaRugiController@print');
        Route::get('ekuitas', 'EkuitasController@index');
        Route::get('ekuitas/print', 'EkuitasController@print');
    });
    
    Route::group(['prefix' => 'log'], function () {
        Route::resource('log-activity', 'LogActivityController');
    });
});