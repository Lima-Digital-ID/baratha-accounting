<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use \App\Models\User;

class UserController extends Controller
{
    private $param;
    public function __construct()
    {
        $this->param['icon'] = 'fa-user';
    }

    public function index(Request $request)
    {
        $this->param['pageInfo'] = 'Manage User / List User';
        $this->param['btnRight']['text'] = 'Tambah Data';
        $this->param['btnRight']['link'] = route('user.create');

        try {
            $keyword = $request->get('keyword');
            if ($keyword) {
                $user = User::where('name', 'LIKE', "%$keyword%")->orWhere('email', 'LIKE', "%$keyword%")->paginate(10);
            }
            else{
                $user = User::paginate(10);
            }
        } catch (\Illuminate\Database\QueryException $e) {
            return redirect()->back()->withStatus('Terjadi Kesalahan');
        }
                
        return \view('user.list-user', ['user' => $user], $this->param);
    }

    public function create()
    {
        $this->param['pageInfo'] = 'Manage User / Tambah Data';
        $this->param['btnRight']['text'] = 'Lihat Data';
        $this->param['btnRight']['link'] = route('user.index');

        return \view('user.tambah-user', $this->param);
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required',
            'email' => 'required|email|unique:users',
            'akses' => 'required',
            'password' => 'required',
            'konfirmasi_password' => 'required|same:password',
        ]);
        try{
    
            $newUser = new User;
    
            $newUser->name = $request->get('name');
            $newUser->email = $request->get('email');
            $newUser->password = \Hash::make($request->get('password'));
            $newUser->akses = $request->get('akses');
    
            $newUser->save();
    
            return redirect()->back()->withStatus('Data berhasil ditambahkan.');
        }
        catch(\Exception $e){
            return redirect()->back()->withStatus('Terjadi kesalahan. : '. $e->getMessage());
        }
        catch(\Illuminate\Database\QueryException $e){
            return redirect()->back()->withStatus('Terjadi kesalahan pada database : '. $e->getMessage());
        }
    }

    public function edit($id)
    {
        try{
            $this->param['pageInfo'] = 'Manage User / Edit Data';
            $this->param['btnRight']['text'] = 'Lihat Data';
            $this->param['btnRight']['link'] = route('user.index');
            $this->param['user'] = User::find($id);

            return \view('user.edit-user', $this->param);
        }
        catch(\Exception $e){
            return redirect()->back()->withError('Terjadi kesalahan : '. $e->getMessage());
        }
        catch(\Illuminate\Database\QueryException $e){
            return redirect()->back()->withError('Terjadi kesalahan pada database : '. $e->getMessage());
        }
    }

    public function update(Request $request, $id)
    {
        $user = User::find($id);

        $isUnique = $user->email == $request->email ? '' : '|unique:users';

        $validatedData = $request->validate([
            'name' => 'required',
            'email' => '|required|email'.$isUnique,
        ]);
        try{

            $user->name = $request->get('name');
            $user->email = $request->get('email');
            $user->akses = $request->get('akses');
            $user->save();

            return redirect()->back()->withStatus('Data berhasil diperbarui.');
        }
        catch(\Exception $e){
            return redirect()->back()->withError('Terjadi kesalahan : '. $e->getMessage());
        }
        catch(\Illuminate\Database\QueryException $e){
            return redirect()->back()->withError('Terjadi kesalahan pada database : '. $e->getMessage());
        }
    }

    public function destroy($id)
    {
        try{
            $member = User::findOrFail($id);

            $member->delete();

            return redirect()->route('user.index')->withStatus('Data berhasil dihapus.');
        }
        catch(\Exception $e){
            return redirect()->route('user.index')->withError('Terjadi kesalahan : '. $e->getMessage());
        }
        catch(\Illuminate\Database\QueryException $e){
            return redirect()->route('user.index')->withError('Terjadi kesalahan pada database : '. $e->getMessage());
        }
        
    }
}
