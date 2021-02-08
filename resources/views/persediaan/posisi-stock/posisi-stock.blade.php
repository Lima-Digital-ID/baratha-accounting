@extends('common.template')
@section('container')
        {{-- <div class="col-12"> --}}
        @if (session('status'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('status') }}
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
        @endif
        
        <div class="row">
            <div class="col-md-12">
                <form action="{{ url('persediaan/posisi-stock') }}" method="get">
                    <div class="row">

                        <div class="col-md-6 mb-4">
                            <label for="">Barang</label>
                            <select name="kodeBarangDari" id="kodeBarangDari" class="form-control select2" required>
                                <option value="">--Pilih Barang--</option>
                                @foreach ($barang as $item)
                                    <option value="{{$item->kode_barang}}" {{!is_null(Request::get('kodeBarangDari')) && Request::get('kodeBarangDari') == $item->kode_barang ? 'selected' : '' }} >{{$item->kode_barang . ' ~ '.$item->nama}}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-6 mb-4">
                            <label for="">Sampai Barang</label>
                            <select name="kodeBarangSampai" class="form-control select2" required >
                                <option value="">--Pilih Barang--</option>
                                @foreach ($barang as $item)
                                    <option value="{{$item->kode_barang}}" {{!is_null(Request::get('kodeBarangSampai')) && Request::get('kodeBarangSampai') == $item->kode_barang ? 'selected' : '' }} >{{$item->kode_barang . ' ~ '.$item->nama}}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-6 mb-4">
                            <label for="">Tanggal Dari</label>
                            <input type="text" name="tanggalDari" autocomplete="off" class="form-control datepicker" value="{{!is_null(Request::get('tanggalDari')) ? Request::get('tanggalDari') : '' }}" required>
                        </div>
                        
                        <div class="col-md-6 mb-4">
                            <label for="">Tanggal Sampai</label>
                            <input type="text" name="tanggalSampai" autocomplete="off" class="form-control datepicker" value="{{!is_null(Request::get('tanggalSampai')) ? Request::get('tanggalSampai') : ''}}" required>
                        </div>

                        <div class="col-md-6">
                            <button type="submit" class="btn btn-primary"> <i class="fas fa-filter"></i> Filter</button>
                        </div>

                    </div>
                </form>
            </div>
        </div>
        @if ( !is_null(Request::get('kodeBarangDari')) && !is_null(Request::get('tanggalDari')) && !is_null(Request::get('kodeBarangSampai')) && !is_null(Request::get('tanggalSampai')) )
            <br>
            <hr>
            <div class="table-responsive">
                <table class="table table-custom">
                    <thead>
                        <tr>
                            <th>Kode Barang</th>
                            <th>Nama</th>
                            <th>Satuan</th>
                            <th>Qty Awal</th>
                            <th>Saldo Awal</th>
                            <th>Qty Masuk</th>
                            <th>Nominal</th>
                            <th>Qty Keluar</th>
                            <th>Nominal</th>
                            <th>Qty Akhir</th>
                            <th>Saldo Akhir</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                            $totalStockAwal = 0;
                            $totalSaldoAwal = 0;

                            $totalStockMasuk = 0;
                            $totalSaldoMasuk = 0;

                            $totalStockKeluar = 0;
                            $totalSaldoKeluar = 0;

                            $totalStockAkhir = 0;
                            $totalSaldoAkhir = 0;
                        @endphp
                        @foreach ($kodeBarang as $item)
                            @php
                                $stockAwal = 0;
                                $saldoAwal = 0;
                                $stockAkhir = 0;
                                $saldoAkhir = 0;

                                // $data = \DB::table('kartu_stock AS ks')
                                //                     ->select('ks.id', 'ks.tanggal', 'ks.kode_barang', 'ks.kode_transaksi', 'ks.qty', 'ks.nominal', 'ks.tipe')
                                //                     ->join('barang AS b', 'b.kode_barang', '=', 'ks.kode_barang')
                                //                     ->where('ks.kode_barang', $item->kode_barang)
                                //                     ->whereBetween('ks.tanggal', [Request::get('tanggalDari'), Request::get('tanggalSampai')])
                                //                     ->orderBy('ks.tanggal', 'ASC')
                                //                     ->get();

                                $pembelianAwal = \DB::table('kartu_stock')
                                                        ->select(\DB::raw('sum(qty) as qty_pembelian_awal'), \DB::raw('sum(nominal) as nominal_pembelian_awal'))
                                                        ->where('tanggal', '<', Request::get('tanggalDari'))
                                                        ->where('kode_barang', $item->kode_barang)
                                                        ->where('tipe', 'Masuk')
                                                        ->get()[0];

                                $pemakaianAwal = \DB::table('kartu_stock')
                                                        ->select(\DB::raw('sum(qty) as qty_pemakaian_awal'), \DB::raw('sum(nominal) as nominal_pemakaian_awal'))
                                                        ->where('tanggal', '<', Request::get('tanggalDari'))
                                                        ->where('kode_barang', $item->kode_barang)
                                                        ->where('tipe', 'Keluar')
                                                        ->get()[0];
                                
                                $pembelian = \DB::table('kartu_stock')
                                                        ->select(\DB::raw('sum(qty) as qty_pembelian'), \DB::raw('sum(nominal) as nominal_pembelian'))
                                                        ->whereBetween('tanggal', [Request::get('tanggalDari'), Request::get('tanggalSampai')])
                                                        ->where('kode_barang', $item->kode_barang)
                                                        ->where('tipe', 'Masuk')
                                                        ->get()[0];

                                $pemakaian = \DB::table('kartu_stock')
                                                        ->select(\DB::raw('sum(qty) as qty_pemakaian'), \DB::raw('sum(nominal) as nominal_pemakaian'))
                                                        ->whereBetween('tanggal', [Request::get('tanggalDari'), Request::get('tanggalSampai')])
                                                        ->where('kode_barang', $item->kode_barang)
                                                        ->where('tipe', 'Keluar')
                                                        ->get()[0];   

                                $stockAwal = $item->stock_awal + ($pembelianAwal->qty_pembelian_awal - $pemakaianAwal->qty_pemakaian_awal);

                                $saldoAwal = $item->saldo_awal + ($pembelianAwal->nominal_pembelian_awal - $pemakaianAwal->nominal_pemakaian_awal);

                                $qtyPembelian = $pembelian->qty_pembelian;
                                $nominalPembelian = $pembelian->nominal_pembelian;

                                $qtyPemakaian = $pemakaian->qty_pemakaian;
                                $nominalPemakaian = $pemakaian->nominal_pemakaian;

                                $stockAkhir = $stockAwal + $qtyPembelian - $qtyPemakaian;
                                $saldoAkhir = $saldoAwal + $nominalPembelian - $nominalPemakaian;

                                $totalStockAwal += $stockAwal;
                                $totalSaldoAwal += $saldoAwal;

                                $totalStockMasuk += $qtyPembelian;
                                $totalSaldoMasuk += $nominalPembelian;

                                $totalStockKeluar += $qtyPemakaian;
                                $totalSaldoKeluar += $nominalPemakaian;

                                $totalStockAkhir += $stockAkhir;
                                $totalSaldoAkhir += $saldoAkhir;
                            @endphp
                            <tr>
                                <td>{{$item->kode_barang}}</td>
                                <td>{{$item->nama}}</td>
                                <td>{{$item->satuan}}</td>
                                <td>{{$stockAwal}}</td>
                                <td>{{number_format($saldoAwal, 2, ',', '.')}}</td>
                                <td>{{$qtyPembelian}}</td>
                                <td>{{number_format($nominalPembelian, 2, ',', '.')}}</td>
                                <td>{{$qtyPemakaian}}</td>
                                <td>{{number_format($nominalPemakaian, 2, ',', '.')}}</td>
                                <td>{{$stockAkhir}}</td>
                                <td>{{number_format($saldoAkhir, 2, ',', '.')}}</td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr>
                            <th colspan="3" style="text-align: center">Total</th>
                            <th>{{$totalStockAwal}}</th>
                            <th>{{number_format($totalSaldoAwal, 2, ',', '.')}}</th>
                            <th>{{$totalStockMasuk}}</th>
                            <th>{{number_format($totalSaldoMasuk, 2, ',', '.')}}</th>
                            <th>{{$totalStockKeluar}}</th>
                            <th>{{number_format($totalSaldoKeluar, 2, ',', '.')}}</th>
                            <th>{{$totalStockAkhir}}</th>
                            <th>{{number_format($totalSaldoAkhir, 2, ',', '.')}}</th>
                        </tr>
                    </tfoot>
                </table>
            </div>
        @endif
@endsection
