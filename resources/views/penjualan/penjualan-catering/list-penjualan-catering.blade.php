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
          <div class="col-2">
            <a href="{{$btnRight['link']}}" class="btn btn-primary mb-3"> <span class="fa fa-plus-circle"></span> {{$btnRight['text']}}</a>
          </div>
          <div class="col-auto ml-auto">
            <form class="d-none d-sm-inline-block form-inline mr-auto ml-md-3 my-2 my-md-0 mw-100 navbar-search" action="{{ route('penjualan-catering.index') }}" method="get">
              <div class="input-group">
                <input type="text" class="form-control bg-light border-1 small" placeholder="Cari Data..." aria-label="Search" name="keyword" aria-describedby="basic-addon2" value="{{Request::get('keyword')}}">
                <div class="input-group-append">
                  <button class="btn btn-primary" type="submit">
                    <i class="fas fa-search fa-sm"></i>
                  </button>
                </div>
              </div>
            </form>
          </div>
        </div>
        <div class="table-responsive">
            <table class="table table-custom">
                <thead>
                    <tr>
                        <td>#</td>
                        <td>Tanggal</td>
                        <td>Kode Penjualan</td>
                        <td>Customer</td>
                        <td>Qty</td>
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
                    @foreach ($penjualanCatering as $value)
                        <tr>
                            <td>{{$no}}</td>
                            <td>{{date('d-m-Y', strtotime($value->tanggal))}}</td>
                            <td>{{$value->kode_penjualan}}</td>
                            <td>{{$value->customer->nama}}</td>
                            <td>{{$value->qty}}</td>
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
                                        <a href="{{ route('penjualan-catering.edit', $value->kode_penjualan) }}" class="dropdown-item">{{ __('Edit') }}</a>
                                        <form action="{{ route('penjualan-catering.destroy', $value->kode_penjualan) }}" method="post">
                                            @csrf
                                            @method('delete')
                                            <button type="button" class="ml-1 dropdown-item" onclick="confirm('{{ __("Apakah anda yakin ingin menghapus?") }}') ? this.parentElement.submit() : ''">
                                                {{ __('Hapus') }}
                                            </button>
                                        </form>  
                                    </div>
                                </div> --}}
                                <div class="form-inline">
                                    <a href="{{ route('penjualan-catering.edit', $value) }}" class="btn btn-success mr-2"> <span class="fa fa-pen"></span> </a>
                                    <form action="{{ route('penjualan-catering.destroy', $value) }}" method="post">
                                        @csrf
                                        @method('delete')
                                        <button type="button" class="btn btn-danger" onclick="confirm('{{ __("Apakah anda yakin ingin menghapus?") }}') ? this.parentElement.submit() : ''">
                                            <span class="fa fa-minus-circle"></span>
                                        </button>
                                    </form>
                                    <a target="_blank" href="{{ route('print-invoice-catering')."?kode_penjualan=$value->kode_penjualan" }}" class="btn btn-info ml-2">
                                        <span class="fa fa-print" aria-hidden="true"></span>
                                    </a>
                                </div>
                            </td>
                        </tr>
                        @php
                            $no++
                        @endphp
                    @endforeach
                </tbody>
            </table>
            {{$penjualanCatering->appends(Request::all())->links('vendor.pagination.custom')}}
        </div>
@endsection
