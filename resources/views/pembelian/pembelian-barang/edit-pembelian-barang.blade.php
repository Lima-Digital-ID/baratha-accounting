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
        <form action="{{ route('pembelian-barang.update', $pembelian->kode_pembelian) }}" method="POST">
            @csrf
            @method('PUT')
            <div class="idDelete">
            </div>
            <h6 class="heading-small text-muted mb-3">Informasi Umum</h6>
            <div class="row">
                <div class="col-md-4">
                    <label>Kode Pembelian</label>
                    <input type="text" id="kode" class="form-control {{ $errors->has('kode_pembelian') ? ' is-invalid' : '' }}" value="{{ old('kode_pembelian', $pembelian->kode_pembelian) }}" name="kode_pembelian" placeholder="Kode Pembelian" readonly>
                    @if ($errors->has('kode_pembelian'))
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $errors->first('kode_pembelian') }}</strong>
                        </span>
                    @endif

                    <br>
                </div>

                <div class="col-md-4">
                    <label>Tanggal</label>
                    <input type="text" class="form-control getKode datepicker {{ $errors->has('tanggal') ? ' is-invalid' : '' }}" value="{{ old('tanggal', $pembelian->tanggal) }}" name="tanggal" placeholder="Tanggal" data-url="{{url('pembelian/pembelian-barang/getKode')}}" autocomplete="off">
                    @if ($errors->has('tanggal'))
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $errors->first('tanggal') }}</strong>
                        </span>
                    @endif

                    <br>
                </div>

                <div class="col-md-4">
                    <label>Supplier</label>
                    <select name="kode_supplier" class="form-control select2 {{ $errors->has('kode_supplier') ? ' is-invalid' : '' }}">
                        <option value="">--Pilih Supplier--</option>
                        @foreach ($supplier as $item)
                            <option value="{{$item->kode_supplier}}" {{old('kode_supplier', $pembelian->kode_supplier) == $item->kode_supplier ? 'selected' : ''}} >{{$item->kode_supplier . ' -- '. $item->nama}}</option>
                            
                        @endforeach
                    </select>
                    @if ($errors->has('kode_supplier'))
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $errors->first('kode_supplier') }}</strong>
                        </span>
                    @endif

                    <br>
                </div>
                
                <div class="col-md-4">
                    <label>Statun PPN</label>
                    <select name="status_ppn" id="status_ppn" class="form-control {{ $errors->has('status_ppn') ? ' is-invalid' : '' }}" disabled>
                        <option value="Tanpa" {{old('status_ppn', $pembelian->status_ppn) == 'Tanpa' ? 'selected' : ''}} >Tanpa</option>
                        <option value="Belum" {{old('status_ppn', $pembelian->status_ppn) == 'Belum' ? 'selected' : ''}} >Belum</option>
                        <option value="Sudah" {{old('status_ppn', $pembelian->status_ppn) == 'Sudah' ? 'selected' : ''}} >Sudah</option>
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
                    <input type="text" class="form-control datepicker {{ $errors->has('jatuh_tempo') ? ' is-invalid' : '' }}" value="{{ old('jatuh_tempo', $pembelian->jatuh_tempo) }}" name="jatuh_tempo" placeholder="Jatuh Tempoo" autocomplete="off">
                    @if ($errors->has('jatuh_tempo'))
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $errors->first('jatuh_tempo') }}</strong>
                        </span>
                    @endif

                    <br>
                </div>

            </div>

            <hr class="my-2">
            <h6 class="heading-small text-muted mb-3">Detail Pembelian</h6>

            <div class="pl-lg-4" id='urlAddDetail' data-url="{{url('pembelian/pembelian-barang/addEditDetailPembelian')}}">
              @if(!is_null(old('kode_barang')))
                @php
                  $loop = array();
                  foreach(old('kode_barang') as $i => $val){
                    $loop[] = array(
                      'kode_barang' => old('kode_barang.'.$i),
                      'qty' => (float)old('qty.'.$i),
                      'harga_satuan' => (float)old('harga_satuan.'.$i),
                      'subtotal' => (float)old('subtotal.'.$i),
                      'ppn' => (float)old('ppn.'.$i),
                    );
                  }
                @endphp
              @else
                @php
                    $loop = $detailPembelian;
                @endphp
              @endif

              @php $no = 0; $total = 0; $totalPpn = 0; $grandtotal = 0; @endphp
              @foreach($loop as $n => $edit)
                @php 
                  $no++;
                  $linkHapus = $no==1 ? false : true; 
                  $harga = 0;
                  $fields = array(
                      'kode_barang' => 'kode_barang.'.$n,
                      'qty' => 'qty.'.$n,
                      'harga_satuan' => 'harga_satuan.'.$n,
                      'subtotal' => 'subtotal.'.$n,
                      'ppn' => 'ppn.'.$n,
                  );
                  
                  if(!is_null(old('kode_barang'))){
                      $total = $total + $edit['subtotal'];
                      $totalPpn = $totalPpn + $edit['ppn'];
                      $idDetail = old('id_detail.'.$n);
                  }
                  else{
                      $total = $total + $edit['subtotal'];
                      $totalPpn = $totalPpn + $edit['ppn'];
                      $harga = $edit['harga_satuan'];
                      $idDetail = $edit['id'];
                  }
                @endphp
                  @include('pembelian.pembelian-barang.edit-detail-pembelian-barang',['hapus' => $linkHapus, 'no' => $no, 'barang' => $barang])
              @endforeach
              @php
                  $grandtotal = $total + $totalPpn;
              @endphp
            </div>
            <h5 class='text-right mt-5 pr-5'>Total : <span id='total' class="text-orange">{{number_format($total,0,',','.')}}</span></h5>
            <h5 class='text-right mt-1 pr-5'>Total Qty : <span id='totalQty' class="text-orange">{{$pembelian->total_qty}}</span></h5>
            <h5 class='text-right mt-1 pr-5'>Total PPN : <span id='totalPpn' class="text-orange">{{number_format($totalPpn,0,',','.')}}</span></h5>
            <h5 class='text-right mt-1 pr-5'>Grandtotal : <span id='grandtotal' class="text-orange">{{number_format($grandtotal,0,',','.')}}</span></h5>
            <div class="mt-4">

            <button type="reset" class="btn btn-default"> <span class="fa fa-times"></span> Cancel</button>
            &nbsp;
            <button type="submit" class="btn btn-primary"> <span class="fa fa-save"></span> Save</button>
        </form>
    </div>
</div>
@endsection
