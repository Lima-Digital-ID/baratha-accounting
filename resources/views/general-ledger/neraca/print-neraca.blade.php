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
    <h5><b>Neraca</b></h5>
    <h6><b>Periode : {{date('d-m-Y', strtotime(Request::get('tanggalDari')))}} s/d {{date('d-m-Y', strtotime(Request::get('tanggalSampai')))}}</b></h6>
  </center>
  <br>
  <table class="table table-bordered table-custom">
    <thead style="background: #e2e3f7; font-weight: 500; letter-spacing: 0.5px; color: #3c4099;">
        <tr>
            <th rowspan="2" style="vertical-align:middle">Kode</th>
            <th rowspan="2" style="vertical-align:middle">Nama Rekening</th>
            <th colspan="2" style="text-align: center;">Saldo Awal</th>
            <th colspan="2" style="text-align: center;">Mutasi</th>
            <th colspan="2" style="text-align: center;">Saldo Akhir</th>
            <tr>
                <th style="text-align: center;">Debet</th>
                <th style="text-align: center;">Kredit</th>
                <th style="text-align: center;">Debet</th>
                <th style="text-align: center;">Kredit</th>
                <th style="text-align: center;">Debet</th>
                <th style="text-align: center;">Kredit</th>
            </tr>
        </tr>
    </thead>
    <tbody>
        @php
            $tanggalDari = Request::get('tanggalDari');
            $tanggalSampai = Request::get('tanggalSampai');

            $totalSaldoAwalDebet = 0;
            $totalSaldoAwalKredit = 0;
            $totalMutasiDebet = 0;
            $totalMutasiKredit = 0;
            $totalSaldoAkhirDebet = 0;
            $totalSaldoAkhirKredit = 0;
        @endphp
        <?php
            foreach ($kodeRekening as $item) {

                $mutasiAwalDebet = 0;
                $mutasiAwalKredit = 0;
                
                $mutasiDebet = 0;
                $mutasiKredit = 0;

                // cek apakah ada jurnal awal di field kode
                $cekTransaksiAwalDiKode = \App\Models\Jurnal::where('tanggal', '<', $tanggalDari)->where('kode', $item->kode_rekening)->count();

                if ($cekTransaksiAwalDiKode > 0) {
                    $sumMutasiAwalDebetDiKode = \DB::table('jurnal')->where('kode', $item->kode_rekening)->where('tanggal', '<', $tanggalDari)->where('tipe', 'Debet')->sum('jurnal.nominal');
                    
                    $sumMutasiAwalKreditDiKode = \DB::table('jurnal')->where('kode', $item->kode_rekening)->where('tanggal', '<', $tanggalDari)->where('tipe', 'Kredit')->sum('jurnal.nominal');

                    if ($item->tipe == 'Debet') {
                        $mutasiAwalDebet += $sumMutasiAwalDebetDiKode + $item->saldo_awal;
                        $mutasiAwalKredit += $sumMutasiAwalKreditDiKode;
                    }
                    else{
                        $mutasiAwalDebet += $sumMutasiAwalDebetDiKode;
                        $mutasiAwalKredit += $sumMutasiAwalKreditDiKode + $item->saldo_awal;
                    }

                    // cek apakah transaksi sebelumnya juga terdapat di field lawan
                    $cekTransaksiAwalDiLawan = \App\Models\Jurnal::where('tanggal', '<', $tanggalDari)->where('lawan', $item->kode_rekening)->count();
                    if ($cekTransaksiAwalDiLawan > 0) {
                        $sumMutasiAwalDebetDiLawan = \DB::table('jurnal')->where('lawan', $item->kode_rekening)->where('tanggal', '<', $tanggalDari)->where('tipe', 'Kredit')->sum('jurnal.nominal');
                    
                        $sumMutasiAwalKreditDiLawan = \DB::table('jurnal')->where('lawan', $item->kode_rekening)->where('tanggal', '<', $tanggalDari)->where('tipe', 'Debet')->sum('jurnal.nominal');

                        $mutasiAwalDebet += $sumMutasiAwalDebetDiLawan;
                        $mutasiAwalKredit += $sumMutasiAwalKreditDiLawan;
                    }
                }
                else{ // cek apakah ada jurnal awal di field lawan
                    $cekTransaksiAwalDiLawan = \App\Models\Jurnal::where('tanggal', '<', $tanggalDari)->where('lawan', $item->kode_rekening)->count();
                    if ($cekTransaksiAwalDiLawan > 0) {
                        $sumMutasiAwalDebetDiLawan = \DB::table('jurnal')->where('lawan', $item->kode_rekening)->where('tanggal', '<', $tanggalDari)->where('tipe', 'Kredit')->sum('jurnal.nominal');
                    
                        $sumMutasiAwalKreditDiLawan = \DB::table('jurnal')->where('lawan', $item->kode_rekening)->where('tanggal', '<', $tanggalDari)->where('tipe', 'Debet')->sum('jurnal.nominal');

                        if ($item->tipe == 'Debet') {
                            $mutasiAwalDebet += $sumMutasiAwalDebetDiLawan + $item->saldo_awal;

                            $mutasiAwalKredit += $sumMutasiAwalKreditDiLawan;
                        }
                        else{
                            $mutasiAwalDebet += $sumMutasiAwalDebetDiLawan;
                            $mutasiAwalKredit += $sumMutasiAwalKreditDiLawan + $item->saldo_awal;
                        }
                    }
                    else{ //tidak ada jurnal awal di field kode maupun lawan
                        if ($item->tipe == 'Debet') {
                            $mutasiAwalDebet += $item->saldo_awal;
                        }
                        else{
                            $mutasiAwalKredit += $item->saldo_awal;
                        }
                    }
                }

                // cek transaksi di field kode
                $cekTransaksiDiKode = \App\Models\Jurnal::whereBetween('tanggal', [$tanggalDari, $tanggalSampai])->where('kode', $item->kode_rekening)->count();
                
                if ($cekTransaksiDiKode > 0) {
                    $sumMutasiDebetDiKode = \DB::table('jurnal')->where('kode', $item->kode_rekening)->whereBetween('tanggal', [$tanggalDari, $tanggalSampai])->where('tipe', 'Debet')->sum('jurnal.nominal');
                    
                    $sumMutasiKreditDiKode = \DB::table('jurnal')->where('kode', $item->kode_rekening)->whereBetween('tanggal', [$tanggalDari, $tanggalSampai])->where('tipe', 'Kredit')->sum('jurnal.nominal');

                    $mutasiDebet += $sumMutasiDebetDiKode;
                    $mutasiKredit += $sumMutasiKreditDiKode;

                    // cek transaksi di field lawan
                    $cekTransaksiDiLawan = \App\Models\Jurnal::whereBetween('tanggal', [$tanggalDari, $tanggalSampai])->where('lawan', $item->kode_rekening)->count();

                    if ($cekTransaksiDiLawan > 0) {
                        $sumMutasiDebetDiLawan = \DB::table('jurnal')->where('lawan', $item->kode_rekening)->whereBetween('tanggal', [$tanggalDari, $tanggalSampai])->where('tipe', 'Kredit')->sum('jurnal.nominal');
                        
                        $sumMutasiKreditDiLawan = \DB::table('jurnal')->where('lawan', $item->kode_rekening)->whereBetween('tanggal', [$tanggalDari, $tanggalSampai])->where('tipe', 'Debet')->sum('jurnal.nominal');

                        $mutasiDebet += $sumMutasiDebetDiLawan;
                        $mutasiKredit += $sumMutasiKreditDiLawan;
                    }
                }
                else{ // cek transaksi di field lawan
                    // cek transaksi di field lawan
                    $cekTransaksiDiLawan = \App\Models\Jurnal::whereBetween('tanggal', [$tanggalDari, $tanggalSampai])->where('lawan', $item->kode_rekening)->count();
                    if ($cekTransaksiDiLawan > 0) {
                        $sumMutasiDebetDiLawan = \DB::table('jurnal')->where('lawan', $item->kode_rekening)->whereBetween('tanggal', [$tanggalDari, $tanggalSampai])->where('tipe', 'Kredit')->sum('jurnal.nominal');
                        
                        $sumMutasiKreditDiLawan = \DB::table('jurnal')->where('lawan', $item->kode_rekening)->whereBetween('tanggal', [$tanggalDari, $tanggalSampai])->where('tipe', 'Debet')->sum('jurnal.nominal');

                        $mutasiDebet += $sumMutasiDebetDiLawan;
                        $mutasiKredit += $sumMutasiKreditDiLawan;
                    }
                }

                $saldoAwal = $mutasiAwalDebet - $mutasiAwalKredit;

                $saldoAkhir = ($mutasiAwalDebet + $mutasiDebet) - ($mutasiAwalKredit + $mutasiKredit);
                
                $totalMutasiDebet += $mutasiDebet;
                $totalMutasiKredit += $mutasiKredit;

                if ($item->tipe == 'Debet') {
                    $totalSaldoAwalDebet += $saldoAwal;
                    $totalSaldoAkhirDebet += $saldoAkhir;
                }
                else{
                    $totalSaldoAwalKredit += $saldoAwal;
                    $totalSaldoAkhirKredit += $saldoAkhir;
                }
        ?>
                <tr>
                    <td>{{$item->kode_rekening}}</td>
                    <td>{{$item->nama}}</td>
                    @if ($item->tipe == 'Debet')
                        <td>{{number_format($saldoAwal, 2, ',', '.')}}</td>
                        <td>-</td>
                    @else
                        <td>-</td>
                        <td>{{number_format($saldoAwal * -1, 2, ',', '.')}}</td>
                    @endif
                    <td>{{number_format($mutasiDebet, 2, ',', '.')}}</td>
                    <td>{{number_format($mutasiKredit, 2, ',', '.')}}</td>
                    @if ($item->tipe == 'Debet')
                        <td>{{number_format($saldoAkhir, 2, ',', '.')}}</td>
                        <td>-</td>
                    @else
                        <td>-</td>
                        <td>{{number_format($saldoAkhir * -1, 2, ',', '.')}}</td>
                    @endif
                </tr>
        <?php
        // endforeach
            }
        ?>
    </tbody>
    <thead style="background: #e2e3f7; font-weight: 500; letter-spacing: 0.5px; color: #3c4099;">
        <tr>
            <th colspan="2" style="text-align: center">Total</th>
            <th>{{number_format($totalSaldoAwalDebet, 2, ',', '.')}}</th>
            <th>{{number_format($totalSaldoAwalKredit * -1, 2, ',', '.')}}</th>
            <th>{{number_format($totalMutasiDebet, 2, ',', '.')}}</th>
            <th>{{number_format($totalMutasiKredit, 2, ',', '.')}}</th>
            <th>{{number_format($totalSaldoAkhirDebet, 2, ',', '.')}}</th>
            <th>{{number_format($totalSaldoAkhirKredit * -1, 2, ',', '.')}}</th>
        </tr>
    </thead>
  </table>
</body>
</html>
@if (isset($_GET['xls']))
    @php
        $name = 'Neraca ' . date('d-m-Y', strtotime($_GET['tanggalDari'])).' s/d '.date('d-m-Y', strtotime($_GET['tanggalSampai'])).'.xls';
        header("Content-Type: application/xls");
        header("Content-Disposition: attachment; filename=$name");
    @endphp
@else
    <script>
        window.print()
    </script>
@endif