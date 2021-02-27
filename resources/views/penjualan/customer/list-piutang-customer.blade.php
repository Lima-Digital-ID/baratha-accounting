@extends('common.template')
    @section('container')

<div class="table-responsive">
    <table class="table table table-custom">
        <thead>
            <tr>
                <td>#</td>
                <td>Kode Transaksi</td>
                <td>Tipe Piutang</td>
                <td>Tanggal Transaksi</td>
                <td>Jumlah Piutang</td>
                <td>Terbayar</td>
                <td>Sisa</td>
            </tr>
        </thead>
        <tbody>
            @php 
                $no = 0;
                $total = 0;
                $terbayar = 0;
            @endphp
            @foreach($piutang as $data)
                @php 
                    $no++;
                    $total .= $data->grandtotal;
                    $terbayar .= $data->terbayar;
                @endphp
                <tr>
                    <td>{{$no}}</td>
                    <td>{{$data->kode_penjualan}}</td>
                    <td>{{ucwords($data->tipe_penjualan)}}</td>
                    <td>{{date('m-d-Y', strtotime($data->tanggal))}}</td>
                    <td>{{number_format($data->grandtotal,0,',','.')}}</td>
                    <td>{{number_format($data->terbayar,0,',','.')}}</td>
                    <td>{{number_format($data->grandtotal-$data->terbayar,0,',','.')}}</td>
                </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr>
                <td colspan="4" class="text-center">Total</td>
                <td>{{number_format($total,0,',','.')}}</td>
                <td>{{number_format($terbayar,0,',','.')}}</td>
                <td>{{number_format($total-$terbayar,0,',','.')}}</td>
            </tr>
        </tfoot>
    </table>
</div>
@endsection
