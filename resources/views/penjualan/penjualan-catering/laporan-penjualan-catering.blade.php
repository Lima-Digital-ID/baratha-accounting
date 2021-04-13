@extends('common.template')
@section('container')

<div class="card shadow py-2">
    <div class="card-body">
        @if (session('status'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('status') }}
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
        @endif
        @if (session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                {{ session('error') }}
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
        @endif

        <form action="{{ route('laporan-penjualan-catering') }}" method="GET">
            <h6 class="heading-small text-muted mb-3">Laporan Penjualan Catering</h6>
            <div class="row">
                <div class="col-md-4">
                    <label>Tanggal</label><span style="color: red;">*</span>
                    <input type="text" class="form-control getKode datepicker {{ $errors->has('start') ? ' is-invalid' : '' }}" value="{{ old('start', isset($_GET['start']) != null ? $_GET['start'] : '') }}" name="start" placeholder="Tanggal" autocomplete="off">
                    @if ($errors->has('start'))
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $errors->first('start') }}</strong>
                        </span>
                    @endif

                    <br>
                </div>

                <div class="col-md-4">
                    <label>S/d</label><span style="color: red;">*</span>
                    <input type="text" class="form-control getKode datepicker {{ $errors->has('end') ? ' is-invalid' : '' }}" value="{{ old('end', isset($_GET['end']) != null ? $_GET['end'] : '') }}" name="end" placeholder="Tanggal" autocomplete="off">
                    @if ($errors->has('end'))
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $errors->first('end') }}</strong>
                        </span>
                    @endif

                    <br>
                </div>

                <div class="col-md-4">
                    <label>Customer</label>
                    <select name="kode_customer" class="form-control select2 {{ $errors->has('kode_customer') ? ' is-invalid' : '' }}">
                        <option value="">-- Semua Customer --</option>
                        @foreach ($customer as $item)
                            @if (isset($_GET['kode_customer']))
                            <option value="{{$item->kode_customer}}" {{ old('kode_customer', $_GET['kode_customer'] == $item->kode_customer ? 'selected' : '') }} >{{$item->kode_customer . ' -- '. $item->nama}}</option>
                            @else
                            <option value="{{$item->kode_customer}}" {{ old('kode_customer') }} >{{$item->kode_customer . ' -- '. $item->nama}}</option>
                            @endif
                        @endforeach
                    </select>
                    @if ($errors->has('kode_customer'))
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $errors->first('kode_customer') }}</strong>
                        </span>
                    @endif

                    <br>
                </div>
                
                <div class="col-md-4">
                    <label>Nilai di Tampilkan</label>
                    <select name="nilai" id="nilai" class="form-control {{ $errors->has('nilai') ? ' is-invalid' : '' }}">
                        @if (isset($_GET['nilai']))
                        <option value="Rekap" {{old('nilai', $_GET['nilai'] == 'Rekap' ? 'selected' : '' ) }} >Rekap</option>
                        <option value="Detail" {{old('nilai', $_GET['nilai'] == 'Detail' ? 'selected' : '' ) }} >Detail</option>    
                        @else
                        <option value="Rekap" {{old('nilai') }} >Rekap</option>
                        <option value="Detail" {{old('nilai') }} >Detail</option>
                        @endif
                    </select>
                    @if ($errors->has('nilai'))
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $errors->first('nilai') }}</strong>
                        </span>
                    @endif

                    <br>
                </div>

                <div class="col-md-4">
                    <label>Urutkan Berdasarkan</label>
                    <select name="order" id="order" class="form-control {{ $errors->has('order') ? ' is-invalid' : '' }}">
                        @if (isset($_GET['order']))
                        <option value="kode_penjualan" {{ old('order', $_GET['order'] == 'kode_penjualan' ? 'selected' : '') }} >Kode Transaksi</option>
                        <option value="kode_customer" {{old('order',  $_GET['order'] == 'kode_customer' ? 'selected' : '') }} >Kode Customer</option>
                        <option value="tanggal" {{old('order',  $_GET['order'] == 'tanggal' ? 'selected' : '') }} >Tanggal</option>
                        @else
                        <option value="kode_penjualan" {{old('order') }} >Kode Transaksi</option>
                        <option value="kode_customer" {{old('order') }} >Kode Customer</option>
                        <option value="tanggal" {{old('order') }} >Tanggal</option>
                        @endif
                    </select>
                    @if ($errors->has('order'))
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $errors->first('order') }}</strong>
                        </span>
                    @endif

                    <br>
                </div>

                <div class="d-flex flex-wrap col-md-4 align-content-center justify-content-end">
                    <button type="reset" class="btn btn-default mx-2"> <span class="fa fa-times"></span> Cancel</button>
                    <button type="submit" class="btn btn-primary"> <span class="fa fa-save"></span> Save</button>
                </div>

            </div>

            @if ($report != null)
            <hr class="my-2">

            <div class="row d-flex justify-content-between">
                <div class="col">
                    <h6 class="heading-small text-muted mb-3">Laporan Penjualan Catering {{ Request::get('nilai') == 'Rekap' ? '(Rekap)' :  '(Detail)' }}</h6>
                    <h6 class="heading-small text-dark mb-3">Periode {{ \Request::get('start') }} s/d {{ \Request::get('end') }}</h6>
                </div>
                <div class="form-group mr-3">
                    <a href="{{ route('print-penjualan-catering')."?start=$_GET[start]&end=$_GET[end]&kode_customer=$_GET[kode_customer]&nilai=$_GET[nilai]&order=$_GET[order]" }}" class="btn btn-primary btn-sm">
                        <i class="fa fa-print" aria-hidden="true"></i> Cetak
                    </a>
                    <a href="{{ route('print-penjualan-catering')."?start=$_GET[start]&end=$_GET[end]&kode_customer=$_GET[kode_customer]&nilai=$_GET[nilai]&order=$_GET[order]&xls=true" }}" class="btn btn-success btn-sm">
                        <i class="fa fa-download"></i> Download xls
                    </a>
                </div>
            </div>

            @php
                $total_qty = 0;
                $total_ppn = 0;
                $grandtotal = 0;
                $total_harga_satuan = 0;
                $total_dpp = 0;
                $total_ppn = 0;
                $total_dpp_ppn = 0;
            @endphp

            <div class="table-responsive">
                @if (Request::get('nilai') == 'Rekap')
                <table class="table table-custom">
                    <thead>
                        <tr>
                            <td>Tanggal</td>
                            <td>Customer</td>
                            <td>Kode Transaksi</td>
                            <td>Jumlah</td>
                            <td>PPN</td>
                            <td>Grandtotal (Rp)</td>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($report as $item)
                        @php
                            $total_qty += $item->qty;
                            $total_ppn += $item->total_ppn;
                            $grandtotal += $item->grandtotal;
                        @endphp
                        <tr>
                           <td>{{ $item->tanggal }}</td>
                           <td>{{ $item->kode_customer }} - {{ $item->nama }}</td>
                           <td>{{ $item->kode_penjualan }}</td>
                           <td>{{ number_format($item->qty, 2, ',', '.') }}</td>
                           <td>{{ number_format($item->total_ppn, 2, ',', '.') }}</td>
                           <td>{{ number_format($item->grandtotal, 2, ',', '.') }}</td>
                       </tr>
                        @endforeach
                    </tbody>
                    <thead>
                        <tr>
                            <td colspan="3" class="text-center">Total</td>
                            <td>{{ number_format($total_qty, 2, ',', '.') }}</td>
                            <td>{{ number_format($total_ppn, 2, ',', '.') }}</td>
                            <td>{{ number_format($grandtotal, 2, ',', '.') }}</td>
                        </tr>
                    </thead>
               </table>
                @else
                <table class="table table-custom">
                    <thead>
                        <tr>
                            <td>Tanggal</td>
                            <td>Customer</td>
                            <td>Kode Transaksi</td>
                            <td>Keterangan</td>
                            <td>Qty</td>
                            <td>Harga Sat.</td>
                            <td>DPP</td>
                            <td>PPN</td>
                            <td>DPP + PPN</td>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($report as $item)
                        @php
                            $total_qty += $item->qty;
                            $total_harga_satuan += $item->harga_satuan;
                            $total_dpp += $item->total;
                            $total_ppn += $item->total_ppn;
                            $total_dpp_ppn += ($item->total + $item->total_ppn);
                        @endphp
                        <tr>
                           <td>{{ $item->tanggal }}</td>
                           <td>{{ $item->kode_customer }} - {{ $item->nama }}</td>
                           <td>{{ $item->kode_penjualan }}</td>
                           <td>{{ $item->keterangan }}</td>
                           <td>{{ $item->qty }}</td>
                           <td>{{ number_format($item->harga_satuan, 2, ',', '.') }}</td>
                           <td>{{ number_format($item->total, 2, ',', '.') }}</td>
                           <td>{{ number_format($item->total_ppn, 2, ',', '.') }}</td>
                           <td>{{ number_format(($item->total + $item->total_ppn), 2, ',', '.') }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                    <thead>
                        <tr>
                            <td colspan="4" class="text-center">Total</td>
                            <td>{{ number_format($total_qty, 2, ',', '.') }}</td>
                            <td>{{ number_format($total_harga_satuan, 2, ',', '.') }}</td>
                            <td>{{ number_format($total_dpp, 2, ',', '.') }}</td>
                            <td>{{ number_format($total_ppn, 2, ',', '.') }}</td>
                            <td>{{ number_format($total_dpp_ppn, 2, ',', '.') }}</td>
                        </tr>
                    </thead>
               </table>
                @endif
            </div>
            @endif

        </form>
    </div>
</div>
@endsection
