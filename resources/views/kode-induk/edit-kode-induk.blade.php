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
    <form action="{{ route('kode-induk.update', $kodeInduk->kode_induk) }}" method="POST">
      @csrf
      @method('PUT')
      <label>Kode Induk</label>
      <input type="text" class="form-control {{ $errors->has('kode_induk') ? ' is-invalid' : '' }}" value="{{ old('kode_induk', $kodeInduk->kode_induk) }}" name="kode_induk" placeholder="Msaukan Kode Induk" readonly>
      @if ($errors->has('kode_induk'))
          <span class="invalid-feedback" role="alert">
              <strong>{{ $errors->first('kode_induk') }}</strong>
          </span>
      @endif

      <br>

      <label>Nama</label>
      <input type="text" class="form-control {{ $errors->has('nama') ? ' is-invalid' : '' }}" value="{{ old('nama', $kodeInduk->nama) }}" autofocus name="nama" placeholder="Nama Kode Induk">
      @if ($errors->has('nama'))
          <span class="invalid-feedback" role="alert">
              <strong>{{ $errors->first('nama') }}</strong>
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
