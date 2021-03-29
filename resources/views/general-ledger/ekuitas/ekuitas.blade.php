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
                <form action="{{ url('general-ledger/ekuitas') }}" method="get">
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
                  <h6 class="heading-small text-muted mb-3">Laporan Perubahan Modal/Ekuitas</h6>
                  <h6 class="heading-small text-dark mb-3">Periode : {{$month}} - {{$year}}</h6>
              </div>
              <div class="form-group mr-3">
                  <a href="{{ url('general-ledger/ekuitas/print')."?month=$_GET[month]&year=$_GET[year]"}}" class="btn btn-primary btn-sm" target="_blank">
                      <i class="fa fa-print" aria-hidden="true"></i> Cetak
                  </a>
                  <a href="{{ url('general-ledger/ekuitas/print')."?month=$_GET[month]&year=$_GET[year]&xls=true" }}" class="btn btn-success btn-sm" target="_blank">
                      <i class="fa fa-download"></i> Download xls
                  </a>
              </div>
          </div>
          @php
              
              // echo "<pre>";
              // print_r ($rekeningModal->kode_rekening);
              // echo "</pre>";
              $mutasiAwalDebetModal = 0;
              $mutasiAwalKreditModal = 0;

              // cek apakah ada jurnal awal di field kode untuk rekening modal
              $cekTransaksiAwalDiKode = \App\Models\Jurnal::whereMonth('tanggal', '<', $month)->whereYear('tanggal', '<=', $year)->where('kode', $rekeningModal->kode_rekening)->count();

              if ($cekTransaksiAwalDiKode > 0) {
                  $sumMutasiAwalDebetDiKode = \DB::table('jurnal')->where('kode', $rekeningModal->kode_rekening)->whereMonth('tanggal', '<', $month)->whereYear('tanggal', '<=', $year)->where('tipe', 'Debet')->sum('jurnal.nominal');
                  
                  $sumMutasiAwalKreditDiKode = \DB::table('jurnal')->where('kode', $rekeningModal->kode_rekening)->whereMonth('tanggal', '<', $month)->whereYear('tanggal', '<=', $year)->where('tipe', 'Kredit')->sum('jurnal.nominal');

                  if ($rekeningModal->tipe == 'Debet') {
                      $mutasiAwalDebetModal += $sumMutasiAwalDebetDiKode + $rekeningModal->saldo_awal;
                      $mutasiAwalKreditModal += $sumMutasiAwalKreditDiKode;
                  }
                  else{
                      $mutasiAwalDebetModal += $sumMutasiAwalDebetDiKode;
                      $mutasiAwalKreditModal += $sumMutasiAwalKreditDiKode + $rekeningModal->saldo_awal;
                  }

                  // cek apakah transaksi sebelumnya juga terdapat di field lawan
                  $cekTransaksiAwalDiLawan = \App\Models\Jurnal::whereMonth('tanggal', '<', $month)->whereYear('tanggal', '<=', $year)->where('lawan', $rekeningModal->kode_rekening)->count();
                  if ($cekTransaksiAwalDiLawan > 0) {
                      $sumMutasiAwalDebetDiLawan = \DB::table('jurnal')->where('lawan', $rekeningModal->kode_rekening)->whereMonth('tanggal', '<', $month)->whereYear('tanggal', '<=', $year)->where('tipe', 'Kredit')->sum('jurnal.nominal');
                  
                      $sumMutasiAwalKreditDiLawan = \DB::table('jurnal')->where('lawan', $rekeningModal->kode_rekening)->whereMonth('tanggal', '<', $month)->whereYear('tanggal', '<=', $year)->where('tipe', 'Debet')->sum('jurnal.nominal');

                      $mutasiAwalDebetModal += $sumMutasiAwalDebetDiLawan;
                      $mutasiAwalKreditModal += $sumMutasiAwalKreditDiLawan;
                  }
              }
              else{ // cek apakah ada jurnal awal di field lawan
                  $cekTransaksiAwalDiLawan = \App\Models\Jurnal::whereMonth('tanggal', '<', $month)->whereYear('tanggal', '<=', $year)->where('lawan', $rekeningModal->kode_rekening)->count();
                  if ($cekTransaksiAwalDiLawan > 0) {
                      $sumMutasiAwalDebetDiLawan = \DB::table('jurnal')->where('lawan', $rekeningModal->kode_rekening)->whereMonth('tanggal', '<', $month)->whereYear('tanggal', '<=', $year)->where('tipe', 'Kredit')->sum('jurnal.nominal');
                  
                      $sumMutasiAwalKreditDiLawan = \DB::table('jurnal')->where('lawan', $rekeningModal->kode_rekening)->whereMonth('tanggal', '<', $month)->whereYear('tanggal', '<=', $year)->where('tipe', 'Debet')->sum('jurnal.nominal');

                      if ($rekeningModal->tipe == 'Debet') {
                          $mutasiAwalDebetModal += $sumMutasiAwalDebetDiLawan + $rekeningModal->saldo_awal;

                          $mutasiAwalKreditModal += $sumMutasiAwalKreditDiLawan;
                      }
                      else{
                          $mutasiAwalDebetModal += $sumMutasiAwalDebetDiLawan;
                          $mutasiAwalKreditModal += $sumMutasiAwalKreditDiLawan + $rekeningModal->saldo_awal;
                      }
                  }
                  else{ //tidak ada jurnal awal di field kode maupun lawan
                      if ($rekeningModal->tipe == 'Debet') {
                          $mutasiAwalDebetModal += $rekeningModal->saldo_awal;
                      }
                      else{
                          $mutasiAwalKreditModal += $rekeningModal->saldo_awal;
                      }
                  }

                  $saldoAwalModal = $mutasiAwalKreditModal - $mutasiAwalDebetModal;
              }

              // prive
              // cek transaksi di field kode
              $mutasiDebetPrive = 0;
              $mutasiKreditPrive = 0;

              $cekTransaksiDiKode = \App\Models\Jurnal::whereMonth('tanggal', '<=', $month)->whereYear('tanggal', '<=', $year)->where('kode', $rekeningPrive->kode_rekening)->count();
                                
              if ($cekTransaksiDiKode > 0) {
                  $sumMutasiDebetDiKode = \DB::table('jurnal')->where('kode', $rekeningPrive->kode_rekening)->whereMonth('tanggal', '<=', $month)->whereYear('tanggal', '<=', $year)->where('tipe', 'Debet')->sum('jurnal.nominal');
                  
                  $sumMutasiKreditDiKode = \DB::table('jurnal')->where('kode', $rekeningPrive->kode_rekening)->whereMonth('tanggal', '<=', $month)->whereYear('tanggal', '<=', $year)->where('tipe', 'Kredit')->sum('jurnal.nominal');

                  $mutasiDebetPrive += $sumMutasiDebetDiKode;
                  $mutasiKreditPrive += $sumMutasiKreditDiKode;

                  // cek transaksi di field lawan
                  $cekTransaksiDiLawan = \App\Models\Jurnal::whereMonth('tanggal', '<=', $month)->whereYear('tanggal', '<=', $year)->where('lawan', $rekeningPrive->kode_rekening)->count();

                  if ($cekTransaksiDiLawan > 0) {
                      $sumMutasiDebetDiLawan = \DB::table('jurnal')->where('lawan', $rekeningPrive->kode_rekening)->whereMonth('tanggal', '<=', $month)->whereYear('tanggal', '<=', $year)->where('tipe', 'Kredit')->sum('jurnal.nominal');
                      
                      $sumMutasiKreditDiLawan = \DB::table('jurnal')->where('lawan', $rekeningPrive->kode_rekening)->whereMonth('tanggal', '<=', $month)->whereYear('tanggal', '<=', $year)->where('tipe', 'Debet')->sum('jurnal.nominal');

                      $mutasiDebetPrive += $sumMutasiDebetDiLawan;
                      $mutasiKreditPrive += $sumMutasiKreditDiLawan;
                  }
              }
              else{ // cek transaksi di field lawan
                  // cek transaksi di field lawan
                  $cekTransaksiDiLawan = \App\Models\Jurnal::whereMonth('tanggal', '<=', $month)->whereYear('tanggal', '<=', $year)->where('lawan', $rekeningPrive->kode_rekening)->count();
                  if ($cekTransaksiDiLawan > 0) {
                      $sumMutasiDebetDiLawan = \DB::table('jurnal')->where('lawan', $rekeningPrive->kode_rekening)->whereMonth('tanggal', '<=', $month)->whereYear('tanggal', '<=', $year)->where('tipe', 'Kredit')->sum('jurnal.nominal');
                      
                      $sumMutasiKreditDiLawan = \DB::table('jurnal')->where('lawan', $rekeningPrive->kode_rekening)->whereMonth('tanggal', '<=', $month)->whereYear('tanggal', '<=', $year)->where('tipe', 'Debet')->sum('jurnal.nominal');

                      $mutasiDebetPrive += $sumMutasiDebetDiLawan;
                      $mutasiKreditPrive += $sumMutasiKreditDiLawan;
                  }
              }
              
              $prive = $mutasiKreditPrive - $mutasiDebetPrive;
          @endphp
          <div class="table-responsive">
            <table class="table table-bordered table-custom">
              <thead>
                <tr>
                  <th>Modal Awal</th>
                  <th>{{number_format($saldoAwalModal, 2, ',', '.')}}</th>
                </tr>
                <tr>
                  <th>Laba Bersih</th>
                  <th>{{number_format($labaRugiBersih, 2, ',', '.')}}</th>
                </tr>
                <tr>
                  <th>Prive</th>
                  <th>( {{number_format($prive * -1, 2, ',', '.')}} )</th>
                </tr>
                <tr>
                  <th>Modal Akhir</th>
                  <th>{{number_format($saldoAwalModal + $labaRugiBersih + $prive, 2, ',', '.')}}</th>
                </tr>
              </thead>
              
            </table>
          </div>
        @endif
@endsection