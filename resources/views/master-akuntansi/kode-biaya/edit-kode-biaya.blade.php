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
    <form action="{{ route('kode-biaya.update', $kodeBiaya->kode_biaya) }}" method="POST">
      @csrf
      @method('PUT')
      <label>Kode Biaya</label>
      <input type="text" id="kode_biaya" class="form-control {{ $errors->has('kode_biaya') ? ' is-invalid' : '' }}" value="{{ old('kode_biaya', $kodeBiaya->kode_biaya) }}" autofocus name="kode_biaya" placeholder="Kode Biaya" readonly>
      @if ($errors->has('kode_biaya'))
          <span class="invalid-feedback" role="alert">
              <strong>{{ $errors->first('kode_biaya') }}</strong>
          </span>
      @endif

      <br>

      <label>Nama</label>
      <input type="text" class="form-control {{ $errors->has('nama') ? ' is-invalid' : '' }}" value="{{ old('nama', $kodeBiaya->nama) }}" name="nama" placeholder="Nama Kode Biaya">
      @if ($errors->has('nama'))
          <span class="invalid-feedback" role="alert">
              <strong>{{ $errors->first('nama') }}</strong>
          </span>
      @endif

      <br>

      <label>Kode Rekening</label>
      <select name="kode_rekening" id="kode_rekening" class="form-control select2 {{ $errors->has('kode_rekening') ? ' is-invalid' : '' }}">
        <option value="">--Pilih Kode Rekening--</option>
        @foreach ($kodeRekening as $item)
            <option value="{{$item->kode_rekening}}" {{old('kode_rekening', $kodeBiaya->kode_rekening) == $item->kode_rekening ? 'selected' : ''}} >{{$item->kode_rekening . ' -- '. $item->nama}}</option>
            
        @endforeach
      </select>
      @if ($errors->has('kode_rekening'))
          <span class="invalid-feedback" role="alert">
              <strong>{{ $errors->first('kode_rekening') }}</strong>
          </span>
      @endif

      <br><br>

      <button type="reset" class="btn btn-default"> <span class="fa fa-times"></span> Cancel</button>
      &nbsp;
      <button type="submit" class="btn btn-primary"> <span class="fa fa-save"></span> Save</button>
    </form>
  </div>
</div>
@endsection
