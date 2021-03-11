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
            <form class="d-none d-sm-inline-block form-inline mr-auto ml-md-3 my-2 my-md-0 mw-100 navbar-search" action="{{ route('hpp.index') }}" method="get">
              <div class="input-group">
                <input type="text" autocomplete="off" class="form-control datepicker bg-light border-1 small" placeholder="Cari Data..." aria-label="Search" name="date" aria-describedby="basic-addon2" value="{{Request::get('date')}}">
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
                        <td>Nominal</td>
                        <td>Keterangan</td>
                        <td>Aksi</td>
                    </tr>
                </thead>
                <tbody>
                    @php
                        $page = Request::get('page');
                        $no = !$page || $page == 1 ? 1 : ($page - 1) * 10 + 1;
                    @endphp
                    @foreach ($hpp as $value)
                        <tr>
                            <td>{{$no}}</td>
                            <td>{{date('d-m-Y', strtotime($value->tanggal))}}</td>
                            <td>Rp. {{number_format($value->nominal_hpp, 2, ',','.')}}</td>
                            <td>{{$value->keterangan}}</td>
                            <td>
                                {{-- <div class="dropdown dropdown-link">
                                    <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown">
                                        Opsi
                                    </button>
                                    <div class="dropdown-menu">
                                        <a href="{{ route('hpp.edit', $value->kode_hpp) }}" class="dropdown-item">{{ __('Edit') }}</a>
                                        <form action="{{ route('Customer.destroy', $value->kode_hpp) }}" method="post">
                                            @csrf
                                            @method('delete')
                                            <button type="button" class="ml-1 dropdown-item" onclick="confirm('{{ __("Apakah anda yakin ingin menghapus?") }}') ? this.parentElement.submit() : ''">
                                                {{ __('Hapus') }}
                                            </button>
                                        </form>  
                                    </div>
                                </div> --}}
                                <div class="form-inline">
                                    <a data-toggle="tooltip" title="Edit Hpp" href="{{ route('hpp.edit', $value) }}" class="btn btn-success mr-2"> <span class="fa fa-pen"></span> </a>
                                    <form action="{{ route('hpp.destroy', $value) }}" method="post">
                                        @csrf
                                        @method('delete')
                                        <button type="button" data-toggle="tooltip" title="Hapus Hpp" class="btn btn-danger" onclick="confirm('{{ __("Apakah anda yakin ingin menghapus?") }}') ? this.parentElement.submit() : ''">
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
            {{$hpp->appends(Request::all())->links('vendor.pagination.custom')}}
        </div>
@endsection
