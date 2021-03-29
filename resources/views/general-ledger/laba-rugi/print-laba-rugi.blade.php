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
    <h5><b>Laba Rugi</b></h5>
    <h6><b>Periode : {{$month}} - {{$year}}</b></h6>
  </center>
  <br>
  <table class="table table-bordered table-custom">
    @php
        $totalPenjualan = 0;
        $totalBeban = 0;
        $totalPajak = 0;
        $labaRugiKotor = 0;
        $labaRugiSebelumPajak = 0;
        $labaRugiBersih = 0;
    @endphp
    <thead style="background: #e2e3f7; font-weight: 500; letter-spacing: 0.5px; color: #3c4099;">
      <tr>
        <th colspan="2">Penjualan</th>
      </tr>
    </thead>
    
  </table>
</body>
</html>
@if (isset($_GET['xls']))
    @php
        $name = 'Laba Rugi ' . $month.' - ' . $year.'.xls';
        header("Content-Type: application/xls");
        header("Content-Disposition: attachment; filename=$name");
    @endphp
@else
    <script>
        window.print()
    </script>
@endif