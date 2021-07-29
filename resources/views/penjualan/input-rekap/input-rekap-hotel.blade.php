@extends('common.template')
@section('container')
    <div class="row">
        <div class="col-md-4">
            <div class="card shadow py-2">
                <div class="card-body">
                    <form action="" method="" id="rekap-hotel">
                        <label for="">Tanggal</label>
                        <div class="input-group mb-3">
                            <div class="input-group-prepend">
                                <span class="input-group-text" id="basic-addon1"><span class="fa fa-calendar"></span></span>
                            </div>
                            <input type="text" class="form-control datepickerDate"
                                value="{{ isset($_GET['tanggal']) ? $_GET['tanggal'] : date('Y-m-d') }}" name="tanggal"
                                placeholder="Pilih Tanggal" autocomplete="off" required>
                        </div>
                        <button type="submit" class="btn btn-primary"> <span class="fa fa-filter"></span> Filter</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    @if (session('status'))
        <div class="alert alert-success alert-dismissible mt-3 fade show" role="alert">
            {{ session('status') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif
    @if (session('error'))
        <div class="alert alert-danger alert-dismissible mt-3 fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif
    @if (isset($_GET['tanggal']))
        {{-- @php
        
        echo "<pre>";
        print_r ($json);
        echo "</pre>";
        
    @endphp --}}
        @if (isset($json) && $json['message'] != 'Kosong')
            @if (isset($status))
                <div class="alert alert-success font-weight-bold mt-3">
                    Rekap pada tanggal {{ date('d-m-Y', strtotime($_GET['tanggal'])) }} sudah dilakukan.
                </div>

            @endif
            <div class="table-responsive">
                <table class="table table-custom">
                    <thead>
                        <tr>
                            <td>#</td>
                            <td>Tanggal</td>
                            <td>Metode Pembayaran</td>
                            <td>Total</td>
                            <td>Total PPN</td>
                            <td>Status</td>
                            <td>Aksi</td>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($json['data'] as $item)
                            @php
                                $cek = \App\Models\Rekap_hotel::where('tanggal', $_GET['tanggal'])
                                    ->where('jenis_bayar', $item['jenis_bayar'])
                                    ->count();
                            @endphp
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ date('d-m-Y', strtotime($_GET['tanggal'])) }}</td>
                                <td>{{ $item['jenis_bayar'] }}</td>
                                <td>Rp{{ number_format($item['total'], 2, ',', '.') }}</td>
                                <td>Rp{{ number_format($item['total_ppn'], 2, ',', '.') }}</td>
                                <td>
                                    @if ($cek == 0)
                                        <span class="badge badge-primary">Belum ditarik</span>
                                    @elseif($cek == 1)
                                        <span class="badge badge-success">Sudah ditarik</span>
                                    @endif
                                </td>
                                <td>
                                    {{-- <a href="{{ url('penjualan/rekap-resto/save?tanggal=' . $_GET['tanggal']) }}"
                                        data-alert='Akan dilakukan penarikan data'
                                        class="confirm-alert btn btn-default"><span class="fa fa-download"></span> Tarik
                                        Data</a> --}}
                                    @if ($cek == 0)
                                        <a class="btn btn-primary tarikData" href="#" data-toggle="modal"
                                            data-target="#tarikData" data-jenis_bayar="{{ $item['jenis_bayar'] }}"
                                            data-total="{{ $item['total'] }}"
                                            data-total_ppn="{{ $item['total_ppn'] }}">
                                            <i class="fa fa-download fa-sm fa-fw mr-2 text-gray-400"></i>
                                            Tarik Data
                                        </a>
                                    @elseif($cek == 1)
                                        <a class="btn btn-success" href="#">
                                            <i class="fa fa-check fa-sm fa-fw mr-2 text-gray-400"></i>
                                            Sudah ditarik
                                        </a>
                                    @endif

                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Tarik Rekap Modal-->
            <div class="modal fade" id="tarikData" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
                aria-hidden="true">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="exampleModalLabel">Rekap Penjualan Resto</h5>
                            <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">×</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <form action="{{ url('penjualan/rekap-hotel/save') }}" method="get">
                                <div class="form-group">
                                    <label for="">Tanggal</label>
                                    <input type="text" name="tanggal" class="form-control" id="tanggal"
                                        value="{{ $_GET['tanggal'] }}" step=".01" readonly>
                                </div>
                                <div class="form-group">
                                    <label for="">Kode Rekening</label>
                                    <select name="kode_rekening" class="form-control select2" id="" required autofocus>
                                        <option value="">Pilih Rekening</option>
                                        @foreach ($kodeRekening as $item)
                                            <option value="{{ $item->kode_rekening }}">
                                                {{ $item->kode_rekening . ' - ' . $item->nama }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label for="">Metode Pembayaran</label>
                                    <input type="text" name="jenis_bayar" class="form-control" id="jenis_bayar" step=".01"
                                        readonly>
                                </div>
                                <div class="form-group">
                                    <label for="">Total</label>
                                    <input type="text" name="total" class="form-control" id="total" step=".01" readonly>
                                </div>
                                <div class="form-group">
                                    <label for="">Total PPN</label>
                                    <input type="text" name="total_ppn" class="form-control" id="total_ppn" step=".01"
                                        readonly>
                                </div>

                        </div>
                        <div class="modal-footer">
                            <button class="btn btn-secondary" type="button" data-dismiss="modal">
                                Cancel
                            </button>
                            <button type="submit" class="btn btn-primary">
                                Proses
                            </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        @else
            <div class="alert alert-info font-weight-bold mt-3">
                Tidak ada rekap data pada {{ $_GET['tanggal'] }}.
            </div>
        @endif

    @endif
@endsection
