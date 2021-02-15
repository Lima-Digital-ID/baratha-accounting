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
        <form action="{{ route('transaksi-kas.store') }}" method="POST">
            @csrf
            <h6 class="heading-small text-muted mb-3">Informasi Umum</h6>
            <div class="row">
                <div class="col-md-4">
                    <label>Kode Transaksi Kas</label>
                    <input type="text" id="kode" class="form-control {{ $errors->has('kode_kas') ? ' is-invalid' : '' }}" value="{{ old('kode_kas') }}" name="kode_kas" placeholder="Kode Transaksi Kas" readonly>
                    @if ($errors->has('kode_kas'))
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $errors->first('kode_kas') }}</strong>
                        </span>
                    @endif

                    <br>
                </div>

                <div class="col-md-4">
                    <label>Tanggal</label>
                    <input type="text" class="form-control getKodeKas datepicker {{ $errors->has('tanggal') ? ' is-invalid' : '' }}" value="{{ old('tanggal') }}" name="tanggal" placeholder="Tanggal" data-url="{{url('kas/transaksi-kas/getKode')}}" autocomplete="off" id="tanggal">
                    @if ($errors->has('tanggal'))
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $errors->first('tanggal') }}</strong>
                        </span>
                    @endif

                    <br>
                </div>
                
                <div class="col-md-4">
                    <label for="" class="form-control-label">Tipe</label>
                    <select name="tipe" id="tipe" class="form-control getKodeKas @error('tipe') is-invalid @enderror" data-url="{{url('kas/transaksi-kas/getKode')}}">
                        <option value="">--Pilih Tipe--</option>
                        <option value="Masuk" {{old('tipe') == 'Masuk' ? 'selected' : ''}} >Masuk</option>
                        <option value="Keluar" {{old('tipe') == 'Keluar' ? 'selected' : ''}}>Keluar</option>
                    </select>
                    @error('tipe')
                        <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                    <br>
                </div>
                
                <div class="col-md-4 mb-3">
                    <label for="" class="form-control-label">Kode Rekening Kas</label>
                    <select name="kode_rekening" class="form-control select2 @error('kode_rekening') is-invalid @enderror">
                        <option value="">--Pilih Kode Rekening Kas--</option>
                        @foreach ($kodeRekeningKas as $item)
                            <option value="{{$item->kode_rekening}}" {{old('kode_rekening') == $item->kode_rekening ? 'selected' : ''}} >{{$item->kode_rekening . ' ~ '.$item->nama}}</option>
                        @endforeach
                    </select>
                    @error('kode_rekening')
                        <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                    <br>
                </div>

                <div class="col-md-4 mb-3">
                    <label for="" class="form-control-label">Supplier</label>
                    <select name="kode_supplier" class="form-control select2 @error('kode_supplier') is-invalid @enderror" id="kode_supplier" {{old('kode_supplier')=='' ? 'disabled' : ''}}>
                        <option value="">--Pilih Supplier--</option>
                        @foreach ($supplier as $item)
                            <option value="{{$item->kode_supplier}}" {{old('kode_supplier') == $item->kode_supplier ? 'selected' : ''}} >{{$item->kode_supplier . ' ~ '.$item->nama}}</option>
                        @endforeach
                    </select>
                    @error('kode_supplier')
                        <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                    <br>
                </div>
                
                <div class="col-md-4 mb-3">
                    <label for="" class="form-control-label">Customer</label>
                    <select name="kode_customer" class="form-control select2 @error('kode_customer') is-invalid @enderror" id="kode_customer" {{old('kode_customer')=='' ? 'disabled' : ''}}>
                        <option value="">--Pilih Customer--</option>
                        @foreach ($customer as $item)
                            <option value="{{$item->kode_customer}}" {{old('kode_customer') == $item->kode_customer ? 'selected' : ''}} >{{$item->kode_customer . ' ~ '.$item->nama}}</option>
                        @endforeach
                    </select>
                    @error('kode_customer')
                        <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                    <br>
                </div>

            </div>

            <hr class="my-2">
            <h6 class="heading-small text-muted mb-3">Detail Transaksi Kas</h6>

            <div class="pl-lg-4" id='urlAddDetail' data-url="{{url('kas/transaksi-kas/addDetailTransaksiKas')}}">
                @if(!is_null(old('lawan')))
                  @php $no = 0 @endphp
                  @foreach(old('lawan') as $n => $value)
                    @php $no++ @endphp
                    @include('kas.tambah-detail-transaksi-kas',['hapus' => false, 'no' => $no, 'lawan' => $lawan])
                  @endforeach
                @else
                  @include('kas.tambah-detail-transaksi-kas',['hapus' => false, 'no' => 1, 'lawan' => $lawan])
                @endif
            </div>
            
            <h5 class='text-right mt-1 pr-5'>Total : <span id='total' class="text-orange">0</span></h5>
            <div class="mt-4">

            <button type="reset" class="btn btn-default"> <span class="fa fa-times"></span> Cancel</button>
            &nbsp;
            <button type="submit" class="btn btn-primary"> <span class="fa fa-save"></span> Save</button>
        </form>
    </div>
</div>
@endsection
