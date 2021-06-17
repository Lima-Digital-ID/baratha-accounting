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
    <form action="{{ route('customer.store') }}" method="POST">
      @csrf
      <label>Kode Customer</label>
      <input type="text" class="form-control {{ $errors->has('kode_customer') ? ' is-invalid' : '' }}" value="{{ old('kode_customer', $kode_customer) }}" autofocus name="kode_customer" placeholder="ex : Customer 1" readonly>
      @if ($errors->has('kode_customer'))
          <span class="invalid-feedback" role="alert">
              <strong>{{ $errors->first('kode_customer') }}</strong>
          </span>
      @endif

      <br>

      <label>Nama</label>
      <input type="text" class="form-control {{ $errors->has('nama') ? ' is-invalid' : '' }}" value="{{ old('nama') }}" autofocus name="nama" placeholder="ex : Anthony Davis">
      @if ($errors->has('nama'))
          <span class="invalid-feedback" role="alert">
              <strong>{{ $errors->first('nama') }}</strong>
          </span>
      @endif

      <br>

      <label>Alamat</label>
      <textarea class="form-control" name="alamat" id="alamat" cols="30" rows="5"></textarea>
      @if ($errors->has('alamat'))
          <span class="invalid-feedback" role="alert">
              <strong>{{ $errors->first('name') }}</strong>
          </span>
      @endif

      <br>

      <label>No Hp</label>
      <input type="text" class="form-control {{ $errors->has('no_hp') ? ' is-invalid' : '' }}" value="{{ old('no_hp') }}" autofocus name="no_hp" placeholder="ex : 085331xxxxxx">
      @if ($errors->has('no_hp'))
          <span class="invalid-feedback" role="alert">
              <strong>{{ $errors->first('no_hp') }}</strong>
          </span>
      @endif

      <br>

      <label>Piutang</label>
      <input type="text" class="form-control {{ $errors->has('piutang') ? ' is-invalid' : '' }}" value="{{ old('piutang', 0) }}" autofocus name="piutang" placeholder="ex : 1.000.000,00">
      @if ($errors->has('piutang'))
          <span class="invalid-feedback" role="alert">
              <strong>{{ $errors->first('piutang') }}</strong>
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
