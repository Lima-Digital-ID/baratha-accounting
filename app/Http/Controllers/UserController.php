<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use \App\Models\User;

class UserController extends Controller
{
    private $param;
    public function __construct()
    {
        $this->param['icon'] = 'fa-cog';
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
                
        return \view('data-master.user.list-user', ['user' => $user], $this->param);
    }

    public function create()
    {
        $this->param['pageInfo'] = 'Manage User / Tambah Data';
        $this->param['btnRight']['text'] = 'Lihat Data';
        $this->param['btnRight']['link'] = route('user.index');

        return \view('data-master.user.tambah-user', $this->param);
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required',
            'username' => 'required|unique:users,username',
            'email' => 'required|email|unique:users',
            'akses' => 'required|not_in:',
            'password' => 'required',
            'konfirmasi_password' => 'required|same:password',
        ],
        [
            'required' => ':attribute tidak boleh kosong.',
            'akses.required' => ':attribute harus dipilih.',
            'email' => 'Masukan email yang valid.',
            'same' => 'Password & konfirmasi password harus sama',
            'unique' => ':attribute telah terdaftar'
        ],
        [
            'name' => 'Nama',
            'username' => 'Username',
            'email' => 'Alamat email',
            'akses' => 'Akses',
            'password' => 'Password'
        ]);
        try{
            $newUser = new User;
    
            $newUser->name = $request->get('name');
            $newUser->username = $request->get('username');
            $newUser->email = $request->get('email');
            $newUser->password = \Hash::make($request->get('password'));
            $newUser->akses = $request->get('akses');

            $newUser->save();

            return redirect()->route('user.index')->withStatus('Data berhasil ditambahkan.');
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

            return \view('data-master.user.edit-user', $this->param);
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

        // $isUnique = $user->email == $request->email ? '' : '|unique:users,email';
        $validatedData = $request->validate([
            'name' => 'required',
            'akses' => 'required:not_in:'
            // 'email' => 'required|email'.$isUnique,
        ],
        [
            'name.required' => ':attribute tidak boleh kosong.',
            'akses.required' => ':attribute harus dipilih.'
        ],
        [
           'name' => 'Nama',
           'akses' => 'Akses' 
        ]);
        try{

            $user->name = $request->get('name');
            // $user->email = $request->get('email');
            $user->akses = $request->get('akses');
            $user->save();

            return redirect()->route('user.index')->withStatus('Data berhasil diperbarui.');
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
