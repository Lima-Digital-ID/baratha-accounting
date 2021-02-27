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
        <a href="{{$btnRight['link']}}" class="btn btn-primary mb-3"> <span class="fa fa-arrow-alt-circle-left"></span> {{$btnRight['text']}}</a>
        <h2>Kode Pembelian {{ $pembelianBarang[0]['kode_pembelian'] }}</h2>
        <div class="table-responsive">
            <table class="table table-custom">
                <thead>
                    <tr>
                        <td>#</td>
                        <td>Kode Barang</td>
                        <td>Harga Satuan</td>
                        <td>Qty</td>
                        <td>Subtotal</td>
                        <td>Total PPN</td>
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
                            <td>{{$value->kode_barang}}</td>
                            <td>{{ number_format($value->harga_satuan, 2, ',', '.') }}</td>
                            <td>{{$value->qty}}</td>
                            <td class="text-right">Rp. {{number_format($value->subtotal, 2, ',','.')}}</td>
                            <td class="text-right">Rp. {{number_format($value->ppn, 2, ',','.')}}</td>
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
