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
        <h4 class="heading-small text-muted mb-3">Laporan Kas</h4>
    </center>
    <center>
        <h3 class="heading-small text-dark mb-3">Periode {{ \Request::get('start') }} s/d {{ \Request::get('end') }}</h3>
    </center>
    <center>
        <h4 class="heading-small text-muted mb-3">
            @foreach ($kodeRekeningKas as $item)
            @if ($_GET['kode_perkiraan'] == $item->kode_rekening)
            {{ $_GET['kode_perkiraan'].' '. $item->nama }}
            @endif
            @endforeach
        </h4>
    </center>
    <br>
    <div class="table-responsive">
        <table class="table table-custom">
            <thead style="background: #e2e3f7; font-weight: 500; letter-spacing: 0.5px; color: #3c4099;">
                <tr>
                    <td>Tanggal</td>
                    <td>Kode Transaksi</td>
                    <td>Keterangan</td>
                    <td>Pasangan</td>
                    <td>Penerimaan</td>
                    <td>Pengeluaran</td>
                </tr>
            </thead>
            <tbody>
                @foreach ($report as $item)
                <tr>
                    <td>{{ $item->tanggal }}</td>
                    <td>{{ $item->kode_kas }}</td>
                    <td>{{ $item->keterangan }}</td>
                    <td>{{ $item->lawan }}</td>
                    <td>
                        @if ($item->tipe == 'Masuk')
                            {{ $item->subtotal }}
                        @else
                            -
                        @endif
                    </td>
                    <td>
                        @if ($item->tipe == 'Keluar')
                            {{ $item->subtotal }}
                        @else
                            -
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
       </table>
    </div>
</body>
</html>
@if (isset($_GET['xls']))
    @php
        $name = 'Laporan Kas ' . date('d-m-Y', strtotime($_GET['start'])).' s/d '.date('d-m-Y', strtotime($_GET['end'])).'.xls';
        header("Content-Type: application/xls");
        header("Content-Disposition: attachment; filename=$name");
    @endphp
@else
    <script>
        window.print()
    </script>
@endif