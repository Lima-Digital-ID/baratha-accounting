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
        <h4 class="heading-small text-muted mb-3">Laporan Pemakaian Barang</h4>
    </center>
    <center>
        <h3 class="heading-small text-dark mb-3">Periode {{ \Request::get('start') }} s/d {{ \Request::get('end') }}</h3>
    </center>
    <br>
    <div class="table-responsive">
        <table class="table table-custom">
            <thead style="background: #e2e3f7; font-weight: 500; letter-spacing: 0.5px; color: #3c4099;">
                <tr>
                    <td>Tanggal</td>
                    <td>Kode Transaksi</td>
                    <td>Kode Barang</td>
                    <td>Keterangan</td>
                    <td>Qty</td>
                    <td>Sat</td>
                    <td>Total</td>
                    <td>Kode Biaya</td>
                </tr>
            </thead>
            <tbody>
                @foreach ($report as $item)
                <tr>
                    <td>{{ $item->tanggal }}</td>
                    <td>{{ $item->kode_pemakaian }}</td>
                    <td>{{ $item->kode_barang }}</td>
                    <td>{{ $item->keterangan != null ? $item->keterangan : '-' }}</td>
                    <td>{{ $item->qty }}</td>
                    <td>{{ $item->satuan }}</td>
                    <td>{{ number_format($item->subtotal, 2, ',', '.') }}</td>
                    <td>{{ $item->kode_biaya }}</td>
               </tr>
                @endforeach
            </tbody>
       </table>
    </div>
</body>
</html>
@if (isset($_GET['xls']))
    @php
        $name = 'Laporan Pemakaian Barang ' . date('d-m-Y', strtotime($_GET['start'])).' s/d '.date('d-m-Y', strtotime($_GET['end'])).'.xls';
        header("Content-Type: application/xls");
        header("Content-Disposition: attachment; filename=$name");
    @endphp
@else
    <script>
        window.print()
    </script>
@endif