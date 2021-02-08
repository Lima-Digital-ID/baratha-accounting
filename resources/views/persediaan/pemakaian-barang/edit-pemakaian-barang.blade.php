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
        <form action="{{ route('pemakaian-barang.update', $pemakaian->kode_pemakaian) }}" method="POST">
            @csrf
            @method('PUT')
            <div class="idDelete">
            </div>
            <h6 class="heading-small text-muted mb-3">Informasi Umum</h6>
            <div class="row">
                <div class="col-md-4">
                    <label>Kode Pemakaian</label>
                    <input type="text" id="kode" class="form-control {{ $errors->has('kode_pemakaian') ? ' is-invalid' : '' }}" value="{{ old('kode_pemakaian', $pemakaian->kode_pemakaian) }}" name="kode_pemakaian" placeholder="Kode Pemakaian" readonly>
                    @if ($errors->has('kode_pemakaian'))
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $errors->first('kode_pemakaian') }}</strong>
                        </span>
                    @endif

                    <br>
                </div>

                <div class="col-md-4">
                    <label>Tanggal</label>
                    <input type="text" class="form-control datepicker {{ $errors->has('tanggal') ? ' is-invalid' : '' }}" value="{{ old('tanggal', $pemakaian->tanggal) }}" name="tanggal" placeholder="Tanggal" autocomplete="off">
                    @if ($errors->has('tanggal'))
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $errors->first('tanggal') }}</strong>
                        </span>
                    @endif

                    <br>
                </div>

            </div>

            <hr class="my-2">
            <h6 class="heading-small text-muted mb-3">Detail Pemakaian</h6>

            <div class="pl-lg-4" id='urlAddDetail' data-url="{{url('persediaan/pemakaian-barang/addEditDetailPemakaian')}}">
              @if(!is_null(old('kode_barang')))
                @php
                  $loop = array();
                  foreach(old('kode_barang') as $i => $val){
                    $loop[] = array(
                      'kode_barang' => old('kode_barang.'.$i),
                      'stock' => old('stock.'.$i),
                      'saldo' => old('saldo.'.$i),
                      'qty' => old('qty.'.$i),
                      'kode_biaya' => old('kode_biaya.'.$i),
                      'keterangan' => old('keterangan.'.$i),
                    );
                  }
                @endphp
              @else
                @php
                    $loop = $detailPemakaian;
                @endphp
              @endif

              @php $no = 0; $total = 0; @endphp
              @foreach($loop as $n => $edit)
                @php 
                    $edit = (array)$edit;
                  $no++;
                  $linkHapus = $no==1 ? false : true;
                  $fields = array(
                    'kode_barang' => 'kode_barang.'.$n,
                    'stock' => 'stock.'.$n,
                    'saldo' => 'saldo.'.$n,
                    'qty' => 'qty.'.$n,
                    'keterangan' => 'keterangan.'.$n,
                    'kode_biaya' => 'kode_biaya.'.$n,
                  );
                  if(!is_null(old('kode_barang'))){
                      $total = $total + $edit['qty'];
                      $idDetail = old('id_detail.'.$n);
                  }
                  else{
                      $total = $total + $edit['qty'];
                      $idDetail = $edit['id'];
                  }
                @endphp
                  @include('persediaan.pemakaian-barang.edit-detail-pemakaian-barang',['hapus' => $linkHapus, 'no' => $no, 'barang' => $barang])
              @endforeach
              </div>
            
            <h5 class='text-right mt-1 pr-5'>Total Qty : <span id='totalQty' class="text-orange">0</span></h5>
            <div class="mt-4">

            <button type="reset" class="btn btn-default"> <span class="fa fa-times"></span> Cancel</button>
            &nbsp;
            <button type="submit" class="btn btn-primary"> <span class="fa fa-save"></span> Save</button>
        </form>
    </div>
</div>
@endsection
