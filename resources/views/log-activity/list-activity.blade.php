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
        <form class="mr-auto ml-md-3 my-2 my-md-0 mw-100 navbar-search" action="{{ route('log-activity.index') }}" method="get">
            <div class="row">
                <div class="col-md-2 ml-auto">
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
                <div class="col-md-2">
                    <input type="text" class="form-control end datepicker {{ $errors->has('end') ? ' is-invalid' : '' }}" value="{{ old('end', isset($_GET['end']) ? $_GET['end'] : '') }}" name="end" placeholder="Tanggal" autocomplete="off">    
                    @if ($errors->has('end'))
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $errors->first('end') }}</strong>
                        </span>
                    @endif
                </div>
                <div class="col-md-2">
                    <select name="id_user" id="" class="form-control select2">
                        <option value="">Semua User</option>
                        @foreach ($users as $item)
                            <option value="{{$item->id}}" {{isset($_GET['id_user']) && $_GET['id_user'] == $item->id ? 'selected' : ''}} >{{$item->name}}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-auto">
                    <button type="submit" class="btn btn-primary btn-sm">
                        <i class="fas fa-search"></i>
                    </button>
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
        {{-- </div> --}}
        <div class="table-responsive">
            <table class="table table-custom">
                <thead>
                    <tr>
                        <td>#</td>
                        <td>User</td>
                        <td>Jenis Transaksi</td>
                        <td>Tipe</td>
                        <td>Keterangan</td>
                        <td>Waktu</td>
                    </tr>
                </thead>
                <tbody>
                    @php
                        $page = Request::get('page');
                        $no = !$page || $page == 1 ? 1 : ($page - 1) * 10 + 1;
                    @endphp
                    @foreach ($logActivity as $value)
                        <tr>
                            <td>{{$no}}</td>
                            <td>{{$value->name}}</td>
                            <td>{{$value->jenis_transaksi}}</td>
                            <td>{{$value->tipe}}</td>
                            <td>{{$value->keterangan}}</td>
                            <td>{{date('d-m-Y H:i:s', strtotime($value->created_at))}}</td>
                        </tr>
                        @php
                            $no++
                        @endphp
                    @endforeach
                </tbody>
            </table>
            {{$logActivity->appends(Request::all())->links('vendor.pagination.custom')}}
        </div>
@endsection
