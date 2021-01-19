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
    <hr>
    <form action="{{ route('perusahaan.update', $perusahaan->id) }}" method="POST">
      @csrf
      @method('PUT')
      <label>Nama</label>
      <input type="text" class="form-control {{ $errors->has('nama') ? ' is-invalid' : '' }}" value="{{ old('nama', $perusahaan->nama) }}" name="nama" placeholder="Nama Perusahaan" autofocus>
      @if ($errors->has('nama'))
          <span class="invalid-feedback" role="alert">
              <strong>{{ $errors->first('nama') }}</strong>
          </span>
      @endif

      <br>
      
      <label>Alamat</label>
      <textarea name="alamat" class="form-control {{ $errors->has('alamat') ? ' is-invalid' : '' }}" >{{ old('alamat', $perusahaan->alamat) }}</textarea>
      @if ($errors->has('alamat'))
          <span class="invalid-feedback" role="alert">
              <strong>{{ $errors->first('alamat') }}</strong>
          </span>
      @endif

      <br>

      <label>Kota</label>
      <input type="text" class="form-control {{ $errors->has('kota') ? ' is-invalid' : '' }}" value="{{ old('kota', $perusahaan->kota) }}" name="kota" placeholder="Kota">
      @if ($errors->has('kota'))
          <span class="invalid-feedback" role="alert">
              <strong>{{ $errors->first('kota') }}</strong>
          </span>
      @endif

      <br>

      <label>Provinsi</label>
      <input type="text" class="form-control {{ $errors->has('provinsi') ? ' is-invalid' : '' }}" value="{{ old('provinsi', $perusahaan->provinsi) }}" name="provinsi" placeholder="Provinsi">
      @if ($errors->has('provinsi'))
          <span class="invalid-feedback" role="alert">
              <strong>{{ $errors->first('provinsi') }}</strong>
          </span>
      @endif

      <br>

      <label>Telepon</label>
      <input type="text" class="form-control {{ $errors->has('telepon') ? ' is-invalid' : '' }}" value="{{ old('telepon', $perusahaan->telepon) }}" name="telepon" placeholder="Telepon">
      @if ($errors->has('telepon'))
          <span class="invalid-feedback" role="alert">
              <strong>{{ $errors->first('telepon') }}</strong>
          </span>
      @endif

      <br>

      <label>Email</label>
      <input type="text" class="form-control {{ $errors->has('email') ? ' is-invalid' : '' }}" value="{{ old('email', $perusahaan->email) }}" name="email" placeholder="Email">
      @if ($errors->has('email'))
          <span class="invalid-feedback" role="alert">
              <strong>{{ $errors->first('email') }}</strong>
          </span>
      @endif

      <br>
      

      <button type="reset" class="btn btn-default"> <span class="fa fa-times"></span> Cancel</button>
      &nbsp;
      <button type="submit" class="btn btn-primary"> <span class="fa fa-save"></span> Save</button>
    </form>
  </div>
</div>
@endsection
