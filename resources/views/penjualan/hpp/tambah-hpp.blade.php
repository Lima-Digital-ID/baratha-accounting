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
    <form action="{{ route('hpp.store') }}" method="POST">
      @csrf
      <label>Tanggal</label>
      <input type="text" autocomplete="off" class="form-control datepicker {{ $errors->has('tanggal') ? ' is-invalid' : '' }}" value="{{ old('tanggal') }}" name="tanggal" placeholder="Tanggal HPP">
      @if ($errors->has('tanggal'))
          <span class="invalid-feedback" role="alert">
              <strong>{{ $errors->first('tanggal') }}</strong>
          </span>
      @endif

      <br>

      <label>Nominal</label>
      <input type="number" autocomplete="off" step=".01" class="form-control {{ $errors->has('nominal_hpp') ? ' is-invalid' : '' }}" value="{{ old('nominal_hpp') }}" name="nominal_hpp" placeholder="Nominal HPP">
      @if ($errors->has('nominal_hpp'))
          <span class="invalid-feedback" role="alert">
              <strong>{{ $errors->first('nominal_hpp') }}</strong>
          </span>
      @endif

      <br>

      <label>Keterangan</label>
      <textarea class="form-control" name="keterangan" id="keterangan" cols="30" rows="5"></textarea>
      @if ($errors->has('keterangan'))
          <span class="invalid-feedback" role="alert">
              <strong>{{ $errors->first('keterangan') }}</strong>
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
