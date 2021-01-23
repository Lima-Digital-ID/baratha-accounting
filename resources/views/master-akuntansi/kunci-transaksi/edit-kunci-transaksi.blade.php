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
    <form action="{{ route('kunci-transaksi.update', $kunciTransaksi->id) }}" method="POST">
      @csrf
      @method('PUT')
      <label>Kunci Transaksi</label>
      <input type="text" class="form-control {{ $errors->has('jenis_transaksi') ? ' is-invalid' : '' }}" value="{{ old('jenis_transaksi', $kunciTransaksi->jenis_transaksi) }}" name="jenis_transaksi" placeholder="Masukan Jenis Transaksi" readonly>
      @if ($errors->has('jenis_transaksi'))
          <span class="invalid-feedback" role="alert">
              <strong>{{ $errors->first('jenis_transaksi') }}</strong>
          </span>
      @endif

      <br>

      <label>Tanggal Kunci</label>
      <input class="form-control" type="date" name="tanggal_kunci" id="tanggal_kunci" value="{{ old('tanggal_kunci', $kunciTransaksi->tanggal_kunci) }}">
      @if ($errors->has('tanggal_kunci'))
          <span class="invalid-feedback" role="alert">
              <strong>{{ $errors->first('tanggal_kunci') }}</strong>
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
