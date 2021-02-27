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
            <form class="d-none d-sm-inline-block form-inline mr-auto ml-md-3 my-2 my-md-0 mw-100 navbar-search" action="{{ route('supplier.index') }}" method="get">
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
                        <td>Kode Supplier</td>
                        <td>Nama</td>
                        <td>Alamat</td>
                        <td>No. Hp</td>
                        <td>Hutang</td>
                        <td>Aksi</td>
                    </tr>
                </thead>
                <tbody>
                    @php
                        $page = Request::get('page');
                        $no = !$page || $page == 1 ? 1 : ($page - 1) * 10 + 1;
                    @endphp
                    @foreach ($supplier as $value)
                        <tr>
                            <td>{{$no}}</td>
                            <td>{{$value->kode_supplier}}</td>
                            <td>{{$value->nama}}</td>
                            <td>{{$value->alamat}}</td>
                            <td>{{$value->no_hp}}</td>
                            <td class="text-right">Rp. {{number_format($value->hutang, 2, ',','.')}}</td>
                            <td>
                                {{-- <div class="dropdown dropdown-link">
                                    <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown">
                                        Opsi
                                    </button>
                                    <div class="dropdown-menu">
                                        <a href="{{ route('supplier.edit', $value->kode_supplier) }}" class="dropdown-item">{{ __('Edit') }}</a>
                                        <form action="{{ route('supplier.destroy', $value->kode_supplier) }}" method="post">
                                            @csrf
                                            @method('delete')
                                            <button type="button" class="ml-1 dropdown-item" onclick="confirm('{{ __("Apakah anda yakin ingin menghapus?") }}') ? this.parentElement.submit() : ''">
                                                {{ __('Hapus') }}
                                            </button>
                                        </form>  
                                    </div>
                                </div> --}}
                                <div class="form-inline">
                                    <a href="{{ route('hutang-supplier', $value->kode_supplier) }}" class="btn btn-warning mr-2" data-toggle="tooltip" title="Detail Hutang"> <span class="fa fa-money-bill-wave"></span> </a>
                                    <a href="{{ route('supplier.edit', $value) }}" class="btn btn-success mr-2"> <span class="fa fa-pen"></span> </a>
                                    <form action="{{ route('supplier.destroy', $value) }}" method="post">
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
            {{$supplier->appends(Request::all())->links('vendor.pagination.custom')}}
        </div>
@endsection
