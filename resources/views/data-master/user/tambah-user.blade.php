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
    <form action="{{ route('user.store') }}" method="POST">
      @csrf
      <label>Nama</label>
      <input type="text" class="form-control {{ $errors->has('name') ? ' is-invalid' : '' }}" value="{{ old('name') }}" autofocus name="name" placeholder="Nama User">
      @if ($errors->has('name'))
          <span class="invalid-feedback" role="alert">
              <strong>{{ $errors->first('name') }}</strong>
          </span>
      @endif

      <br>

      <label>Username</label>
      <input type="text" class="form-control {{ $errors->has('username') ? ' is-invalid' : '' }}" value="{{ old('username') }}" autofocus name="username" placeholder="Username">
      @if ($errors->has('username'))
          <span class="invalid-feedback" role="alert">
              <strong>{{ $errors->first('username') }}</strong>
          </span>
      @endif

      <br>

      <label>Email</label>
      <input type="email" class="form-control {{ $errors->has('email') ? ' is-invalid' : '' }}" value="{{ old('email') }}" name="email" placeholder="Email">
      @if ($errors->has('email'))
          <span class="invalid-feedback" role="alert">
              <strong>{{ $errors->first('email') }}</strong>
          </span>
      @endif

      <br>

      <label>Password</label>
      <input type="password" class="form-control {{ $errors->has('password') ? ' is-invalid' : '' }}" value="{{ old('password') }}" name="password" placeholder="******">
      @if ($errors->has('password'))
          <span class="invalid-feedback" role="alert">
              <strong>{{ $errors->first('password') }}</strong>
          </span>
      @endif

      <br>

      <label>Konfirmasi Password</label>
      <input type="password" class="form-control {{ $errors->has('konfirmasi_password') ? ' is-invalid' : '' }}" value="{{ old('konfirmasi_password') }}" name="konfirmasi_password" placeholder="******">
      @if ($errors->has('konfirmasi_password'))
          <span class="invalid-feedback" role="alert">
              <strong>{{ $errors->first('konfirmasi_password') }}</strong>
          </span>
      @endif

      <br>

      <label for="">Akses</label>
      <select name="akses" id="akses" class="form-control select2 {{ $errors->has('akses') ? ' is-invalid' : '' }}">
        <option value="">--Pilih Akses--</option>
        <option value="Accounting" {{old('akses') == 'Accounting' ? 'selected' : ''}} >Accounting</option>
        <option value="Gudang" {{old('akses') == 'Gudang' ? 'selected' : ''}} >Admin Gudang</option>
        <option value="Owner" {{old('akses') == 'Owner' ? 'selected' : ''}} >Owner</option>
      </select>
      @if ($errors->has('akses'))
          <span class="invalid-feedback" role="alert">
              <strong>{{ $errors->first('akses') }}</strong>
          </span>
      @endif

        <div class="mt-4">
            <button type="reset" class="btn btn-default"> <span class="fa fa-times"></span> Cancel</button>
            &nbsp;
            <button type="submit" class="btn btn-primary"> <span class="fa fa-save"></span> Save</button>
        </div>
    </form>
  </div>
</div>
@endsection
