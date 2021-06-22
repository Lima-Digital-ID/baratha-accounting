@extends('common.template')
@section('container')
<div class="alert alert-success custom-alert">
    <span class="fa fa-exclamation-triangle sticky"></span>
    <label>Selamat datang di Aplikasi Baratha Accounting</label>
    <br>
    <label class="font-weight-normal">{{date('d-M-Y H:m:s')}}</label>
</div>
@if (count($minim_stock) > 0)
<div class="alert alert-warning custom-alert">
    <span class="fa fa-exclamation-triangle sticky"></span>
    <label>Terdapat {{ count($minim_stock) }} barang yang telah mencapai batas minimal stok. <a href="{{url('persediaan/barang-minim')}}">Selengkapnya</a></label>
    <br>
</div>
@endif
@if (count($barang_will_expired) > 0)
<div class="alert alert-warning custom-alert">
    <span class="fa fa-exclamation-triangle sticky"></span>
    <label>Terdapat {{ count($barang_will_expired) }} barang yang hampir kadaluarsa.<a href="{{url('persediaan/barang-hampir-expired')}}">Selengkapnya</a></label>
    <br>
    @if (count($barang_expired) > 0)
    <label>Terdapat {{ count($barang_expired) }} barang yang telah kadaluarsa. <a href="{{url('persediaan/barang-expired')}}">Selengkapnya</a></label>
    <br>
    @endif
</div>
@endif
{{-- <div class="alert alert-success custom-alert">
    <span class="fa fa-exclamation-triangle sticky"></span>
    <label>Barang kadaluarsa</label>
    <br>
    <label class="font-weight-normal">{{date('d-M-Y H:m:s')}}</label>
    @php
        $data = \DB::table('barang')->get();
        $exp_date = strtotime($data[0]->exp_date);
        $today = strtotime(date('Y-m-d'));
        echo '<br>barang : '.$exp_date;
        echo '<br>skrg : '.$today;
        if($exp_date < $today) {
            // expired
            echo '<br>Kadaluarsa';
        }
        elseif($exp_date == $exp_date) {
            // expired now
            echo '<br>Kadaluarsa sekarang';
        }
        else {
            // not expired
            echo '<br>Belum kadaluarsa';
        }
    @endphp
</div> --}}
<div class="row">
    <div class="col-md-3 mb-4">
        <div class="card card-dashboard py-2">
            <div class="card-body">    
                <div class="row">
                    <div class="col-md-8 pr-0">
                        <h2 class="color-primary font-weight-bold">{{$kategori}}</h2>
                        Kategori Barang
                    </div>
                    <div class="col-md-4 pl-0 text-center">
                        <span class="fas fa-fw fa-box-open fa-4x"></span>
                    </div>
                </div>
                <hr>
                <a href="{{url('persediaan/kategori-barang')}}">Lihat Detail</a>
            </div>
        </div>
    </div>
    <div class="col-md-3 mb-4">
        <div class="card card-dashboard py-2 has-notif">
            <div class="card-body">    
                <div class="row">
                    <div class="col-md-8 pr-0">
                        <h2 class="color-primary font-weight-bold">{{$barang}}</h2>
                        Master Barang
                    </div>
                    <div class="col-md-4 pl-0 text-center">
                        <span class="fas fa-fw fa-boxes fa-4x"></span>
                    </div>
                </div>
                <hr>
                <a href="{{url('persediaan/barang')}}">Lihat Detail</a>
            </div>
        </div>
    </div>
    <div class="col-md-3 mb-4">
        <div class="card card-dashboard py-2">
            <div class="card-body">    
                <div class="row">
                    <div class="col-md-8 pr-0">
                        <h2 class="color-primary font-weight-bold">{{$customer}}</h2>
                        Customer
                    </div>
                    <div class="col-md-4 pl-0 text-center">
                        <span class="fas fa-fw fa-user-tag fa-4x"></span>
                    </div>
                </div>
                <hr>
                <a href="{{url('penjualan/customer')}}">Lihat Detail</a>
            </div>
        </div>
    </div>
    <div class="col-md-3 mb-4">
        <div class="card card-dashboard py-2">
            <div class="card-body">    
                <div class="row">
                    <div class="col-md-8 pr-0">
                        <h2 class="color-primary font-weight-bold">{{$supplier}}</h2>
                        Supplier
                    </div>
                    <div class="col-md-4 pl-0 text-center">
                        <span class="fas fa-fw fa-user-secret fa-4x"></span>
                    </div>
                </div>
                <hr>
                <a href="{{url('pembelian/supplier')}}">Lihat Detail</a>
            </div>
        </div>
    </div>
</div>

@endsection
