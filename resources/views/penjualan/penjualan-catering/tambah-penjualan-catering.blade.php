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
        <form action="{{ route('penjualan-catering.store') }}" method="POST">
            @csrf
            <h6 class="heading-small text-muted mb-3">Informasi Umum</h6>
            <div class="row">
                <div class="col-md-4">
                    <label>Kode Penjualan</label>
                    <input type="text" id="kode" class="form-control {{ $errors->has('kode_penjualan') ? ' is-invalid' : '' }}" value="{{ old('kode_penjualan') }}" name="kode_penjualan" placeholder="Kode Penjualan" readonly>
                    @if ($errors->has('kode_penjualan'))
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $errors->first('kode_penjualan') }}</strong>
                        </span>
                    @endif

                    <br>
                </div>

                <div class="col-md-4">
                    <label>Tanggal</label>
                    <input type="text" class="form-control getKode datepicker {{ $errors->has('tanggal') ? ' is-invalid' : '' }}" value="{{ old('tanggal') }}" name="tanggal" placeholder="Tanggal" data-url="{{url('penjualan/penjualan-catering/getKode')}}" autocomplete="off">
                    @if ($errors->has('tanggal'))
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $errors->first('tanggal') }}</strong>
                        </span>
                    @endif

                    <br>
                </div>

                <div class="col-md-4">
                    <label>Customer</label>
                    <select name="kode_customer" class="form-control select2 {{ $errors->has('kode_customer') ? ' is-invalid' : '' }}">
                        <option value="">--Pilih Customer--</option>
                        @foreach ($customer as $item)
                            <option value="{{$item->kode_customer}}" {{old('kode_customer') == $item->kode_customer ? 'selected' : ''}} >{{$item->kode_customer . ' -- '. $item->nama}}</option>
                            
                        @endforeach
                    </select>
                    @if ($errors->has('kode_customer'))
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $errors->first('kode_customer') }}</strong>
                        </span>
                    @endif

                    <br>
                </div>
                
                <div class="col-md-4">
                    <label>Statun PPN</label>
                    <select name="status_ppn" id="status_ppn" class="form-control {{ $errors->has('status_ppn') ? ' is-invalid' : '' }}">
                        <option value="Tanpa" {{old('status_ppn') == 'Tanpa' ? 'selected' : ''}} >Tanpa</option>
                        <option value="Belum" {{old('status_ppn') == 'Belum' ? 'selected' : ''}} >Belum</option>
                        <option value="Sudah" {{old('status_ppn') == 'Sudah' ? 'selected' : ''}} >Sudah</option>
                    </select>
                    @if ($errors->has('status_ppn'))
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $errors->first('status_ppn') }}</strong>
                        </span>
                    @endif

                    <br>
                </div>

                <div class="col-md-4">
                    <label>Jatuh Tempo</label>
                    <input type="text" class="form-control datepicker {{ $errors->has('jatuh_tempo') ? ' is-invalid' : '' }}" value="{{ old('jatuh_tempo') }}" name="jatuh_tempo" placeholder="Jatuh Tempoo" autocomplete="off">
                    @if ($errors->has('jatuh_tempo'))
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $errors->first('jatuh_tempo') }}</strong>
                        </span>
                    @endif

                    <br>
                </div>

            </div>
            <hr>
            <div class="row">
                <div class="col-md-4">
                    <label>Quantity</label>
                    <input type="number" step=".01" class="form-control getTotalCatering getTotalQty {{ $errors->has('qty') ? ' is-invalid' : '' }}" value="{{ old('qty') }}" name="qty" placeholder="Quantity" data-other='#harga_satuan' id='qty'>
                    @if ($errors->has('qty'))
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $errors->first('qty') }}</strong>
                        </span>
                    @endif
                    <br>
                </div>

                <div class="col-md-4">
                    <label>Harga Satuan</label>
                    <input type="number" step=".01" class="form-control getTotalCatering {{ $errors->has('harga_satuan') ? ' is-invalid' : '' }}" value="{{ old('harga_satuan') }}" name="harga_satuan" placeholder="Harga Satuan" data-other='#qty' id='harga_satuan'>
                    @if ($errors->has('harga_satuan'))
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $errors->first('harga_satuan') }}</strong>
                        </span>
                    @endif
                    <br>
                </div>
                
                <div class="col-md-4">
                    <label>Keterangan</label>
                    <input type="text" class="form-control {{ $errors->has('keterangan') ? ' is-invalid' : '' }}" value="{{ old('keterangan') }}" name="keterangan" placeholder="keterangan">
                    @if ($errors->has('keterangan'))
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $errors->first('keterangan') }}</strong>
                        </span>
                    @endif
                    <br>
                </div>
            </div>
            <div class="row">
                <div class="col-md-3">
                    <button type="reset" class="btn btn-default"> <span class="fa fa-times"></span> Cancel</button>
                    &nbsp;
                    <button type="submit" class="btn btn-primary"> <span class="fa fa-save"></span> Save</button>
                </div>
                <div class="col-md-6 ml-auto">
                    <h5 class='text-right pr-5'>Total : <span id='total' class="text-orange">0</span></h5>
                    <h5 class='text-right mt-1 pr-5'>Total Qty : <span id='totalQty' class="text-orange">0</span></h5>
                    <h5 class='text-right mt-1 pr-5'>Total PPN : <span id='totalPpn' class="text-orange">0</span></h5>
                    <h5 class='text-right mt-1 pr-5'>Grandtotal : <span id='grandtotal' class="text-orange">0</span></h5>
                    <div class="mt-4">
                </div>
            </div>

            
        </form>
    </div>
</div>
@endsection
