<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use \App\Models\KodeRekening;

class EkuitasController extends Controller
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

                $this->param['labaRugiBersihAwal'] = \DB::table('support_ekuitas')->where('bulan', '<', $month)->where('tahun', '<=', $year)->sum('laba_rugi_bersih');
                
                $this->param['labaRugiBersih'] = \DB::table('support_ekuitas')->where('bulan', $month)->where('tahun', $year)->sum('laba_rugi_bersih');

                $this->param['rekeningModal'] = KodeRekening::select('kode_rekening', 'nama', 'saldo_awal', 'tipe')->where('kode_rekening', '3100')->orderBy('kode_rekening', 'ASC')->get()[0];
                
                $this->param['rekeningPrive'] = KodeRekening::select('kode_rekening', 'nama', 'saldo_awal', 'tipe')->where('kode_rekening', '3200')->orderBy('kode_rekening', 'ASC')->get()[0];

                $this->param['month'] = $month;
                $this->param['year'] = $year;
                
            }
        } catch (\Illuminate\Database\QueryException $e) {
            return redirect()->back()->withStatus($e->getMessage());
        }

        return \view('general-ledger.ekuitas.ekuitas', $this->param);
    }
}
