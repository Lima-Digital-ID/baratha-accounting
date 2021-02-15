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
    <form action="{{ route('kode-rekening.store') }}" method="POST">
      @csrf

      <label>Kode Induk</label>
      <select name="kode_induk" id="kode_induk" class="form-control select2 {{ $errors->has('kode_induk') ? ' is-invalid' : '' }}">
        <option value="">--Pilih Kode Induk--</option>
        @foreach ($kodeInduk as $item)
            <option value="{{$item->kode_induk}}" {{old('kode_induk') == $item->kode_induk ? 'selected' : ''}} >{{$item->kode_induk . ' -- '. $item->nama}}</option>
            
        @endforeach
      </select>
      @if ($errors->has('kode_induk'))
          <span class="invalid-feedback" role="alert">
              <strong>{{ $errors->first('kode_induk') }}</strong>
          </span>
      @endif

      <br>
      <br>

      <label>Kode Rekening</label>
      <input type="text" id="kode_rekening" class="form-control {{ $errors->has('kode_rekening') ? ' is-invalid' : '' }}" value="{{ old('kode_rekening') }}" autofocus name="kode_rekening" placeholder="Kode Rekening">
      @if ($errors->has('kode_rekening'))
          <span class="invalid-feedback" role="alert">
              <strong>{{ $errors->first('kode_rekening') }}</strong>
          </span>
      @endif

      <br>

      <label>Nama</label>
      <input type="text" class="form-control {{ $errors->has('nama') ? ' is-invalid' : '' }}" value="{{ old('nama') }}" autofocus name="nama" placeholder="Nama Kode Rekening">
      @if ($errors->has('nama'))
          <span class="invalid-feedback" role="alert">
              <strong>{{ $errors->first('nama') }}</strong>
          </span>
      @endif

      <br>

      <label for="">Tipe</label>
      <br>
      <div class="form-check form-check-inline">
        <input class="form-check-input {{ $errors->has('tipe') ? ' is-invalid' : '' }}" type="radio" name="tipe" id="Debet" value="Debet" {{ old('tipe') == 'Debet' ? 'checked' : '' }}>
        <label class="form-check-label {{ $errors->has('tipe') ? ' is-invalid' : '' }}" for="Debet">Debet</label>
      </div>
      <div class="form-check form-check-inline">
        <input class="form-check-input {{ $errors->has('tipe') ? ' is-invalid' : '' }}" type="radio" name="tipe" id="Kredit" value="Kredit" {{ old('tipe') == 'Kredit' ? 'checked' : '' }}>
        <label class="form-check-label {{ $errors->has('tipe') ? ' is-invalid' : '' }}" for="Kredit">Kredit</label>
      </div>
      @if ($errors->has('tipe'))
          <span class="invalid-feedback" role="alert">
              <strong>{{ $errors->first('tipe') }}</strong>
          </span>
      @endif
      <br>
      <br>

      <label>Saldo Awal</label>
      <input type="number" step=".01" class="form-control {{ $errors->has('saldo_awal') ? ' is-invalid' : '' }}" value="{{ old('saldo_awal', 0) }}" name="saldo_awal" placeholder="Saldo Awal Kode Rekening">
      @if ($errors->has('saldo_awal'))
          <span class="invalid-feedback" role="alert">
              <strong>{{ $errors->first('saldo_awal') }}</strong>
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
