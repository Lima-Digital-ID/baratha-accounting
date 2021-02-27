<?php

namespace App\Http\Controllers;

use App\Models\LogActivity;
use App\Models\User;
use Illuminate\Http\Request;

class LogActivityController extends Controller
{
    public function index(Request $request)
    {
        $this->param['pageInfo'] = 'List Aktifitas User';

        try {
            $start = $request->get('start');
            $end = $request->get('end');
            $idUser = $request->get('id_user');
            $logActivity = LogActivity::select('log_activity.id', 'log_activity.id_user', 'log_activity.created_at', 'log_activity.tipe', 'log_activity.keterangan', 'log_activity.jenis_transaksi', 'users.name')->join('users', 'users.id', 'log_activity.id_user')->orderBy('log_activity.id', 'DESC');

            if ($idUser) {
                $logActivity->where('id_user', $idUser);
            }

            if ($start && $end) {
                $logActivity->whereBetween('log_activity.created_at', ["$start 00:00:00", "$end 23:59:59"]);
            }

            $this->param['users'] = User::get();
        } catch (\Illuminate\Database\QueryException $e) {
            return redirect()->back()->withErrors('Terjadi Kesalahan');
        }

        return view('log-activity.list-activity', $this->param, ['logActivity' => $logActivity->paginate(10)]);
    }
}
