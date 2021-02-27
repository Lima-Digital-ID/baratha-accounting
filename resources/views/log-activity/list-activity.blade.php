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
                            <td>{{$value->created_at}}</td>
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
