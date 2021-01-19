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
    <form action="{{ route('kategori-barang.update', $kategoriBarang->id) }}" method="POST">
      @csrf
      @method('PUT')
      <label>Nama Kategori</label>
      <input type="text" class="form-control {{ $errors->has('nama') ? ' is-invalid' : '' }}" value="{{ old('nama', $kategoriBarang->nama) }}" autofocus name="nama" placeholder="Nama Kategori Barang">
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
