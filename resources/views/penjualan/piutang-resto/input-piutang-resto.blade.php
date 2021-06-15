@extends('common.template')
@section('container')
@if (session('error'))
    <div class="alert alert-danger alert-dismissible mt-3 fade show" role="alert">
        {{ session('error') }}
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
@endif
@if (session('status'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        {{ session('status') }}
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
@endif
@if($piutang['status']!='Success' && $piutang['status']!='Kosong')
<div class="alert alert-danger">
    <b>Gagal Mengambil Data</b>
</div>
@else
@if($piutang['status']=='Kosong')
<div class="alert alert-info">
    <b>Data belum tersedia</b>
</div>
@endif
<div class="table-responsive">
    <table class="table table-custom">
        <thead>
            <tr>
                <td>#</td>
                <td>Tanggal</td>
                <td>Kode Penjualan</td>
                <td>Total</td>
                <td>Total PPN</td>
                <td>Nama Customer</td>
                <td>Aksi</td>
            </tr>
        </thead>
        <tbody>
            @foreach($piutang['data'] as $data)
                <tr>
                    <td>{{$loop->iteration}}</td>
                    <td>{{dmyhi($data['waktu'])}}</td>
                    <td>{{$data['kode_penjualan']}}</td>
                    <td>{{rupiah($data['total'])}}</td>
                    <td>{{rupiah($data['total_ppn'])}}</td>
                    <td>{{$data['nama_customer']}}</td>
                    <td><a href="" data-toggle="modal" data-target="#jadikan-piutang"  class="btn btn-default sendParamToModal" data-param='["{{$data['kode_penjualan']}}","#kodePenjualan"]'><span class="fa fa-cash-register"></span> Jadikan Piutang</a></td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
<div
    class="modal fade"
    id="jadikan-piutang"
    tabindex="-1"
    role="dialog"
    aria-labelledby="exampleModalLabel"
    aria-hidden="true"
    >
      <div class="modal-dialog modal-md" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="exampleModalLabel">Pilih Customer</h5>
            <button
              class="close"
              type="button"
              data-dismiss="modal"
              aria-label="Close"
            >
              <span aria-hidden="true">×</span>
            </button>
          </div>
          <div class="modal-body">
            <form action="{{url('penjualan/piutang-resto/store')}}" method="post" id="submitConfirm" data-info="Piutang Akan Dialihkan Kepada Customer Yang Dipilih">
            @csrf
            <input type="hidden" name="kode_penjualan" id="kodePenjualan" value="">
                <div class="row">
                    <div class="col-md-12 mb-3">
                        <label for="" class="form-control-label">Customer</label>
                        <select name="kode_customer" class="form-control select2 @error('kode_customer') is-invalid @enderror" id="kode_customer">
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
                    </div>
                    <div class="col-md-12 mb-3">
                        <button type="reset" class="btn btn-default"> <span class="fa fa-times"></span> Cancel</button>
                        &nbsp;
                        <button type="submit" class="btn btn-primary"> <span class="fa fa-save"></span> Save</button>
                    </div>
                </div>
            </form>
          </div>
        </div>
      </div>
    </div>
@endif
@endsection
