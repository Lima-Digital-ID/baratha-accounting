<?php

namespace App\Http\Controllers;

use App\Models\LogActivity;

class LogActivityController extends Controller
{
    public function index()
    {
        $this->param['pageInfo'] = 'Akitiftas / List Aktifitas User';

        try {
            $this->param['logActivity'] = LogActivity::join('users', 'users.id', 'log_activity.id_user')->paginate(10);
        } catch (\Illuminate\Database\QueryException $e) {
            return redirect()->back()->withErrors('Terjadi Kesalahan');
        }

        return view('log-activity.list-activity', $this->param);
    }
}
