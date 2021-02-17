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
            <form class="d-none d-sm-inline-block form-inline mr-auto ml-md-3 my-2 my-md-0 mw-100 navbar-search" action="{{ route('barang.index') }}" method="get">
                <select name="kategori" id="" class="form-control">
                    <option value="">Kategori Barang</option>
                    @foreach ($kategoriBarang as $item)
                        <option value="{{$item->id}}" {{Request::get('kategori') == $item->id ? 'selected' : ''}} >{{$item->nama}}</option>
                    @endforeach
                </select>

                <input type="text" class="form-control" placeholder="Cari Data..." aria-label="Search" name="keyword" value="{{Request::get('keyword')}}">
                
                <button class="btn btn-primary" type="submit">
                <i class="fas fa-search fa-sm"></i>
                </button>
            </form>
          </div>
        </div>
        <div class="table-responsive">
            <table class="table table-custom">
                <thead>
                    <tr>
                        <td>#</td>
                        <td>Kode Barang</td>
                        <td>Nama</td>
                        <td>Satuan</td>
                        <td>Stock Awal</td>
                        <td>Saldo Awal</td>
                        <td>Stock</td>
                        <td>Saldo</td>
                        <td>Kategori</td>
                        <td>Aksi</td>
                    </tr>
                </thead>
                <tbody>
                    @php
                        $page = Request::get('page');
                        $no = !$page || $page == 1 ? 1 : ($page - 1) * 10 + 1;
                    @endphp
                    @foreach ($barang as $value)
                        <tr>
                            <td>{{$no}}</td>
                            <td>{{$value->kode_barang}}</td>
                            <td>{{$value->nama}}</td>
                            <td>{{$value->satuan}}</td>
                            <td>{{number_format($value->stock_awal, 2, ',', '.')}}</td>
                            <td>Rp {{number_format($value->saldo_awal, 2, ',', '.')}}</td>
                            <td>{{number_format($value->stock, 2, ',', '.')}}</td>
                            <td>Rp {{number_format($value->saldo, 2, ',', '.')}}</td>
                            <td>{{$value->kategoriBarang->nama}}</td>
                            <td>
                                {{-- <div class="dropdown dropdown-link">
                                    <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown">
                                        Opsi
                                    </button>
                                    <div class="dropdown-menu">
                                        <a href="{{ route('barang.edit', $value) }}" class="dropdown-item">{{ __('Edit') }}</a>
                                        <form action="{{ route('barang.destroy', $value) }}" method="post">
                                            @csrf
                                            @method('delete')
                                            <button type="button" class="mr-1 dropdown-item" onclick="confirm('{{ __("Apakah anda yakin ingin menghapus?") }}') ? this.parentElement.submit() : ''">
                                                {{ __('Hapus') }}
                                            </button>
                                        </form>  
                                    </div>
                                </div> --}}
                                <div class="form-inline">
                                    <a href="{{ route('barang.edit', $value) }}" class="btn btn-success mr-2"> <span class="fa fa-pen"></span> </a>
                                    <form action="{{ route('barang.destroy', $value) }}" method="post">
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
            {{$barang->appends(Request::all())->links('vendor.pagination.custom')}}
        </div>
@endsection
