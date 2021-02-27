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
                        <td>Kode Pembelian</td>
                        <td>Supplier</td>
                        <td>Tanggal</td>
                        <td>Total Qty</td>
                        <td>Total Harga</td>
                        <td>Total PPN</td>
                        <td>Grandtotal</td>
                        <td>Terbayar</td>
                        <td>Detail</td>
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
                                <div class="form-inline">
                                    <a href="{{ url('pembelian/pembelian-jatuh-tempo/detail', $value) }}" class="btn btn-success mr-2"> <span class="fa fa-eye"></span> </a>
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
