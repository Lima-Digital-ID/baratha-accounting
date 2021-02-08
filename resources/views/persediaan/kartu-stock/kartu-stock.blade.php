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
                <form action="{{ url('persediaan/kartu-stock') }}" method="get">
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
            @foreach ($kodeBarang as $item)
                @php
                    $stockAwal = 0;
                    $saldoAwal = 0;
                    $stockAkhir = 0;
                    $saldoAkhir = 0;

                    $totalStockMasuk = 0;
                    $totalSaldoMasuk = 0;
                    $totalStockKeluar = 0;
                    $totalSaldoKeluar = 0;

                    $data = \DB::table('kartu_stock AS ks')
                                        ->select('ks.id', 'ks.tanggal', 'ks.kode_barang', 'ks.kode_transaksi', 'ks.qty', 'ks.nominal', 'ks.tipe')
                                        ->join('barang AS b', 'b.kode_barang', '=', 'ks.kode_barang')
                                        ->where('ks.kode_barang', $item->kode_barang)
                                        ->whereBetween('ks.tanggal', [Request::get('tanggalDari'), Request::get('tanggalSampai')])
                                        ->orderBy('ks.tanggal', 'ASC')
                                        ->get();

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
                    
                    $stockAwal = $item->stock_awal + ($pembelianAwal->qty_pembelian_awal - $pemakaianAwal->qty_pemakaian_awal);

                    $saldoAwal = $item->saldo_awal + ($pembelianAwal->nominal_pembelian_awal - $pemakaianAwal->nominal_pemakaian_awal);

                    $stockAkhir = $stockAwal;
                    $saldoAkhir = $saldoAwal;

                    // echo "<pre>";
                    // print_r ($pembelianAwal);
                    // echo "</pre>";

                    // echo "<pre>";
                    // print_r ($pemakaianAwal);
                    // echo "</pre>";
                    

                @endphp
                <center>
                    <h6>Kode Barang : {{$item->kode_barang}}</h6>
                    <h6>Nama Barang : {{$item->nama}}</h6>
                </center>
                <div class="table-responsive">
                    <table class="table table-custom">
                        <thead>
                            <tr>
                                <th colspan="2"></th>
                                <th colspan="2">Masuk</th>
                                <th colspan="2">Keluar</th>
                                <th colspan="3">Saldo Akhir</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <th>Tanggal</th>
                                <th>Kode Transaksi</th>
                                <th>Qty</th>
                                <th>Nominal</th>
                                <th>Qty</th>
                                <th>Nominal</th>
                                <th>Qty</th>
                                <th>HPP</th>
                                <th>Nominal</th>
                            </tr>
                            <tr>
                                <td>-</td>
                                <td>Saldo Awal</td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td>{{$stockAwal}}</td>
                                <td>{{number_format($saldoAwal == 0.00 && $stockAwal == 0.00 ? 0.00 : $saldoAwal / $stockAwal, 2, ',', '.')}}</td>
                                <td>{{number_format($saldoAwal, 2, ',', '.')}}</td>
                            </tr>
                            @foreach ($data as $value)
                                <tr>
                                    <td>{{$value->tanggal}}</td>
                                    <td>{{$value->kode_transaksi}}</td>
                                    @if ($value->tipe == 'Masuk')
                                        @php
                                            $stockAkhir += $value->qty;
                                            $saldoAkhir += $value->nominal;

                                            $totalStockMasuk += $value->qty;
                                            $totalSaldoMasuk += $value->nominal;
                                        @endphp
                                        <td>{{$value->qty}}</td>
                                        <td>{{number_format($value->nominal, 2, ',', '.')}}</td>
                                        <td></td>
                                        <td></td>
                                    @else
                                        @php
                                            $stockAkhir -= $value->qty;
                                            $saldoAkhir -= $value->nominal;

                                            $totalStockKeluar += $value->qty;
                                            $totalSaldoKeluar += $value->nominal;
                                        @endphp
                                        <td></td>
                                        <td></td>
                                        <td>{{$value->qty}}</td>
                                        <td>{{number_format($value->nominal, 2, ',', '.')}}</td>
                                    @endif
                                    <td>{{$stockAkhir}}</td>
                                    <td>{{number_format($saldoAkhir / $stockAkhir, 2, ',', '.')}}</td>
                                    <td>{{number_format($saldoAkhir, 2, ',', '.')}}</td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr>
                                <th colspan="2" style="text-align: center">Total</th>
                                <th>{{$totalStockMasuk}}</th>
                                <th>{{number_format($totalSaldoMasuk, 2, ',', '.')}}</th>
                                <th>{{$totalStockKeluar}}</th>
                                <th>{{number_format($totalSaldoKeluar, 2, ',', '.')}}</th>
                                <th colspan="3"></th>
                            </tr>
                        </tfoot>
                    </table>
                </div>
                <hr>
            @endforeach
        @endif
@endsection
