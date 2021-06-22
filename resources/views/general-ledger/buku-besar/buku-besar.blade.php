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
                <form action="{{ url('general-ledger/buku-besar') }}" method="get">
                    <div class="row">

                        <div class="col-md-6 mb-4">
                            <label for="">Kode Rekening</label>
                            <select name="kodeRekeningDari" id="kodeRekeningDari" class="form-control select2" required>
                                <option value="">--Pilih Rekening--</option>
                                @foreach ($allRekening as $item)
                                    <option value="{{$item->kode_rekening}}" {{!is_null(Request::get('kodeRekeningDari')) && Request::get('kodeRekeningDari') == $item->kode_rekening ? 'selected' : '' }} >{{$item->kode_rekening . ' ~ '.$item->nama}}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-6 mb-4">
                            <label for="">Sampai</label>
                            <select name="kodeRekeningSampai" class="form-control select2" required >
                                <option value="">--Pilih Rekening--</option>
                                @foreach ($allRekening as $item)
                                    <option value="{{$item->kode_rekening}}" {{!is_null(Request::get('kodeRekeningSampai')) && Request::get('kodeRekeningSampai') == $item->kode_rekening ? 'selected' : '' }} >{{$item->kode_rekening . ' ~ '.$item->nama}}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-6 mb-4">
                            <label for="">Tanggal Dari</label>
                            <input type="text" name="tanggalDari" autocomplete="off" class="form-control datepickerDate" value="{{!is_null(Request::get('tanggalDari')) ? Request::get('tanggalDari') : date('Y-m-d') }}" required>
                        </div>
                        
                        <div class="col-md-6 mb-4">
                            <label for="">Tanggal Sampai</label>
                            <input type="text" name="tanggalSampai" autocomplete="off" class="form-control datepickerDate" value="{{!is_null(Request::get('tanggalSampai')) ? Request::get('tanggalSampai') : date('Y-m-d')}}" required>
                        </div>

                        <div class="col-md-6">
                            <button type="submit" class="btn btn-primary"> <i class="fas fa-filter"></i> Filter</button>
                        </div>

                    </div>
                </form>
            </div>
        </div>
        @if ( !is_null(Request::get('kodeRekeningDari')) && !is_null(Request::get('tanggalDari')) && !is_null(Request::get('kodeRekeningSampai')) && !is_null(Request::get('tanggalSampai')) )
        @php
            $tanggalDari = Request::get('tanggalDari');
            $tanggalSampai = Request::get('tanggalSampai');
        @endphp
            <br>
            <hr>
            <div class="row d-flex mb-3 justify-content-between">
                <div class="form-group ml-auto">
                    <a href="{{ url('general-ledger/buku-besar/print')."?tanggalDari=$_GET[tanggalDari]&tanggalSampai=$_GET[tanggalSampai]&kodeRekeningDari=$_GET[kodeRekeningDari]&kodeRekeningSampai=$_GET[kodeRekeningSampai]" }}" class="btn btn-primary btn-sm" target="_blank">
                        <i class="fa fa-print" aria-hidden="true"></i> Cetak
                    </a>
                    <a href="{{ url('general-ledger/buku-besar/print')."?tanggalDari=$_GET[tanggalDari]&tanggalSampai=$_GET[tanggalSampai]&kodeRekeningDari=$_GET[kodeRekeningDari]&kodeRekeningSampai=$_GET[kodeRekeningSampai]&xls=true" }}" class="btn btn-success btn-sm" target="_blank">
                        <i class="fa fa-download"></i> Download xls
                    </a>
                </div>
            </div>
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
                <h6><b>Buku Besar : {{$item->nama . ' - ' . $item->kode_rekening}} </b></h6>
              </center>
              <div class="table-responsive">
                <table class="table table-custom">
                  <thead>
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
                          <td>{{$val->keterangan}}</td>
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
                    <thead>
                      <tr>
                        <th colspan="4" style="text-align: center">Total</th>
                        <th>{{number_format($totalDebet, 2, ',', '.')}}</th>
                        <th>{{number_format($totalKredit, 2, ',', '.')}}</th>
                        <th></th>
                      </tr>
                    </thead>
                  </tbody>
                </table>
              </div>
              <hr>
            @endforeach
        @endif
@endsection
