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
        <form class="mr-auto ml-md-3 my-2 my-md-0 mw-100 navbar-search" action="{{ route('pembelian-barang.index') }}" method="get">
            <div class="row">
                <div class="col-2">
                    <a href="{{$btnRight['link']}}" class="btn btn-primary mb-3"> <span class="fa fa-plus-circle"></span> {{$btnRight['text']}}</a>
                </div>
                <div class="col-auto ml-auto">
                    <input type="text" class="form-control start datepicker {{ $errors->has('start') ? ' is-invalid' : '' }}" value="{{ old('start', isset($_GET['start']) ? $_GET['start'] : '') }}" name="start" placeholder="Tanggal" autocomplete="off">
                        @if ($errors->has('start'))
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $errors->first('start') }}</strong>
                            </span>
                        @endif
                </div>
                <div class="col-auto my-auto">
                    <label>s/d</label>
                </div>
                <div class="col-auto">
                    <input type="text" class="form-control end datepicker {{ $errors->has('end') ? ' is-invalid' : '' }}" value="{{ old('end', isset($_GET['end']) ? $_GET['end'] : '') }}" name="end" placeholder="Tanggal" autocomplete="off">    
                    @if ($errors->has('end'))
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $errors->first('end') }}</strong>
                        </span>
                    @endif
                </div>
                <div class="col-auto">
                    <div class="input-group">
                        <input type="text" class="form-control bg-light border-1 small" placeholder="Cari Data..." aria-label="Search" name="keyword" aria-describedby="basic-addon2" value="{{Request::get('keyword')}}">
                        <div class="input-group-append">
                        <button class="btn btn-primary" type="submit">
                            <i class="fas fa-search fa-sm"></i>
                        </button>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                @if (isset($_GET['keyword']) && !isset($_GET['start']) && !isset($_GET['end']))
                <label>Menampilkan data berdasarkan " {{ $_GET['keyword'] }} "</label>
                @elseif (!isset($_GET['keyword']) && isset($_GET['start']) && isset($_GET['end']))
                <label>Menampilkan data dari tanggal {{ isset($_GET['start']) ? $_GET['start'] : '' }} sampai {{ isset($_GET['end']) ? $_GET['end'] : '' }}</label>
                @elseif (isset($_GET['keyword']) && isset($_GET['start']) && isset($_GET['end']))
                <label>Menampilkan data berdasarkan " {{ $_GET['keyword'] }} " dari tanggal {{ $_GET['start'] }} sampai {{ $_GET['end'] }}.</label>
                @endif
            </div>
        </form>
        <div class="table-responsive">
            <table class="table table-custom">
                <thead>
                    <tr>
                        <td>#</td>
                        <td>Kode Pembelian</td>
                        <td>Supplier</td>
                        <td>Tanggal</td>
                        <td>Total Qty</td>
                        <td>Total Harga</td>
                        <td>Total PPN</td>
                        <td>Grandtotal</td>
                        <td>Terbayar</td>
                        <td>Aksi</td>
                    </tr>
                </thead>
                <tbody>
                    @php
                        $page = Request::get('page');
                        $no = !$page || $page == 1 ? 1 : ($page - 1) * 10 + 1;
                    @endphp
                    @foreach ($pembelianBarang as $value)
                        <tr>
                            <td>{{$no}}</td>
                            <td>{{$value->kode_pembelian}}</td>
                            <td>{{$value->supplier->nama}}</td>
                            <td>{{date('d-m-Y', strtotime($value->tanggal))}}</td>
                            <td>{{$value->total_qty}}</td>
                            <td class="text-right">Rp. {{number_format($value->total, 2, ',','.')}}</td>
                            <td class="text-right">Rp. {{number_format($value->total_ppn, 2, ',','.')}}</td>
                            <td class="text-right">Rp. {{number_format($value->grandtotal, 2, ',','.')}}</td>
                            <td class="text-right">Rp. {{number_format($value->terbayar, 2, ',','.')}}</td>
                            <td>
                                {{-- <div class="dropdown dropdown-link">
                                    <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown">
                                        Opsi
                                    </button>
                                    <div class="dropdown-menu">
                                        <a href="{{ route('pembelian-barang.edit', $value->kode_pembelian) }}" class="dropdown-item">{{ __('Edit') }}</a>
                                        <form action="{{ route('pembelian-barang.destroy', $value->kode_pembelian) }}" method="post">
                                            @csrf
                                            @method('delete')
                                            <button type="button" class="ml-1 dropdown-item" onclick="confirm('{{ __("Apakah anda yakin ingin menghapus?") }}') ? this.parentElement.submit() : ''">
                                                {{ __('Hapus') }}
                                            </button>
                                        </form>  
                                    </div>
                                </div> --}}
                                <div class="form-inline">
                                    <a href="{{ route('pembelian-barang.edit', $value) }}" class="btn btn-success mr-2"> <span class="fa fa-pen"></span> </a>
                                    <form action="{{ route('pembelian-barang.destroy', $value) }}" method="post">
                                        @csrf
                                        @method('delete')
                                        <button type="button" class="btn btn-danger" onclick="confirm('{{ __("Apakah anda yakin ingin menghapus?") }}') ? this.parentElement.submit() : ''">
                                            <span class="fa fa-minus-circle"></span>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @php
                            $no++
                        @endphp
                    @endforeach
                </tbody>
            </table>
            {{$pembelianBarang->appends(Request::all())->links('vendor.pagination.custom')}}
        </div>
@endsection
