@extends('common.template')
@section('container')
<div class="card shadow py-2">
    <div class="card-body">
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
            <form class="d-none d-sm-inline-block form-inline mr-auto ml-md-3 my-2 my-md-0 mw-100 navbar-search" action="{{ route('kategori-barang.index') }}" method="get">
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
            <table class="table table-striped table-hover table-bordered table-custom">
                <thead>
                    <tr>
                        <td>#</td>
                        <td>Nama Kategori</td>
                        <td>Opsi</td>
                    </tr>
                </thead>
                <tbody>
                    @php
                        $page = Request::get('page');
                        $no = !$page || $page == 1 ? 1 : ($page - 1) * 10 + 1;
                    @endphp
                    @foreach ($kategoriBarang as $value)
                        <tr>
                            <td>{{$no}}</td>
                            <td>{{$value->nama}}</td>
                            <td class="text-center">
                                <div class="dropdown dropdown-link">
                                    <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown">
                                        Opsi
                                    </button>
                                    <div class="dropdown-menu">
                                        <a href="{{ route('kategori-barang.edit', $value) }}" class="dropdown-item">{{ __('Edit') }}</a>
                                        {{-- <form action="{{ route('kategori-barang.destroy', $value) }}" method="post">
                                            @csrf
                                            @method('delete')
                                            <button type="button" class="mr-1 dropdown-item" onclick="confirm('{{ __("Apakah anda yakin ingin menghapus?") }}') ? this.parentElement.submit() : ''">
                                                {{ __('Hapus') }}
                                            </button>
                                        </form>   --}}
                                    </div>
                                </div>

                            </td>
                        </tr>
                        @php
                            $no++
                        @endphp
                    @endforeach
                </tbody>
            </table>
            {{$kategoriBarang->appends(Request::all())->links('vendor.pagination.custom')}}
        </div>
    </div>
</div>
@endsection
