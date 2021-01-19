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
    <form action="{{ route('supplier.update', $supplier->kode_supplier) }}" method="POST">
      @csrf
      @method('put')
      <label>Kode Supplier</label>
      <input type="text" class="form-control {{ $errors->has('kode_supplier') ? ' is-invalid' : '' }}" value="{{ old('kode_supplier', $supplier->kode_supplier) }}" autofocus name="kode_supplier" placeholder="ex : Supplier 1">
      @if ($errors->has('kode_supplier'))
          <span class="invalid-feedback" role="alert">
              <strong>{{ $errors->first('kode_supplier') }}</strong>
          </span>
      @endif

      <br>

      <label>Nama</label>
      <input type="text" class="form-control {{ $errors->has('nama') ? ' is-invalid' : '' }}" value="{{ old('nama', $supplier->nama) }}" autofocus name="nama" placeholder="ex : Anthony Davis">
      @if ($errors->has('nama'))
          <span class="invalid-feedback" role="alert">
              <strong>{{ $errors->first('nama') }}</strong>
          </span>
      @endif

      <br>

      <label>Alamat</label>
      <textarea class="form-control" name="alamat" id="alamat" cols="30" rows="5">{{old('alamat', $supplier->alamat)}}</textarea>
      @if ($errors->has('alamat'))
          <span class="invalid-feedback" role="alert">
              <strong>{{ $errors->first('alamat') }}</strong>
          </span>
      @endif

      <br>

      <label>No Hp</label>
      <input type="text" class="form-control {{ $errors->has('no_hp') ? ' is-invalid' : '' }}" value="{{ old('no_hp', $supplier->no_hp) }}" autofocus name="no_hp" placeholder="ex : 085331xxxxxx">
      @if ($errors->has('no_hp'))
          <span class="invalid-feedback" role="alert">
              <strong>{{ $errors->first('no_hp') }}</strong>
          </span>
      @endif

      <br>

      <label>Hutang</label>
      <input type="text" class="form-control {{ $errors->has('hutang') ? ' is-invalid' : '' }}" value="{{ old('hutang', $supplier->hutang) }}" autofocus name="hutang" placeholder="ex : 1.000.000,00">
      @if ($errors->has('hutang'))
          <span class="invalid-feedback" role="alert">
              <strong>{{ $errors->first('hutang') }}</strong>
          </span>
      @endif

      <br>

      <button type="reset" class="btn btn-default"> <span class="fa fa-times"></span> Cancel</button>

      <button type="submit" class="btn btn-primary"> <span class="fa fa-save"></span> Save</button>
    </form>
  </div>
</div>
@endsection
