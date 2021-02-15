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

        <form action="{{ route('laporan-memorial') }}" method="GET">
            <h6 class="heading-small text-muted mb-3">Laporan Memorial</h6>
            <div class="row">

                <div class="col-md-6">
                    <label>Tanggal</label><span style="color: red;">*</span>
                    <input type="text" class="form-control datepicker {{ $errors->has('start') ? ' is-invalid' : '' }}" value="{{ old('start', isset($_GET['start']) != null ? $_GET['start'] : '') }}" name="start" placeholder="Tanggal" autocomplete="off">
                    @if ($errors->has('start'))
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $errors->first('start') }}</strong>
                        </span>
                    @endif

                    <br>
                </div>

                <div class="col-md-6">
                    <label>S/d</label><span style="color: red;">*</span>
                    <input type="text" class="form-control datepicker {{ $errors->has('end') ? ' is-invalid' : '' }}" value="{{ old('end', isset($_GET['end']) != null ? $_GET['end'] : '') }}" name="end" placeholder="Tanggal" autocomplete="off">
                    @if ($errors->has('end'))
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $errors->first('end') }}</strong>
                        </span>
                    @endif

                    <br>
                </div>

                <div class="d-flex flex-wrap col-md-12 align-content-center justify-content-end">
                    <button type="reset" class="btn btn-default mx-2"> <span class="fa fa-times"></span> Cancel</button>
                    <button type="submit" class="btn btn-primary"> <span class="fa fa-save"></span> Save</button>
                </div>
            </div>

            @if ($report != null)
            <hr class="my-2">

            <div class="row d-flex justify-content-between">
                <div class="col">
                    <h6 class="heading-small text-muted mb-3">Laporan Memorial</h6>
                    <h6 class="heading-small text-dark mb-3">Periode {{ \Request::get('start') }} s/d {{ \Request::get('end') }}</h6>
                </div>
                <div class="form-group mt-3 mr-3">
                    <a href="{{ route('print-memorial')."?start=$_GET[start]&end=$_GET[end]" }}" class="btn btn-primary btn-sm">
                        <i class="fa fa-print" aria-hidden="true"></i> Cetak
                    </a>
                    <a href="{{ route('print-memorial')."?start=$_GET[start]&end=$_GET[end]&xls=true" }}" class="btn btn-success btn-sm">
                        <i class="fa fa-download"></i> Download xls
                    </a>
                </div>
            </div>

            <div class="table-responsive">
                <table class="table table-custom">
                    <thead>
                        <tr>
                            <td>Tanggal</td>
                            <td>Kode Transaksi</td>
                            <td>Keterangan</td>
                            <td>Pasangan</td>
                            <td>Penerimaan</td>
                            <td>Pengeluaran</td>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($report as $item)
                        <tr>
                            <td>{{ $item->tanggal }}</td>
                            <td>{{ $item->kode_memorial }}</td>
                            <td>{{ $item->keterangan }}</td>
                            <td>{{ $item->lawan }}</td>
                            <td>
                                @if ($item->tipe == 'Masuk')
                                    {{ $item->subtotal }}
                                @else
                                    -
                                @endif
                            </td>
                            <td>
                                @if ($item->tipe == 'Keluar')
                                    {{ $item->subtotal }}
                                @else
                                    -
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
               </table>
            </div>
            @endif

        </form>
    </div>
</div>
@endsection