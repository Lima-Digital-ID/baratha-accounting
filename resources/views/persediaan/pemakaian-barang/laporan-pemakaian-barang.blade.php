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

        <form action="{{ route('laporan-pemakaian') }}" method="GET">
            <h6 class="heading-small text-muted mb-3">Laporan Pemakaian Barang</h6>
            <div class="row">
                <div class="col-md-4">
                    <label>Tanggal</label><span style="color: red;">*</span>
                    <input type="text" class="form-control getKode datepickerDate {{ $errors->has('start') ? ' is-invalid' : '' }}" value="{{ old('start', isset($_GET['start']) != null ? $_GET['start'] : date('Y-m-d')) }}" name="start" placeholder="Tanggal" autocomplete="off">
                    @if ($errors->has('start'))
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $errors->first('start') }}</strong>
                        </span>
                    @endif

                    <br>
                </div>

                <div class="col-md-4">
                    <label>S/d</label><span style="color: red;">*</span>
                    <input type="text" class="form-control getKode datepickerDate {{ $errors->has('end') ? ' is-invalid' : '' }}" value="{{ old('end', isset($_GET['end']) != null ? $_GET['end'] : date('Y-m-d')) }}" name="end" placeholder="Tanggal" autocomplete="off">
                    @if ($errors->has('end'))
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $errors->first('end') }}</strong>
                        </span>
                    @endif

                    <br>
                </div>

                <div class="col-md-4">
                    <label>Kode Stok</label>
                    <select name="kode_stok" class="form-control select2 {{ $errors->has('kode_stok') ? ' is-invalid' : '' }}">
                        <option value="">-- Semua Kode Stok --</option>
                        @foreach ($barang as $item)
                            @if (isset($_GET['kode_stok']))
                            <option value="{{$item->kode_barang}}" {{ old('kode_stok', $_GET['kode_stok'] == $item->kode_barang ? 'selected' : '') }} >{{$item->kode_barang . ' -- '. $item->nama}}</option>
                            @else
                            <option value="{{$item->kode_barang}}" {{ old('kode_stok') }} >{{$item->kode_barang . ' -- '. $item->nama}}</option>
                            @endif
                        @endforeach
                    </select>
                    @if ($errors->has('kode_stok'))
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $errors->first('kode_stok') }}</strong>
                        </span>
                    @endif

                    <br>
                </div>
                
                <div class="col-md-4">
                    <label>Kode Biaya</label>
                    <select name="kode_biaya" id="kode_biaya" class="form-control select2 {{ $errors->has('kode_biaya') ? ' is-invalid' : '' }}">
                        <option value="">-- Semua Kode Biaya --</option>
                        @foreach ($kode_biaya as $item)
                            @if (isset($_GET['kode_biaya']))
                            <option value="{{$item->kode_biaya}}" {{ old('kode_biaya', $_GET['kode_biaya'] == $item->kode_biaya ? 'selected' : '') }} >{{$item->kode_biaya . ' -- '. $item->nama}}</option>
                            @else
                            <option value="{{$item->kode_biaya}}" {{ old('kode_biaya') }} >{{$item->kode_biaya . ' -- '. $item->nama}}</option>
                            @endif
                        @endforeach
                    </select>
                    @if ($errors->has('kode_biaya'))
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $errors->first('kode_biaya') }}</strong>
                        </span>
                    @endif

                    <br>
                </div>

                <div class="col-md-4">
                    <label>Urutkan Berdasarkan</label>
                    <select name="order" id="order" class="form-control select2 {{ $errors->has('order') ? ' is-invalid' : '' }}">
                        @if (isset($_GET['order']))
                        <option value="tanggal" {{ old('order', $_GET['order'] == 'tanggal' ? 'selected' : '') }} >Tanggal</option>
                        <option value="kode_barang" {{ old('order', $_GET['order'] == 'kode_barang' ? 'selected' : '') }} >Kode Barang</option>
                        <option value="kode_pemakaian" {{ old('order', $_GET['order'] == 'kode_pemakaian' ? 'selected' : '') }} >Kode Transaksi</option>
                        @else
                        <option value="tanggal" {{ old('order') }} >Tanggal</option>
                        <option value="kode_barang" {{ old('order') }} >Kode Barang</option>
                        <option value="kode_pemakaian" {{ old('order') }} >Kode Transaksi</option>
                        @endif
                    </select>
                    @if ($errors->has('order'))
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $errors->first('order') }}</strong>
                        </span>
                    @endif

                    <br>
                </div>
                <div class="col-md-4">
                    <label>Keterangan</label>
                    <select name="keterangan" id="keterangan" class="form-control select2 {{ $errors->has('keterangan') ? ' is-invalid' : '' }}">
                        <option value="">-- Semua Keterangan --</option>
                        @foreach ($keterangan as $item)
                            @if (isset($item->keterangan))
                                <option value="{{$item->keterangan}}" {{isset($_GET['keterangan']) && $_GET['keterangan'] ==  $item->keterangan ? 'selected' : ''}} >{{$item->keterangan}}</option>
                            @endif
                        @endforeach
                    </select>
                    @if ($errors->has('keterangan'))
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $errors->first('keterangan') }}</strong>
                        </span>
                    @endif

                    <br>
                </div>

                <div class="col-md-4 mt-3">
                    <button type="submit" class="btn btn-primary"> <span class="fa fa-filter"></span> Filter</button>
                    <button type="reset" class="btn btn-default mx-2"> <span class="fa fa-times"></span> Cancel</button>
                </div>
            </div>

            @if ($report != null)
            <br>
            <hr class="my-2">

            <div class="row d-flex justify-content-between">
                <div class="col">
                    <h6 class="heading-small text-muted mb-3">Laporan Pemakaian Barang</h6>
                    <h6 class="heading-small text-dark mb-3">Periode {{ \Request::get('start') }} s/d {{ \Request::get('end') }}</h6>
                </div>
                <div class="form-group mr-3">
                    <a href="{{ route('print-pemakaian')."?start=$_GET[start]&end=$_GET[end]&kode_stok=$_GET[kode_stok]&kode_biaya=$_GET[kode_biaya]&keterangan=$_GET[keterangan]&order=$_GET[order]" }}" target="_blank" class="btn btn-primary btn-sm">
                        <i class="fa fa-print" aria-hidden="true"></i> Cetak
                    </a>
                    <a href="{{ route('print-pemakaian')."?start=$_GET[start]&end=$_GET[end]&kode_stok=$_GET[kode_stok]&kode_biaya=$_GET[kode_biaya]&keterangan=$_GET[keterangan]&order=$_GET[order]&xls=true" }}" target="_blank" class="btn btn-success btn-sm">
                        <i class="fa fa-download"></i> Download xls
                    </a>
                </div>
            </div>

            @php
                $total_qty = 0;
                $grandtotal = 0;
            @endphp

            <div class="table-responsive">
                <table class="table table-custom">
                    <thead>
                        <tr>
                            <td>Tanggal</td>
                            <td>Kode Transaksi</td>
                            <td>Kode Barang</td>
                            <td>Keterangan</td>
                            <td>Qty</td>
                            <td>Sat</td>
                            <td>Total</td>
                            <td>Kode Biaya</td>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($report as $item)
                        @php
                            $total_qty += $item->qty;
                            $grandtotal += $item->subtotal;
                        @endphp
                        <tr>
                            <td>{{ $item->tanggal }}</td>
                            <td>{{ $item->kode_pemakaian }}</td>
                            <td>{{ $item->kode_barang }}</td>
                            <td>{{ $item->keterangan != null ? $item->keterangan : '-' }}</td>
                            <td>{{ number_format($item->qty, 2, ',', '.') }}</td>
                            <td>{{ $item->satuan }}</td>
                            <td>{{ number_format($item->subtotal, 2, ',', '.') }}</td>
                            <td>{{ $item->kode_biaya }}</td>
                       </tr>
                        @endforeach
                    </tbody>
                    <thead>
                        <tr>
                            <td colspan="4" class="text-center">
                                Total
                            </td>
                            <td>{{ number_format($total_qty, 2, ',', '.') }}</td>
                            <td></td>
                            <td>{{ number_format($grandtotal, 2, ',', '.') }}</td>
                            <td></td>
                        </tr>
                    </thead>
               </table>
            </div>
            @endif

        </form>
    </div>
</div>
@endsection
