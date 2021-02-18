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
                <form action="{{ url('general-ledger/neraca') }}" method="get">
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
                            <input type="text" name="tanggalDari" autocomplete="off" class="form-control datepicker" value="{{!is_null(Request::get('tanggalDari')) ? Request::get('tanggalDari') : '' }}" required>
                        </div>
                        
                        <div class="col-md-6 mb-4">
                            <label for="">Tanggal Sampai</label>
                            <input type="text" name="tanggalSampai" autocomplete="off" class="form-control datepicker" value="{{!is_null(Request::get('tanggalSampai')) ? Request::get('tanggalSampai') : ''}}" required>
                        </div>

                        <div class="col-md-6">
                            <button type="submit" class="btn btn-primary"> <i class="fas fa-filter"></i> Filter</button>
                        </div>

                    </div>
                </form>
            </div>
        </div>
        @if ( !is_null(Request::get('kodeRekeningDari')) && !is_null(Request::get('tanggalDari')) && !is_null(Request::get('kodeRekeningSampai')) && !is_null(Request::get('tanggalSampai')) )
            <hr>
            <div class="table-responsive">
                <table class="table table-bordered table-custom">
                    <thead>
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
                    <thead>
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
            </div>
        @endif
@endsection
