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
  @php
  $tanggalDari = Request::get('tanggalDari');
  $tanggalSampai = Request::get('tanggalSampai');
  @endphp
    <br>
    <hr>
    @foreach ($kodeRekening as $item)
    @php
        $totalDebet = 0;
        $totalKredit = 0;
        $saldoAkhir = 0;
        $saldoAwalDebet2 = 0;
        $saldoAwalKredit2 = 0;
        $saldoAwalDebet = 0;
        $saldoAwalKredit = 0;
    @endphp
      <center>
        <h5><b>Buku Besar : {{$item->nama . ' - ' . $item->kode_rekening}} </b></h5>
        <h6><b>Periode : {{date('d-m-Y', strtotime($tanggalDari))}} s/d {{date('d-m-Y', strtotime($tanggalSampai))}}</b></h6>
      </center>
      
        <table class="table table-custom">
          <thead style="background: #e2e3f7; font-weight: 500; letter-spacing: 0.5px; color: #3c4099;">
            <tr>
              <th>Tanggal</th>
              <th>Kode Transaksi</th>
              <th>Keterangan</th>
              <th>Lawan</th>
              <th>Debet</th>
              <th>Kredit</th>
              <th>Saldo</th>
            </tr>
          </thead>
          <tbody>
            @php
                // count jumlah transaksi masing2 kode rekening sebelum tanggal dari
                $cekTransaksi = \App\Models\Jurnal::where('tanggal', '<', $tanggalDari)->where('kode', $item->kode_rekening)->orWhere('lawan', $item->kode_rekening)->count();
                
                // cek apakah ada transaksi sebelum tanggal dari
                // untuk ngambil saldo awal sebelum tanggal dari
                if ($cekTransaksi > 0) {

                  // cek apakah rekening terdapat di field kode di table jurnal
                  $isFieldKode = \App\Models\Jurnal::where('kode', $item->kode_rekening)->where('tanggal', '<', $tanggalDari)->count();

                  if ($isFieldKode > 0) {
                    $saldoAwalDebet = \App\Models\Jurnal::select(\DB::raw('SUM(nominal) AS nominal'))->where('kode', $item->kode_rekening)->where('tipe', 'Debet')->where('tanggal', '<',$tanggalDari)->get()[0]->nominal;

                    $saldoAwalKredit = \App\Models\Jurnal::select(\DB::raw('SUM(nominal) AS nominal'))->where('kode', $item->kode_rekening)->where('tipe', 'Kredit')->where('tanggal', '<',$tanggalDari)->get()[0]->nominal;

                    // cek apakah rekening juga terdapat di field lawan di table jurnal
                    $isFieldLawan = \App\Models\Jurnal::where('lawan', $item->kode_rekening)->where('tanggal', '<', $tanggalDari)->count();
                    if ($isFieldLawan > 0) {
                      $saldoAwalDebet2 = \App\Models\Jurnal::select(\DB::raw('SUM(nominal) AS nominal'))->where('lawan', $item->kode_rekening)->where('tipe', 'Kredit')->where('tanggal', '<',$tanggalDari)->get()[0]->nominal;

                      $saldoAwalKredit2 = \App\Models\Jurnal::select(\DB::raw('SUM(nominal) AS nominal'))->where('lawan', $item->kode_rekening)->where('tipe', 'Debet')->where('tanggal', '<',$tanggalDari)->get()[0]->nominal;

                    }
                  }
                  else{ //rekening tsb tidak terdapat di field kode dan hanya terdapat di field lawan
                    $saldoAwalDebet = \App\Models\Jurnal::select(\DB::raw('SUM(nominal) AS nominal'))->where('lawan', $item->kode_rekening)->where('tipe', 'Kredit')->where('tanggal', '<',$tanggalDari)->get()[0]->nominal;

                    $saldoAwalKredit = \App\Models\Jurnal::select(\DB::raw('SUM(nominal) AS nominal'))->where('lawan', $item->kode_rekening)->where('tipe', 'Debet')->where('tanggal', '<',$tanggalDari)->get()[0]->nominal;
                  }

                  // hitung saldoAwal dari rekening
                  if ($item->tipe == 'Debet') {
                    $saldoAkhir = $item->saldo_awal + ($saldoAwalDebet + $saldoAwalDebet2) - ($saldoAwalKredit + $saldoAwalKredit2);
                  }
                  else{
                    $saldoAkhir = $item->saldo_awal - ($saldoAwalDebet + $saldoAwalDebet2) + ($saldoAwalKredit + $saldoAwalKredit2);
                  }
                }
                // tidak ada transaksi untuk rekening tsb sebelum tanggal dari
                else{
                  // set saldo akhir = saldo awal rekening
                  $saldoAkhir = $item->saldo_awal;
                }
                // echo $saldoAwalDebet . "saldoawaldebet<br>";
                // echo $saldoAwalDebet2 . "saldoawaldebet2<br>";
                // echo $saldoAwalKredit . "saldoawalkredit<br>";
                // echo $saldoAwalKredit2 . "saldoawalkredit2<br>";
            @endphp
            {{-- print saldo awal --}}
            <tr>
              <td>{{date('d-m-Y', strtotime($tanggalDari))}}</td>
              <td>-</td>
              <td>Saldo Awal</td>
              <td colspan="3"></td>
              <td>{{number_format($saldoAkhir, 2, ',', '.')}}</td>
            </tr>
            @php
                $getBukuBesar = \App\Models\Jurnal::select('id', 'tanggal', 'kode_transaksi', 'keterangan', 'kode', 'lawan', 'nominal', 'tipe')->whereBetween('tanggal', [$tanggalDari, $tanggalSampai])->where('kode', $item->kode_rekening)->orWhere('lawan', $item->kode_rekening)->whereBetween('tanggal', [$tanggalDari, $tanggalSampai])->orderBy('tanggal', 'ASC')->get();
            @endphp
            @foreach ($getBukuBesar as $val)
                {{-- cek posisi lawan (pasangan) ada di field kode atau di field lawan --}}
                @if ($val->kode == $item->kode_rekening)
                  @php
                      $fieldLawan = 'lawan';
                  @endphp
                @else
                  @php
                      $fieldLawan = 'kode';
                  @endphp
                @endif
                <tr>
                  <td>{{date('d-m-Y', strtotime($val->tanggal))}}</td>
                  <td>{{$val->kode_transaksi}}</td>
                  <td>{{$val->tipe}}</td>
                  <td>{{$val->$fieldLawan . ' ~ ' . \App\Models\KodeRekening::select('nama')->where('kode_rekening', $val->$fieldLawan)->get()[0]->nama}}</td>
                  {{-- jika lawan terdapat di field lawan --}}
                  @if ($fieldLawan == 'lawan')
                      {{-- jika tipe transaksi = debet --}}
                      @if ($val->tipe == 'Debet')
                          {{-- totaldebet bertambah --}}
                          @php
                              $totalDebet += $val->nominal;
                          @endphp

                          {{-- jika tipe rekening = debet --}}
                          @if ($item->tipe == 'Debet')
                              {{-- saldo akhir rekening bertambah --}}
                              @php
                                  $saldoAkhir += $val->nominal;
                              @endphp

                          {{-- jika tipe rekening = kredit --}}
                          @else
                              {{-- saldo akhir rekening berkurang --}}
                              @php
                                  $saldoAkhir -= $val->nominal;
                              @endphp
                          @endif

                          <td>{{number_format($val->nominal, 2, ',', '.')}}</td>
                          <td>-</td>
                          <td>{{number_format($saldoAkhir, 2, ',', '.')}}</td>

                      {{-- jika tipe transaksi = kredit --}}
                      @else
                          {{-- total kredit bertambah --}}
                          @php
                              $totalKredit += $val->nominal;
                          @endphp

                          {{-- jika tipe rekening = debet --}}
                          @if ($item->tipe == 'Debet')
                              {{-- saldo akhir berkurang --}}
                              @php
                                  $saldoAkhir -= $val->nominal;
                              @endphp

                          {{-- jika tipe rekening = kredit --}}
                          @else
                              {{-- saldo akhir bertambah --}}
                              @php
                                  $saldoAkhir += $val->nominal;
                              @endphp
                          @endif

                          <td>-</td>
                          <td>{{number_format($val->nominal, 2, ',', '.')}}</td>
                          <td>{{number_format($saldoAkhir, 2, ',', '.')}}</td>
                      
                      @endif
                  
                  {{-- jika lawan terdapat di field kode --}}
                  @else
                      {{-- jike tipe transaksi  = debet --}}
                      @if ($val->tipe == 'Debet')
                          {{-- total kredit bertambah --}}
                          @php
                              $totalKredit += $val->nominal;
                          @endphp
                          
                          {{-- jika tipe rekening = Debet --}}
                          @if ($item->tipe == 'Debet')
                              {{-- saldo akhir berkurang --}}
                              @php
                                  $saldoAkhir -= $val->nominal;
                              @endphp

                          {{-- jika tipe rekening = kredit --}}
                          @else
                              @php
                                  $saldoAkhir += $val->nominal;
                              @endphp
                          @endif

                          <td>-</td>
                          <td>{{number_format($val->nominal, 2, ',', '.')}}</td>
                          <td>{{number_format($saldoAkhir, 2, ',', '.')}}</td>

                      {{-- jika tipe transaksi = kredit --}}
                      @else
                          {{-- total debet bertambah --}}
                          @php
                              $totalDebet += $val->nominal;
                          @endphp

                          {{-- jika tipe rekening = Debet --}}
                          @if ($item->tipe == 'Debet')
                              {{-- saldo akhir bertambah --}}
                              @php
                                  $saldoAkhir += $val->nominal;
                              @endphp

                          {{-- jika tipe rekening = kredit --}}
                          @else
                              @php
                                  $saldoAkhir -= $val->nominal;
                              @endphp
                          @endif

                          <td>{{number_format($val->nominal, 2, ',', '.')}}</td>
                          <td>-</td>
                          <td>{{number_format($saldoAkhir, 2, ',', '.')}}</td>
                      @endif
                  @endif
                </tr>
            @endforeach
            <thead style="background: #e2e3f7; font-weight: 500; letter-spacing: 0.5px; color: #3c4099;">
              <tr>
                <th colspan="4" style="text-align: center">Total</th>
                <th>{{number_format($totalDebet, 2, ',', '.')}}</th>
                <th>{{number_format($totalKredit, 2, ',', '.')}}</th>
                <th></th>
              </tr>
            </thead>
          </tbody>
        </table>
      <hr>
    @endforeach
</body>
</html>
@if (isset($_GET['xls']))
    @php
        $name = 'Buku Besar ' . date('d-m-Y', strtotime($_GET['tanggalDari'])).' s/d '.date('d-m-Y', strtotime($_GET['tanggalSampai'])).'.xls';
        header("Content-Type: application/xls");
        header("Content-Disposition: attachment; filename=$name");
    @endphp
@else
    <script>
        window.print()
    </script>
@endif