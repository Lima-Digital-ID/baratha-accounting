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
            $data = \DB::table('kartu_hutang')->where('kode_supplier', $item->kode_supplier)->whereBetween('tanggal', [Request::get('tanggalDari'), Request::get('tanggalSampai')])->get();
            $total = 0;
        @endphp
        @if (count($data) > 0)
        <center>
            <h6>Kode Supplier : {{$item->kode_supplier}}</h6>
            <h6>Nama Supplier : {{$item->nama}}</h6>
        </center>
        @endif
        <div class="table-responsive">
            @if (count($data) > 0)
            <table class="table table-custom">
                <thead>
                    <tr>
                        <th colspan="2"></th>
                        <th colspan="2">Masuk</th>
                        <th colspan="2">Keluar</th>
                        <th colspan="3">Saldo Akhir</th>
                    </tr>
                </thead>
                <thead>
                    <tr>
                        <td>#</td>
                        <td>Tanggal</td>
                        <td>Kode Supplier</td>
                        <td>Kode Transaksi</td>
                        <td>Nominal</td>
                        <td>Tipe</td>
                    </tr>
                </thead>
                <tbody>
                    @php
                        $page = Request::get('page');
                        $no = !$page || $page == 1 ? 1 : ($page - 1) * 10 + 1;
                    @endphp
                    @foreach ($data as $value)
                        @php
                            $total += $value->nominal;
                        @endphp
                        <tr>
                            <td>{{$no}}</td>
                            <td>{{$value->tanggal}}</td>
                            <td>{{$value->kode_supplier}}</td>
                            <td>{{$value->kode_transaksi}}</td>
                            <td class="text-right">Rp. {{number_format($value->nominal, 2, ',','.')}}</td>
                            <td>{{$value->tipe}}</td>
                        </tr>
                        @php
                            $no++
                        @endphp
                    @endforeach
                </tbody>
                <thead>
                    <tr>
                        <td colspan="4" class="text-right">Total</td>
                        <td class="text-right">Rp. {{ number_format($total, 2, ',', '.') }}</td>
                        <td></td>
                    </tr>
                </thead>
            </table>
            @endif
        </div>
        @endforeach
        @endif
@endsection
