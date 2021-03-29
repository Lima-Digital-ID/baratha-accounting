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
        
        <div class="row">
            <div class="col-md-12">
                <form action="{{ url('general-ledger/laba-rugi') }}" method="get">
                    <div class="row">

                        <div class="col-md-6 mb-4">
                            <label for="">Bulan</label>
                            <select name="month" id="month" class="form-control select2" required>
                                <option value="">--Pilih Bulan--</option>
                                @foreach ($allBulan as $item)
                                    <option value="{{$item['bulan']}}" {{!is_null(Request::get('month')) && Request::get('month') == $item['bulan'] ? 'selected' : '' }} >{{$item['bulan'] . ' ~ '.$item['nama']}}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-6 mb-4">
                          <label for="">Tahun</label>
                          <select name="year" id="year" class="form-control select2" required>
                              <option value="">--Pilih Tahun--</option>
                              @for ($y = 2018; $y <= date('Y'); $y++)
                                  <option value="{{$y}}" style="color: black" {{!is_null(Request::get('year')) && Request::get('year') == $y ? 'selected' : '' }}>{{$y}}</option>
                              @endfor
                          </select>
                      </div>

                        <div class="col-md-6">
                            <button type="submit" class="btn btn-primary"> <i class="fas fa-filter"></i> Filter</button>
                        </div>

                    </div>
                </form>
            </div>
        </div>
        @if ( !is_null(Request::get('month')) && !is_null(Request::get('year')))
          <hr>
          <div class="row d-flex justify-content-between">
              <div class="col">
                  <h6 class="heading-small text-muted mb-3">Laba Rugi</h6>
                  <h6 class="heading-small text-dark mb-3">Bulan : {{$month}} - {{$year}}</h6>
              </div>
              <div class="form-group mr-3">
                  <a href="{{ url('general-ledger/laba-rugi/print')."?month=$_GET[month]&year=$_GET[year]"}}" class="btn btn-primary btn-sm" target="_blank">
                      <i class="fa fa-print" aria-hidden="true"></i> Cetak
                  </a>
                  <a href="{{ url('general-ledger/laba-rugi/print')."?month=$_GET[month]&year=$_GET[year]&xls=true" }}" class="btn btn-success btn-sm" target="_blank">
                      <i class="fa fa-download"></i> Download xls
                  </a>
              </div>
          </div>

          <div class="table-responsive">
            <table class="table table-bordered table-custom">
              @php
                  $totalPenjualan = 0;
                  $totalBeban = 0;
                  $totalPajak = 0;
                  $labaRugiKotor = 0;
                  $labaRugiSebelumPajak = 0;
                  $labaRugiBersih = 0;
              @endphp
              <thead>
                <tr>
                  <th colspan="2">Penjualan</th>
                </tr>
              </thead>
              {{-- penjualan --}}
              <tbody>
                @foreach ($rekeningPenjualan as $item)
                  @php
                    $mutasiDebet = 0;
                    $mutasiKredit = 0;
                    // cek transaksi di field kode
                    $cekTransaksiDiKode = \DB::table('view_laba_rugi')->where('bulan', $month)->where('tahun',$year)->where('kode', $item->kode_rekening)->count();

                    if ($cekTransaksiDiKode > 0) {
                        $sumMutasiDebetDiKode = \DB::table('view_laba_rugi')->where('kode', $item->kode_rekening)->where('bulan', $month)->where('tahun',$year)->where('tipe', 'Debet')->sum('view_laba_rugi.nominal');
                        
                        $sumMutasiKreditDiKode = \DB::table('view_laba_rugi')->where('kode', $item->kode_rekening)->where('bulan', $month)->where('tahun',$year)->where('tipe', 'Kredit')->sum('view_laba_rugi.nominal');

                        $mutasiDebet += $sumMutasiDebetDiKode;
                        $mutasiKredit += $sumMutasiKreditDiKode;

                        // cek transaksi di field lawan
                        $cekTransaksiDiLawan = \DB::table('view_laba_rugi')->where('bulan', $month)->where('tahun',$year)->where('lawan', $item->kode_rekening)->count();

                        if ($cekTransaksiDiLawan > 0) {
                            $sumMutasiDebetDiLawan = \DB::table('view_laba_rugi')->where('lawan', $item->kode_rekening)->where('bulan', $month)->where('tahun',$year)->where('tipe', 'Kredit')->sum('view_laba_rugi.nominal');
                            
                            $sumMutasiKreditDiLawan = \DB::table('view_laba_rugi')->where('lawan', $item->kode_rekening)->where('bulan', $month)->where('tahun',$year)->where('tipe', 'Debet')->sum('view_laba_rugi.nominal');

                            $mutasiDebet += $sumMutasiDebetDiLawan;
                            $mutasiKredit += $sumMutasiKreditDiLawan;
                        }
                    }
                    else{ // cek transaksi di field lawan
                        // cek transaksi di field lawan
                        $cekTransaksiDiLawan = \DB::table('view_laba_rugi')->where('bulan', $month)->where('tahun',$year)->where('lawan', $item->kode_rekening)->count();
                        if ($cekTransaksiDiLawan > 0) {
                            $sumMutasiDebetDiLawan = \DB::table('view_laba_rugi')->where('lawan', $item->kode_rekening)->where('bulan', $month)->where('tahun',$year)->where('tipe', 'Kredit')->sum('view_laba_rugi.nominal');
                            
                            $sumMutasiKreditDiLawan = \DB::table('view_laba_rugi')->where('lawan', $item->kode_rekening)->where('bulan', $month)->where('tahun',$year)->where('tipe', 'Debet')->sum('view_laba_rugi.nominal');

                            $mutasiDebet += $sumMutasiDebetDiLawan;
                            $mutasiKredit += $sumMutasiKreditDiLawan;
                        }
                    }
                    $penjualan = $mutasiKredit - $mutasiDebet;
                    $totalPenjualan += $penjualan;
                  @endphp
                    <tr>
                      <td>
                        {{$item->nama}}
                      </td>
                      <td>
                        {{number_format($penjualan, 2, ',', '.')}}
                      </td>
                    </tr>
                @endforeach
              </tbody>
              <thead>
                <tr>
                  <th>Total Penjualan</th>
                  <th>{{number_format($totalPenjualan, 2, ',', '.')}}</th>
                </tr>
              </thead>
              {{-- Harga Pokok Penjualan --}}
              <thead>
                <tr>
                  <th>Harga Pokok Penjualan</th>
                  <th>({{number_format($hpp, 2, ',', '.')}})</th>
                </tr>
              </thead>
              {{-- laba rugi kotor  --}}
              @php
                  $labaRugiKotor = $totalPenjualan - $hpp;
              @endphp
              <thead>
                <tr>
                  <th>Laba Rugi Kotor</th>
                  <th>{{number_format($labaRugiKotor, 2, ',', '.')}}</th>
                </tr>
              </thead>
              {{-- beban --}}
              <thead>
                <tr>
                  <th colspan="2">Beban</th>
                </tr>
              </thead>
              {{-- all beban --}}
              <tbody>
                @foreach ($rekeningBeban as $item)
                  @php
                    $mutasiDebet = 0;
                    $mutasiKredit = 0;
                    // cek transaksi di field kode
                    $cekTransaksiDiKode = \DB::table('view_laba_rugi')->where('bulan', $month)->where('tahun',$year)->where('kode', $item->kode_rekening)->count();

                    if ($cekTransaksiDiKode > 0) {
                        $sumMutasiDebetDiKode = \DB::table('view_laba_rugi')->where('kode', $item->kode_rekening)->where('bulan', $month)->where('tahun',$year)->where('tipe', 'Debet')->sum('view_laba_rugi.nominal');
                        
                        $sumMutasiKreditDiKode = \DB::table('view_laba_rugi')->where('kode', $item->kode_rekening)->where('bulan', $month)->where('tahun',$year)->where('tipe', 'Kredit')->sum('view_laba_rugi.nominal');

                        $mutasiDebet += $sumMutasiDebetDiKode;
                        $mutasiKredit += $sumMutasiKreditDiKode;

                        // cek transaksi di field lawan
                        $cekTransaksiDiLawan = \DB::table('view_laba_rugi')->where('bulan', $month)->where('tahun',$year)->where('lawan', $item->kode_rekening)->count();

                        if ($cekTransaksiDiLawan > 0) {
                            $sumMutasiDebetDiLawan = \DB::table('view_laba_rugi')->where('lawan', $item->kode_rekening)->where('bulan', $month)->where('tahun',$year)->where('tipe', 'Kredit')->sum('view_laba_rugi.nominal');
                            
                            $sumMutasiKreditDiLawan = \DB::table('view_laba_rugi')->where('lawan', $item->kode_rekening)->where('bulan', $month)->where('tahun',$year)->where('tipe', 'Debet')->sum('view_laba_rugi.nominal');

                            $mutasiDebet += $sumMutasiDebetDiLawan;
                            $mutasiKredit += $sumMutasiKreditDiLawan;
                        }
                    }
                    else{ // cek transaksi di field lawan
                        // cek transaksi di field lawan
                        $cekTransaksiDiLawan = \DB::table('view_laba_rugi')->where('bulan', $month)->where('tahun',$year)->where('lawan', $item->kode_rekening)->count();
                        if ($cekTransaksiDiLawan > 0) {
                            $sumMutasiDebetDiLawan = \DB::table('view_laba_rugi')->where('lawan', $item->kode_rekening)->where('bulan', $month)->where('tahun',$year)->where('tipe', 'Kredit')->sum('view_laba_rugi.nominal');
                            
                            $sumMutasiKreditDiLawan = \DB::table('view_laba_rugi')->where('lawan', $item->kode_rekening)->where('bulan', $month)->where('tahun',$year)->where('tipe', 'Debet')->sum('view_laba_rugi.nominal');

                            $mutasiDebet += $sumMutasiDebetDiLawan;
                            $mutasiKredit += $sumMutasiKreditDiLawan;
                        }
                    }
                    if ($item->tipe == 'Debet') {
                      $beban = $mutasiDebet - $mutasiKredit;
                      $totalBeban += $beban;
                    }
                    else{
                      $beban = $mutasiKredit - $mutasiDebet;
                      $totalBeban -= $beban;
                    }
                  @endphp
                    <tr>
                      <td>
                        {{$item->nama}}
                      </td>
                      <td>
                        {{$item->tipe == 'Debet' ? number_format($beban, 2, ',', '.') : '('.number_format($beban, 2, ',', '.').')'}}
                      </td>
                    </tr>
                @endforeach
              </tbody>
              <thead>
                <tr>
                  <th>Total Beban</th>
                  <th>({{number_format($totalBeban, 2, ',', '.')}})</th>
                </tr>
              </thead>
              {{-- laba rugi sebelum pajak  --}}
              @php
                  $labaRugiSebelumPajak = $labaRugiKotor - $totalBeban;
              @endphp
              <thead>
                <tr>
                  <th>Laba Rugi Sebelum Pajak</th>
                  <th>{{number_format($labaRugiSebelumPajak, 2, ',', '.')}}</th>
                </tr>
              </thead>
              {{-- pajak --}}
              <thead>
                <tr>
                  <th colspan="2">Pajak</th>
                </tr>
              </thead>
              {{-- all pajak --}}
              <tbody>
                @foreach ($rekeningPajak as $item)
                  @php
                    $mutasiDebet = 0;
                    $mutasiKredit = 0;
                    // cek transaksi di field kode
                    $cekTransaksiDiKode = \DB::table('view_laba_rugi')->where('bulan', $month)->where('tahun',$year)->where('kode', $item->kode_rekening)->count();

                    if ($cekTransaksiDiKode > 0) {
                        $sumMutasiDebetDiKode = \DB::table('view_laba_rugi')->where('kode', $item->kode_rekening)->where('bulan', $month)->where('tahun',$year)->where('tipe', 'Debet')->sum('view_laba_rugi.nominal');
                        
                        $sumMutasiKreditDiKode = \DB::table('view_laba_rugi')->where('kode', $item->kode_rekening)->where('bulan', $month)->where('tahun',$year)->where('tipe', 'Kredit')->sum('view_laba_rugi.nominal');

                        $mutasiDebet += $sumMutasiDebetDiKode;
                        $mutasiKredit += $sumMutasiKreditDiKode;

                        // cek transaksi di field lawan
                        $cekTransaksiDiLawan = \DB::table('view_laba_rugi')->where('bulan', $month)->where('tahun',$year)->where('lawan', $item->kode_rekening)->count();

                        if ($cekTransaksiDiLawan > 0) {
                            $sumMutasiDebetDiLawan = \DB::table('view_laba_rugi')->where('lawan', $item->kode_rekening)->where('bulan', $month)->where('tahun',$year)->where('tipe', 'Kredit')->sum('view_laba_rugi.nominal');
                            
                            $sumMutasiKreditDiLawan = \DB::table('view_laba_rugi')->where('lawan', $item->kode_rekening)->where('bulan', $month)->where('tahun',$year)->where('tipe', 'Debet')->sum('view_laba_rugi.nominal');

                            $mutasiDebet += $sumMutasiDebetDiLawan;
                            $mutasiKredit += $sumMutasiKreditDiLawan;
                        }
                    }
                    else{ // cek transaksi di field lawan
                        // cek transaksi di field lawan
                        $cekTransaksiDiLawan = \DB::table('view_laba_rugi')->where('bulan', $month)->where('tahun',$year)->where('lawan', $item->kode_rekening)->count();
                        if ($cekTransaksiDiLawan > 0) {
                            $sumMutasiDebetDiLawan = \DB::table('view_laba_rugi')->where('lawan', $item->kode_rekening)->where('bulan', $month)->where('tahun',$year)->where('tipe', 'Kredit')->sum('view_laba_rugi.nominal');
                            
                            $sumMutasiKreditDiLawan = \DB::table('view_laba_rugi')->where('lawan', $item->kode_rekening)->where('bulan', $month)->where('tahun',$year)->where('tipe', 'Debet')->sum('view_laba_rugi.nominal');

                            $mutasiDebet += $sumMutasiDebetDiLawan;
                            $mutasiKredit += $sumMutasiKreditDiLawan;
                        }
                    }
                    if ($item->tipe == 'Debet') {
                      $pajak = $mutasiDebet - $mutasiKredit;
                      $totalPajak += $pajak;
                    }
                    else{
                      $pajak = $mutasiKredit - $mutasiDebet;
                      $totalPajak -= $pajak;
                    }
                  @endphp
                    <tr>
                      <td>
                        {{$item->nama}}
                      </td>
                      <td>
                        {{$item->tipe == 'Debet' ? number_format($pajak, 2, ',', '.') : '('.number_format($pajak, 2, ',', '.').')'}}
                      </td>
                    </tr>
                @endforeach
              </tbody>
              <thead>
                <tr>
                  <th>Total Pajak</th>
                  <th>({{number_format($totalPajak, 2, ',', '.')}})</th>
                </tr>
              </thead>
              {{-- laba rugi setelah pajak / bersih --}}
              @php
                  $labaRugiBersih = $labaRugiSebelumPajak - $totalPajak;

                  // cek is data available
                  $isAvailable = \DB::table('support_ekuitas')->where('bulan', $month)->where('tahun', $year)->count();
                  if ($isAvailable == 0) {
                    // insert ke table support ekuitas
                    \DB::table('support_ekuitas')->insert([
                        'bulan' => $month,
                        'tahun' => $year,
                        'laba_rugi_bersih' => $labaRugiBersih
                    ]);
                  }
                  else{
                    \DB::table('support_ekuitas')->where('bulan', $month)->where('tahun', $year)->update([
                        'bulan' => $month,
                        'tahun' => $year,
                        'laba_rugi_bersih' => $labaRugiBersih
                    ]);
                  }
              @endphp
              <thead>
                <tr>
                  <th>Laba Rugi Bersih</th>
                  <th>{{number_format($labaRugiBersih, 2, ',', '.')}}</th>
                </tr>
              </thead>
            </table>
          </div>
        @endif
@endsection