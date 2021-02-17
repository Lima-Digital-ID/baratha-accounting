@extends('common.template')
@section('container')

<div class="card shadow py-2">
  <div class="card-body">
    @if (session('status'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('status') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif
    @if (session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif
    <a href="{{$btnRight['link']}}" class="btn btn-primary mb-3"> <span class="fa fa-arrow-alt-circle-left"></span> {{$btnRight['text']}}</a>
    <hr>
    <form action="{{ route('user.update', $user->id) }}" method="POST">
      @csrf
      @method('put')
      <label>Nama</label>
      <input type="text" class="form-control {{ $errors->has('name') ? ' is-invalid' : '' }}" value="{{ old('name', $user->name) }}" autofocus name="name" placeholder="ex : Anthony Davis">
      @if ($errors->has('name'))
          <span class="invalid-feedback" role="alert">
              <strong>{{ $errors->first('name') }}</strong>
          </span>
      @endif

      <br>

      <label>Username</label>
      <input type="text" class="form-control {{ $errors->has('username') ? ' is-invalid' : '' }}" value="{{ old('username', $user->username) }}" autofocus name="username" placeholder="ex : Anthony Davis" readonly>
      @if ($errors->has('username'))
          <span class="invalid-feedback" role="alert">
              <strong>{{ $errors->first('username') }}</strong>
          </span>
      @endif

      <br>

      <label>Email</label>
      <input type="email" class="form-control {{ $errors->has('email') ? ' is-invalid' : '' }}" value="{{ old('email', $user->email) }}" name="email" placeholder="ex : anthonydavis@mail.test" readonly>
      @if ($errors->has('email'))
          <span class="invalid-feedback" role="alert">
              <strong>{{ $errors->first('email') }}</strong>
          </span>
      @endif

      <br>

      <label for="">Akses</label>
      <select name="akses" id="akses" class="form-control select2 {{ $errors->has('akses') ? ' is-invalid' : '' }}">
        <option value="">--Pilih Akses--</option>
        <option value="Akuntan" {{old('akses', $user->akses) == 'Akuntan' ? 'selected' : ''}} >Akuntan</option>
        <option value="Persediaan" {{old('akses', $user->akses) == 'Persediaan' ? 'selected' : ''}} >Persediaan</option>
        <option value="Pembelian" {{old('akses', $user->akses) == 'Pembelian' ? 'selected' : ''}} >Pembelian</option>
        <option value="Super Admin" {{old('akses', $user->akses) == 'Super Admin' ? 'selected' : ''}} >Super Admin</option>
      </select>

      <br><br>

      <button type="reset" class="btn btn-default"> <span class="fa fa-times"></span> Cancel</button>

      <button type="submit" class="btn btn-primary"> <span class="fa fa-save"></span> Save</button>
    </form>
  </div>
</div>
@endsection
