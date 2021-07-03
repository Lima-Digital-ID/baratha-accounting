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
    <form action="{{ route('barang.update', $barang->kode_barang) }}" method="POST">
      @csrf
      @method('PUT')
      <label>Kode Barang</label>
      <input type="text" id="kode_barang" class="form-control {{ $errors->has('kode_barang') ? ' is-invalid' : '' }}" value="{{ old('kode_barang', $barang->kode_barang) }}" autofocus name="kode_barang" placeholder="Kode Barang" readonly>
      @if ($errors->has('kode_barang'))
          <span class="invalid-feedback" role="alert">
              <strong>{{ $errors->first('kode_barang') }}</strong>
          </span>
      @endif

      <br>

      <label>Nama Barang</label>
      <input type="text" class="form-control {{ $errors->has('nama') ? ' is-invalid' : '' }}" value="{{ old('nama', $barang->nama) }}" name="nama" placeholder="Nama Barang">
      @if ($errors->has('nama'))
          <span class="invalid-feedback" role="alert">
              <strong>{{ $errors->first('nama') }}</strong>
          </span>
      @endif

      <br>

      <label>Satuan</label>
      <input type="text" class="form-control {{ $errors->has('satuan') ? ' is-invalid' : '' }}" value="{{ old('satuan', $barang->satuan) }}" name="satuan" placeholder="Satuan">
      @if ($errors->has('satuan'))
          <span class="invalid-feedback" role="alert">
              <strong>{{ $errors->first('satuan') }}</strong>
          </span>
      @endif

      <br>

      <label>Stock Awal</label>
      <input type="numer" step=".01" class="form-control {{ $errors->has('stock_awal') ? ' is-invalid' : '' }}" value="{{ old('stock_awal', $barang->stock_awal) }}" name="stock_awal" placeholder="Stock Awal">
      @if ($errors->has('stock_awal'))
          <span class="invalid-feedback" role="alert">
              <strong>{{ $errors->first('stock_awal') }}</strong>
          </span>
      @endif

      <br>
      
      <label>Saldo Awal</label>
      <input type="numer" step=".01" class="form-control {{ $errors->has('saldo_awal') ? ' is-invalid' : '' }}" value="{{ old('saldo_awal', $barang->saldo_awal) }}" name="saldo_awal" placeholder="Saldo Awal">
      @if ($errors->has('saldo_awal'))
          <span class="invalid-feedback" role="alert">
              <strong>{{ $errors->first('saldo_awal') }}</strong>
          </span>
      @endif

      <br>

      <label>Expired Date</label>
      <input type="text" class="form-control datepickerDate {{ $errors->has('exp_date') ? ' is-invalid' : '' }}" value="{{ old('exp_date', $barang->exp_date) }}" name="exp_date" placeholder="Expired Date">
      @if ($errors->has('exp_date'))
          <span class="invalid-feedback" role="alert">
              <strong>{{ $errors->first('exp_date') }}</strong>
          </span>
      @endif

      <br>

      <label>Keterangan</label>
      <input type="text" class="form-control {{ $errors->has('keterangan') ? ' is-invalid' : '' }}" value="{{ old('keterangan', str_replace('-', ' ', $barang->keterangan)) }}" name="keterangan" placeholder="Keterangan">
      @if ($errors->has('keterangan'))
          <span class="invalid-feedback" role="alert">
              <strong>{{ $errors->first('keterangan') }}</strong>
          </span>
      @endif

      <br>

      <label>Tempat Penyimpanan</label>
      <input type="text" class="form-control {{ $errors->has('tempat_penyimpanan') ? ' is-invalid' : '' }}" value="{{ old('tempat_penyimpanan', $barang->tempat_penyimpanan) }}" name="tempat_penyimpanan" placeholder="Nama Barang">
      @if ($errors->has('tempat_penyimpanan'))
          <span class="invalid-feedback" role="alert">
              <strong>{{ $errors->first('tempat_penyimpanan') }}</strong>
          </span>
      @endif

      <br>

      <label>Minimum Stock</label>
      <input type="text" class="form-control {{ $errors->has('minimum_stock') ? ' is-invalid' : '' }}" value="{{ old('minimum_stock', $barang->minimum_stock) }}" autofocus name="minimum_stock" placeholder="Minimum Stock">
      @if ($errors->has('minimum_stock'))
          <span class="invalid-feedback" role="alert">
              <strong>{{ $errors->first('minimum_stock') }}</strong>
          </span>
      @endif
      
      <br>

      <label>Kategori</label>
      <select name="id_kategori" id="id_kategori" class="form-control select2 {{ $errors->has('id_kategori') ? ' is-invalid' : '' }}">
        <option value="">--Pilih Kategori--</option>
        @foreach ($kategoriBarang as $item)
            <option value="{{$item->id}}" {{old('id_kategori', $barang->id_kategori) == $item->id ? 'selected' : ''}} >{{$item->id . ' -- '. $item->nama}}</option>
            
        @endforeach
      </select>
      @if ($errors->has('id_kategori'))
          <span class="invalid-feedback" role="alert">
              <strong>{{ $errors->first('id_kategori') }}</strong>
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
