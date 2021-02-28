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
                <form action="{{ url('penjualan/kartu-piutang/get') }}" method="get">
                    <div class="row">

                        <div class="col-md-6 mb-4">
                            <label for="">Customer</label><span style="color:red;">*</span>
                            <select name="kodeCustomerDari" id="kodeCustomerDari" class="form-control select2" required>
                                <option value="">--Pilih Customer--</option>
                                @foreach ($customer as $item)
                                    <option value="{{$item->kode_customer}}" {{!is_null(Request::get('kodeCustomerDari')) && Request::get('kodeCustomerDari') == $item->kode_customer ? 'selected' : '' }} >{{$item->kode_customer . ' ~ '.$item->nama}}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-6 mb-4">
                            <label for="">Sampai Customer</label><span style="color:red;">*</span>
                            <select name="kodeCustomerSampai" class="form-control select2" required >
                                <option value="">--Pilih Customer--</option>
                                @foreach ($customer as $item)
                                    <option value="{{$item->kode_customer}}" {{!is_null(Request::get('kodeCustomerSampai')) && Request::get('kodeCustomerSampai') == $item->kode_customer ? 'selected' : '' }} >{{$item->kode_customer . ' ~ '.$item->nama}}</option>
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
        @if (!is_null(Request::get('kodeCustomerDari')) && !is_null(Request::get('kodeCustomerSampai')))
        @foreach ($selectedCustomer as $item)
        @php
            $data = \DB::table('kartu_piutang')->where('kode_customer', $item->kode_customer)->get();
            $total = 0;
        @endphp
        @if (count($data) > 0)
        <center>
            <h6>Kode Customer : {{$item->kode_customer}}</h6>
            <h6>Nama Customer : {{$item->nama}}</h6>
        </center>
        @endif
        <div class="table-responsive">
            @if (count($data) > 0)
            <table class="table table-custom">
                <thead>
                    <tr>
                        <td>#</td>
                        <td>Tanggal</td>
                        <td>Kode Customer</td>
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
                            <td>{{$value->kode_customer}}</td>
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
