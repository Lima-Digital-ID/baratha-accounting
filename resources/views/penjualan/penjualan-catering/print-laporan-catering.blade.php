<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link href="{{ asset('css/custom.css') }}" rel="stylesheet" />
    <link href="{{ asset('css/sb-admin-2.min.css') }}" rel="stylesheet" />
    <link href="{{ asset('vendor/datatables/dataTables.bootstrap4.min.css') }}" rel="stylesheet" />
    <title>Document</title>
    <style type="text/css" media="print">
        @page {
            size: auto;
            margin: 0;
        }
        table{
            margin-left: 12px;
            margin-right: 12px;
        }
    </style>        
</head>
<body>
    <br>
    <center>
        <h4 class="heading-small text-muted mb-3">Laporan Pembelian Barang</h4>
    </center>
    <center>
        <h3 class="heading-small text-dark mb-3">Periode {{ \Request::get('start') }} s/d {{ \Request::get('end') }}</h3>
    </center>
    <br>
    @php
    $total_qty = 0;
    $total_ppn = 0;
    $grandtotal = 0;
    $total_harga_satuan = 0;
    $total_dpp = 0;
    $total_ppn = 0;
    $total_dpp_ppn = 0;
    @endphp
    <div class="table-responsive">
        @if (Request::get('nilai') == 'Rekap')
        <table class="table table-custom">
            <thead>
                <tr>
                    <td>Tanggal</td>
                    <td>Customer</td>
                    <td>Kode Transaksi</td>
                    <td>Jumlah</td>
                    <td>PPN</td>
                    <td>Grandtotal (Rp)</td>
                </tr>
            </thead>
            <tbody>
                @foreach ($report as $item)
                @php
                    $total_qty += $item->qty;
                    $total_ppn += $item->total_ppn;
                    $grandtotal += $item->grandtotal;
                @endphp
                <tr>
                   <td>{{ $item->tanggal }}</td>
                   <td>{{ $item->kode_customer }} - {{ $item->nama }}</td>
                   <td>{{ $item->kode_penjualan }}</td>
                   <td>{{ number_format($item->qty, 2, ',', '.') }}</td>
                   <td>{{ number_format($item->total_ppn, 2, ',', '.') }}</td>
                   <td>{{ number_format($item->grandtotal, 2, ',', '.') }}</td>
               </tr>
                @endforeach
            </tbody>
            <thead>
                <tr>
                    <td colspan="3" class="text-center">Total</td>
                    <td>{{ number_format($total_qty, 2, ',', '.') }}</td>
                    <td>{{ number_format($total_ppn, 2, ',', '.') }}</td>
                    <td>{{ number_format($grandtotal, 2, ',', '.') }}</td>
                </tr>
            </thead>
       </table>
        @else
        <table class="table table-custom">
            <thead>
                <tr>
                    <td>Tanggal</td>
                    <td>Customer</td>
                    <td>Kode Transaksi</td>
                    <td>Keterangan</td>
                    <td>Qty</td>
                    <td>Harga Sat.</td>
                    <td>DPP</td>
                    <td>PPN</td>
                    <td>DPP + PPN</td>
                </tr>
            </thead>
            <tbody>
                @foreach ($report as $item)
                @php
                    $total_qty += $item->qty;
                    $total_harga_satuan += $item->harga_satuan;
                    $total_dpp += $item->total;
                    $total_ppn += $item->total_ppn;
                    $total_dpp_ppn += ($item->total + $item->total_ppn);
                @endphp
                <tr>
                   <td>{{ $item->tanggal }}</td>
                   <td>{{ $item->kode_customer }} - {{ $item->nama }}</td>
                   <td>{{ $item->kode_penjualan }}</td>
                   <td>{{ $item->keterangan }}</td>
                   <td>{{ $item->qty }}</td>
                   <td>{{ number_format($item->harga_satuan, 2, ',', '.') }}</td>
                   <td>{{ number_format($item->total, 2, ',', '.') }}</td>
                   <td>{{ number_format($item->total_ppn, 2, ',', '.') }}</td>
                   <td>{{ number_format(($item->total + $item->total_ppn), 2, ',', '.') }}</td>
                </tr>
                @endforeach
            </tbody>
            <thead>
                <tr>
                    <td colspan="4" class="text-center">Total</td>
                    <td>{{ number_format($total_qty, 2, ',', '.') }}</td>
                    <td>{{ number_format($total_harga_satuan, 2, ',', '.') }}</td>
                    <td>{{ number_format($total_dpp, 2, ',', '.') }}</td>
                    <td>{{ number_format($total_ppn, 2, ',', '.') }}</td>
                    <td>{{ number_format($total_dpp_ppn, 2, ',', '.') }}</td>
                </tr>
            </thead>
       </table>
        @endif
    </div>
</body>
</html>
@if (isset($_GET['xls']))
    @php
        $name = 'Laporan Pembelian Barang ' . date('d-m-Y', strtotime($_GET['start'])).' s/d '.date('d-m-Y', strtotime($_GET['end'])).'.xls';
        header("Content-Type: application/xls");
        header("Content-Disposition: attachment; filename=$name");
    @endphp
@else
    <script>
        window.print()
    </script>
@endif