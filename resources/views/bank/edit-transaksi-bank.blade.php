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
        <form action="{{ route('transaksi-bank.update', $bank->kode_bank) }}" method="POST">
            @csrf
            @method('PUT')
            <div class="idDelete">
            </div>
            <h6 class="heading-small text-muted mb-3">Informasi Umum</h6>
            <div class="row">
                <div class="col-md-4">
                    <label>Kode Transaksi Bank</label>
                    <input type="text" id="kode" class="form-control {{ $errors->has('kode_bank') ? ' is-invalid' : '' }}" value="{{ old('kode_bank', $bank->kode_bank) }}" name="kode_bank" placeholder="Kode Transaksi Bank" readonly>
                    @if ($errors->has('kode_bank'))
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $errors->first('kode_bank') }}</strong>
                        </span>
                    @endif

                    <br>
                </div>

                <div class="col-md-4">
                    <label>Tanggal</label>
                    <input type="text" class="form-control datepicker {{ $errors->has('tanggal', $bank->tanggal) ? ' is-invalid' : '' }}" value="{{ old('tanggal', $bank->tanggal) }}" name="tanggal" placeholder="Tanggal" autocomplete="off" id="tanggal">
                    @if ($errors->has('tanggal'))
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $errors->first('tanggal') }}</strong>
                        </span>
                    @endif

                    <br>
                </div>
                
                <div class="col-md-4">
                    <label for="" class="form-control-label">Tipe</label>
                    <select name="tipe" id="tipe" class="form-control @error('tipe') is-invalid @enderror" disabled>
                        <option value="">--Pilih Tipe--</option>
                        <option value="Masuk" {{old('tipe', $bank->tipe, $bank->tipe) == 'Masuk' ? 'selected' : ''}} >Masuk</option>
                        <option value="Keluar" {{old('tipe', $bank->tipe, $bank->tipe) == 'Keluar' ? 'selected' : ''}}>Keluar</option>
                    </select>
                    @error('tipe')
                        <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                    <br>
                </div>
                
                <div class="col-md-4 mb-3">
                    <label for="" class="form-control-label">Kode Rekening Bank</label>
                    <select name="kode_rekening" class="form-control select2 @error('kode_rekening') is-invalid @enderror">
                        <option value="">--Pilih Kode Rekening Bank--</option>
                        @foreach ($kodeRekeningBank as $item)
                            <option value="{{$item->kode_rekening}}" {{old('kode_rekening', $bank->kode_rekening) == $item->kode_rekening ? 'selected' : ''}} >{{$item->kode_rekening . ' ~ '.$item->nama}}</option>
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
                    <select name="kode_supplier" class="form-control select2 @error('kode_supplier') is-invalid @enderror" id="kode_supplier">
                        <option value="">--Pilih Supplier--</option>
                        @foreach ($supplier as $item)
                            <option value="{{$item->kode_supplier}}" {{old('kode_supplier', $bank->kode_supplier) == $item->kode_supplier ? 'selected' : ''}} >{{$item->kode_supplier . ' ~ '.$item->nama}}</option>
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
                    <select name="kode_customer" class="form-control select2 @error('kode_customer') is-invalid @enderror" id="kode_customer">
                        <option value="">--Pilih Customer--</option>
                        @foreach ($customer as $item)
                            <option value="{{$item->kode_customer}}" {{old('kode_customer', $bank->kode_customer) == $item->kode_customer ? 'selected' : ''}} >{{$item->kode_customer . ' ~ '.$item->nama}}</option>
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
            <h6 class="heading-small text-muted mb-3">Detail Transaksi Bank</h6>

            <div class="pl-lg-4" id='urlAddDetail' data-url="{{url('bank/transaksi-bank/addEditDetailTransaksiBank')}}">
                @if(!is_null(old('lawan')))
                    @php
                    $loop = array();
                    foreach(old('lawan') as $i => $val){
                        $loop[] = array(
                        'lawan' => old('lawan.'.$i),
                        'subtotal' => (float)old('subtotal.'.$i),
                        'keterangan' => old('keterangan.'.$i),
                        );
                    }
                    @endphp
                @else
                    @php
                        $loop = $detailTransaksiBank;
                    @endphp
                @endif

                @php $no = 0; $total = 0; @endphp
                @foreach($loop as $n => $edit)
                    @php 
                    $no++;
                    $linkHapus = $no==1 ? false : true; 
                    $harga = 0;
                    $fields = array(
                        'lawan' => 'lawan.'.$n,
                        'subtotal' => 'subtotal.'.$n,
                        'keterangan' => 'keterangan.'.$n,
                    );
                    
                    if(!is_null(old('lawan'))){
                        $total = $total + $edit['subtotal'];
                        $idDetail = old('id_detail.'.$n);
                    }
                    else{
                        $total = $total + $edit['subtotal'];
                        $idDetail = $edit['id'];
                    }
                    @endphp
                    @include('bank.edit-detail-transaksi-bank',['hapus' => $linkHapus, 'no' => $no, 'lawan' => $lawan])
                @endforeach
                @php
                    // $total = $total;
                @endphp
            </div>
            <h5 class='text-right mt-5 pr-5'>Total : <span id='total' class="text-orange">{{number_format($total,0,',','.')}}</span></h5>

            <button type="reset" class="btn btn-default"> <span class="fa fa-times"></span> Cancel</button>
            &nbsp;
            <button type="submit" class="btn btn-primary"> <span class="fa fa-save"></span> Save</button>
        </form>
    </div>
</div>
@endsection
