@extends('common.template')
@section('container')
<div class="alert alert-success custom-alert">
    <span class="fa fa-exclamation-triangle sticky"></span>
    <label>Selamat datang di Aplikasi Baratha Accounting</label>
    <br>
    <label class="font-weight-normal">{{date('d-M-Y H:m:s')}}</label>
</div>
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
