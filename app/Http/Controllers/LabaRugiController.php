<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use \App\Models\KodeRekening;

class LabaRugiController extends Controller
{
    private $param;

    public function index(Request $request)
    {
        $this->param['pageInfo'] = '-';
        
        try {

            $this->param['allBulan'] = array(
                [
                    'bulan' => '01',
                    'nama' => 'Januari'
                ],
                [
                    'bulan' => '02',
                    'nama' => 'Februari'
                ],
                [
                    'bulan' => '03',
                    'nama' => 'Maret'
                ],
                [
                    'bulan' => '04',
                    'nama' => 'April'
                ],
                [
                    'bulan' => '05',
                    'nama' => 'Mei'
                ],
                [
                    'bulan' => '06',
                    'nama' => 'Juni'
                ],
                [
                    'bulan' => '07',
                    'nama' => 'Juli'
                ],
                [
                    'bulan' => '08',
                    'nama' => 'Agustus'
                ],
                [
                    'bulan' => '09',
                    'nama' => 'September'
                ],
                [
                    'bulan' => '10',
                    'nama' => 'Oktober'
                ],
                [
                    'bulan' => '11',
                    'nama' => 'November'
                ],
                [
                    'bulan' => '12',
                    'nama' => 'Desember'
                ],
            );

            $month = $request->get('month');
            $year = $request->get('year');

            if (!is_null($month) && !is_null($year)) {

                $this->param['rekeningPenjualan'] = KodeRekening::select('kode_rekening', 'nama', 'saldo_awal', 'tipe')->where('kode_rekening', 'LIKE', '4%')->orderBy('kode_rekening', 'ASC')->get();

                $this->param['hpp'] = \DB::table('rekap_hpp_bulanan')->select('nominal')->where('bulan', $month)->where('tahun', $year)->get();

                $this->param['rekeningBeban'] = KodeRekening::select('kode_rekening', 'nama', 'saldo_awal', 'tipe')->where('kode_rekening', 'LIKE', '5%')->orderBy('kode_rekening', 'ASC')->get();
                
                $this->param['rekeningPajak'] = KodeRekening::select('kode_rekening', 'nama', 'saldo_awal', 'tipe')->where('kode_rekening', 'LIKE', '6%')->orderBy('kode_rekening', 'ASC')->get();

                // if (count($this->param['hpp']) == 0) {
                //     return redirect('/general-ledger/laba-rugi')->withStatus('Hpp Bulan Tersebut Belum Diinput.');
                // }
                // else{
                //     $this->param['hpp'] =  $this->param['hpp'][0]->nominal;
                // }

                $this->param['month'] = $month;
                $this->param['year'] = $year;
                
            }
        } catch (\Illuminate\Database\QueryException $e) {
            return redirect()->back()->withStatus($e->getMessage());
        }

        return \view('general-ledger.laba-rugi.laba-rugi', $this->param);
    }

    public function print(Request $request)
    {
        try {

            $month = $request->get('month');
            $year = $request->get('year');

            if (!is_null($month) && !is_null($year)) {

                $this->param['rekeningPenjualan'] = KodeRekening::select('kode_rekening', 'nama', 'saldo_awal', 'tipe')->where('kode_rekening', 'LIKE', '4%')->orderBy('kode_rekening', 'ASC')->get();

                $this->param['hpp'] = \DB::table('rekap_hpp_bulanan')->select('nominal')->where('bulan', $month)->where('tahun', $year)->get();

                $this->param['rekeningBeban'] = KodeRekening::select('kode_rekening', 'nama', 'saldo_awal', 'tipe')->where('kode_rekening', 'LIKE', '5%')->orderBy('kode_rekening', 'ASC')->get();
                
                $this->param['rekeningPajak'] = KodeRekening::select('kode_rekening', 'nama', 'saldo_awal', 'tipe')->where('kode_rekening', 'LIKE', '6%')->orderBy('kode_rekening', 'ASC')->get();

                if (count($this->param['hpp']) == 0) {
                    return redirect('/general-ledger/laba-rugi')->withStatus('Hpp Bulan Tersebut Belum Diinput.');
                }
                else{
                    $this->param['hpp'] =  $this->param['hpp'][0]->nominal;
                }

                $this->param['month'] = $month;
                $this->param['year'] = $year;
                
            }
        } catch (\Illuminate\Database\QueryException $e) {
            return redirect()->back()->withStatus($e->getMessage());
        }

        return \view('general-ledger.laba-rugi.print-laba-rugi', $this->param);
    }
}
