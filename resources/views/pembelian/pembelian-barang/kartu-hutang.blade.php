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
        {{-- </div> --}}
        <div class="row">
            <div class="col-md-12">
                <form action="{{ url('pembelian/kartu-hutang/get') }}" method="get">
                    <div class="row">

                        <div class="col-md-6 mb-4">
                            <label for="">Supplier</label><span style="color:red;">*</span>
                            <select name="kodeSupplierDari" id="kodeSupplierDari" class="form-control select2" required>
                                <option value="">--Pilih Supplier--</option>
                                @foreach ($supplier as $item)
                                    <option value="{{$item->kode_supplier}}" {{!is_null(Request::get('kodeSupplierDari')) && Request::get('kodeSupplierDari') == $item->kode_supplier ? 'selected' : '' }} >{{$item->kode_supplier . ' ~ '.$item->nama}}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-6 mb-4">
                            <label for="">Sampai Supplier</label><span style="color:red;">*</span>
                            <select name="kodeSupplierSampai" class="form-control select2" required >
                                <option value="">--Pilih Supplier--</option>
                                @foreach ($supplier as $item)
                                    <option value="{{$item->kode_supplier}}" {{!is_null(Request::get('kodeSupplierSampai')) && Request::get('kodeSupplierSampai') == $item->kode_supplier ? 'selected' : '' }} >{{$item->kode_supplier . ' ~ '.$item->nama}}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-6 mb-4">
                            <label for="">Tanggal Dari</label><span style="color:red;">*</span>
                            <input type="text" name="tanggalDari" autocomplete="off" class="form-control datepicker" value="{{!is_null(Request::get('tanggalDari')) ? Request::get('tanggalDari') : '' }}" required>
                        </div>
                        
                        <div class="col-md-6 mb-4">
                            <label for="">Tanggal Sampai</label><span style="color:red;">*</span>
                            <input type="text" name="tanggalSampai" autocomplete="off" class="form-control datepicker" value="{{!is_null(Request::get('tanggalSampai')) ? Request::get('tanggalSampai') : ''}}" required>
                        </div>

                        <div class="col-md-6">
                            <button type="submit" class="btn btn-primary"> <i class="fas fa-filter"></i> Filter</button>
                        </div>

                    </div>
                </form>
            </div>
        </div>
        @if (!is_null(Request::get('kodeSupplierDari')) && !is_null(Request::get('kodeSupplierSampai')))
        @foreach ($selectedSupplier as $item)
        @php
            $data = \DB::table('kartu_hutang')->where('kartu_hutang.kode_supplier', $item->kode_supplier)->whereBetween('kartu_hutang.tanggal', [Request::get('tanggalDari'), Request::get('tanggalSampai')])->get();
            $saldo_awal = \DB::table('kartu_hutang')->select(\DB::raw('SUM(kartu_hutang.nominal) AS saldo'))->where('kartu_hutang.kode_supplier', $item->kode_supplier)->where('kartu_hutang.tanggal', '<', Request::get('tanggalDari'))->get();
            $saldo_awal = $saldo_awal[0]->saldo;
            $total_masuk = 0;
            $total_keluar = 0;
            $saldo_akhir = 0;
        @endphp
        <center>
            <h6>Kode Supplier : {{$item->kode_supplier}}</h6>
            <h6>Nama Supplier : {{$item->nama}}</h6>
        </center>
        <div class="table-responsive">
            <table class="table table-custom">
                <thead>
                    <tr>
                        <td>#</td>
                        <td>Tanggal</td>
                        <td>Kode Transaksi</td>
                        <td class="text-center">Pembelian</td>
                        <td class="text-center">Pembayaran</td>
                        <td class="text-center">Saldo Akhir</td>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>#</td>
                        <td>-</td>
                        <td>Saldo Awal</td>
                        <td class="text-center">-</td>
                        <td class="text-center">-</td>
                        <td class="text-center">Rp. {{ number_format($saldo_awal, 2, ',', '.') }}</td>
                    </tr>
                    @php
                        $page = Request::get('page');
                        $no = !$page || $page == 1 ? 1 : ($page - 1) * 10 + 1;
                    @endphp
                    @foreach ($data as $value)
                        <tr>
                            <td>{{$no}}</td>
                            <td>{{$value->tanggal}}</td>
                            <td>{{$value->kode_transaksi}}</td>
                            <td class="text-center">
                                @if (!is_null($value->nominal) && $value->tipe == 'Pembelian')
                                Rp. {{number_format($value->nominal, 2, ',','.')}}
                                @php
                                    $total_masuk += $value->nominal;
                                    $saldo_akhir += $value->nominal;
                                @endphp
                                @else
                                -
                                @endif
                            </td>
                            <td class="text-center">
                                @if (!is_null($value->nominal) && $value->tipe == 'Pembayaran')
                                Rp. {{number_format($value->nominal, 2, ',','.')}}
                                @php
                                    $total_keluar += $value->nominal;
                                    $saldo_akhir -= $value->nominal;
                                @endphp
                                @else
                                -
                                @endif
                            </td>
                            <td class="text-center">Rp. {{ number_format($saldo_akhir, 2, ',', '.') }}</td>
                        </tr>
                        @php
                            $no++
                        @endphp
                    @endforeach
                </tbody>
                <thead>
                    <tr>
                        <td colspan="3" class="text-right">Total</td>
                        <td class="text-center">Rp. {{ number_format($total_masuk, 2, ',', '.') }}</td>
                        <td class="text-center">Rp. {{ number_format($total_keluar, 2, ',', '.') }}</td>
                        <td></td>
                    </tr>
                </thead>
            </table>
            <br>
        </div>
        @endforeach
        @endif
@endsection
