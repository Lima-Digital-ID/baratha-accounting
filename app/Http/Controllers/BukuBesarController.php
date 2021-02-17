<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use \App\Models\Jurnal;
use \App\Models\KodeRekening;

class BukuBesarController extends Controller
{
    private $param;

    public function index(Request $request)
    {
        $this->param['pageInfo'] = '-';
        
        try {
            $this->param['allRekening'] = KodeRekening::orderBy('kode_rekening', 'ASC')->get();

            $kodeRekeningDari = $request->get('kodeRekeningDari');
            $kodeRekeningSampai = $request->get('kodeRekeningSampai');
            $tanggalDari = $request->get('tanggalDari');
            $tanggalSampai = $request->get('tanggalSampai');

            if (!is_null($kodeRekeningDari) && !is_null($kodeRekeningSampai) && !is_null($tanggalDari) && !is_null($tanggalSampai) ) {
                $this->param['kodeRekening'] = KodeRekening::select('kode_rekening', 'nama', 'saldo_awal', 'tipe')->whereBetween('kode_rekening', [$kodeRekeningDari, $kodeRekeningSampai])->orderBy('kode_rekening', 'ASC')->get();
            }
        } catch (\Illuminate\Database\QueryException $e) {
            return redirect()->back()->withStatus($e->getMessage());
        }

        return \view('general-ledger.buku-besar.buku-besar', $this->param);
    }
}
