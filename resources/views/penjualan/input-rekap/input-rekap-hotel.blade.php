@extends('common.template')
@section('container')
<div class="row">
    <div class="col-md-4">
        <div class="card shadow py-2">
            <div class="card-body">
                <form action="" method="" id="rekap-hotel">
                    <label for="">Tanggal</label>    
                    <div class="input-group mb-3">
                        <div class="input-group-prepend">
                            <span class="input-group-text" id="basic-addon1"><span class="fa fa-calendar"></span></span>
                        </div>                
                        <input type="text" class="form-control datepickerDate" value="{{ isset($_GET['tanggal']) ? $_GET['tanggal'] : '' }}" name="tanggal" placeholder="Pilih Tanggal" autocomplete="off" required>
                    </div>
                    <button type="submit" class="btn btn-primary"> <span class="fa fa-filter"></span> Filter</button>
                </form>
            </div>
        </div>
    </div>
</div>
@if (session('error'))
    <div class="alert alert-danger alert-dismissible mt-3 fade show" role="alert">
        {{ session('error') }}
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
@endif
@if (isset($_GET['tanggal']))
    @if(isset($json))
        <div class="table-responsive">
            <table class="table table-custom">
                <thead>
                    <tr>
                        <td>#</td>
                        <td>Tanggal</td>
                        <td>Total</td>
                        <td>Total PPN</td>
                        <td>Opsi</td>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>1</td>
                        <td>{{date('d-m-Y',strtotime($_GET['tanggal']))}}</td>
                        <td>{{number_format($json['total'],0,',','.')}}</td>
                        <td>{{number_format($json['total_ppn'],0,',','.')}}</td>
                        <td><a href="{{url('penjualan/rekap-hotel/save?tanggal='.$_GET['tanggal'])}}" data-alert='Akan dilakukan penarikan data'  class="confirm-alert btn btn-default"><span class="fa fa-download"></span> Tarik Data</a></td>
                    </tr>
                </tbody>
            </table>
        </div>
    @else
    <div class="alert alert-success font-weight-bold mt-3">
        Rekap pada tanggal {{date('d-m-Y',strtotime($_GET['tanggal']))}} sudah dilakukan.
    </div>
    @endif

@endif
@endsection