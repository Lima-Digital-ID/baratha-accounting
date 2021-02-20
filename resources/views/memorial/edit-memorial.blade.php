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
        <form action="{{ route('memorial.update', $memorial->kode_memorial) }}" method="POST">
            @csrf
            @method('PUT')
            <div class="idDelete">
            </div>
            <h6 class="heading-small text-muted mb-3">Informasi Umum</h6>
            <div class="row">
                <div class="col-md-4">
                    <label>Kode  Memorial</label>
                    <input type="text" id="kode" class="form-control {{ $errors->has('kode_memorial') ? ' is-invalid' : '' }}" value="{{ old('kode_memorial', $memorial->kode_memorial) }}" name="kode_memorial" placeholder="Kode  Memorial" readonly>
                    @if ($errors->has('kode_memorial'))
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $errors->first('kode_memorial') }}</strong>
                        </span>
                    @endif

                    <br>
                </div>

                <div class="col-md-4">
                    <label>Tanggal</label>
                    <input type="text" class="form-control datepicker {{ $errors->has('tanggal') ? ' is-invalid' : '' }}" value="{{ old('tanggal', $memorial->tanggal) }}" name="tanggal" placeholder="Tanggal" autocomplete="off" id="tanggal">
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
                        <option value="Masuk" {{old('tipe', $memorial->tipe) == 'Masuk' ? 'selected' : ''}} >Masuk</option>
                        <option value="Keluar" {{old('tipe', $memorial->tipe) == 'Keluar' ? 'selected' : ''}}>Keluar</option>
                    </select>
                    @error('tipe')
                        <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                    <br>
                </div>

                <div class="col-md-4 mb-3">
                    <label for="" class="form-control-label">Supplier</label>
                    <select name="kode_supplier" class="form-control select2 @error('kode_supplier') is-invalid @enderror" id="kode_supplier" disabled>
                        <option value="">--Pilih Supplier--</option>
                        @foreach ($supplier as $item)
                            <option value="{{$item->kode_supplier}}" {{old('kode_supplier', $memorial->kode_supplier) == $item->kode_supplier ? 'selected' : ''}} >{{$item->kode_supplier . ' ~ '.$item->nama}}</option>
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
                    <select name="kode_customer" class="form-control select2 @error('kode_customer') is-invalid @enderror" id="kode_customer" disabled>
                        <option value="">--Pilih Customer--</option>
                        @foreach ($customer as $item)
                            <option value="{{$item->kode_customer}}" {{old('kode_customer', $memorial->kode_customer) == $item->kode_customer ? 'selected' : ''}} >{{$item->kode_customer . ' ~ '.$item->nama}}</option>
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

            <ul class="nav nav-tabs mt-4">
                <li class="nav-item">
                    <a class="nav-link <?= empty($_GET['page']) ? "active" : ""   ?>" href="edit">Detail Transaksi Memorial</a>
                </li>
                <?php 
                    if($memorial->kode_supplier!='' || $memorial->kode_customer!=''){
                ?>
                <li class="nav-item">
                    <a class="nav-link <?= isset($_GET['page']) ? "active" : ""   ?>" href="edit?page=hutang-piutang">Pembayaran Hutang / Piutang</a>
                </li>
                <?php } ?>
            </ul>
            <div class="tab-content mb-4">
                <div class="tab-pane <?= empty($_GET['page']) ? "active" : ""   ?>">
                    <div class="body-tab-content">
                        <div class="px-4" id='urlAddDetail' data-url="{{url('memorial/memorial/addEditDetailMemorial')}}">
                            @if(!is_null(old('lawan')))
                                @php
                                $loop = array();
                                foreach(old('lawan') as $i => $val){
                                    $loop[] = array(
                                    'kode' => old('kode.'.$i),
                                    'lawan' => old('lawan.'.$i),
                                    'subtotal' => (float)old('subtotal.'.$i),
                                    'keterangan' => old('keterangan.'.$i),
                                    );
                                }
                                @endphp
                            @else
                                @php
                                    $loop = $detailMemorial;
                                @endphp
                            @endif

                            @php $no = 0; $total = 0; @endphp
                            @foreach($loop as $n => $edit)
                                @php 
                                $no++;
                                $linkHapus = $no==1 ? false : true; 
                                $harga = 0;
                                $fields = array(
                                    'kode' => 'kode.'.$n,
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
                                @include('memorial.edit-detail-memorial',['hapus' => $linkHapus, 'no' => $no, 'kodeRekening' => $kodeRekening])
                            @endforeach
                            @php
                                // $total = $total;
                            @endphp
                        </div>
                        <h5 class='text-right mt-1 pr-5'>Total : {{number_format($total,0,',','.')}}<span id='total' class="text-orange">0</span></h5>
                    </div>
                    <div class="mt-4">
                    <button type="reset" class="btn btn-default"> <span class="fa fa-times"></span> Cancel</button>
                        &nbsp;
                        <button type="submit" class="btn btn-primary"> <span class="fa fa-save"></span> Save</button>
                    </div>
                    </form>
                </div>
                <?php 
                    if(isset($_GET['page'])){
                ?>
                <div class="tab-pane active">
                    <?php
                        if($memorial->kode_supplier!=''){    
                    ?>
                        @include('pembelian.supplier.list-hutang',['hutang' => $hutang, 'sisaDetail' => $total- $totalBayar->total, 'kode_transaksi' => $memorial->kode_memorial,'kode_supplier' => $memorial->kode_supplier])
                    <?php } else{?>
                        @include('penjualan.customer.list-piutang',['piutang' => $piutang, 'sisaDetail' => $total- $totalBayar->total, 'kode_transaksi' => $memorial->kode_memorial,'kode_customer' => $memorial->kode_customer])
                    <?php }  ?>
                        <h5 class='text-right mt-3 pr-5'>Sisa Saldo Detail: <span id='total' class="text-orange">{{number_format($total- $totalBayar->total,0,',','.')}}</span></h5>
                </div>
                <?php } ?>
            </div>
    </div>
</div>
@endsection
