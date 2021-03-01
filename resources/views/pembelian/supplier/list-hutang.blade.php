<div class="table-responsive">
    <table class="table table table-custom td-grey">
        <thead>
            <tr>
                <td>#</td>
                <td>Kode Transaksi</td>
                <td>Tanggal Transaksi</td>
                <td>Jumlah Hutang</td>
                <td>Sisa</td>
                <td>Aksi</td>
            </tr>
        </thead>
        <tbody>
            @php $no=0 @endphp
            @foreach($hutang as $data)
                @php $no++ @endphp
                <tr>
                    <td>{{$no}}</td>
                    <td>{{$data->kode_pembelian}}</td>
                    <td>{{date('m-d-Y', strtotime($data->tanggal))}}</td>
                    <td>{{number_format($data->grandtotal,0,',','.')}}</td>
                    <td>{{number_format($data->grandtotal-$data->terbayar,0,',','.')}}</td>
                    <td><a href="" class="btn btn-default btn-pembayaran" data-param='["{{$data->kode_pembelian}}","{{$data->grandtotal}}","{{$data->grandtotal-$data->terbayar}}"]' data-toggle="modal" data-target=".modal-pembayaran"><span class="fa fa-credit-card"></span> Pembayaran</a></td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
<div
    class="modal fade modal-pembayaran"
    tabindex="-1"
    role="dialog"
    aria-labelledby="exampleModalLabel"
    aria-hidden="true"
    >
      <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="exampleModalLabel">Pembayaran Hutang</h5>
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
            <form action="{{url('pembelian/pembayaran-hutang')}}" method="post">
            @csrf
            <input type="hidden" name="kode_transaksi" value="{{$kode_transaksi}}">
            <input type="hidden" name="kode_supplier" value="{{$kode_supplier}}">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="">Kode Pembelian</label>
                        <input type="text" class="form-control" name="kode_pembelian" id='kode-hutangpiutang' readonly>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="">Jumlah Hutang</label>
                        <input type="text" class="form-control" id='jml-hutangpiutang' readonly>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="">Sisa</label>
                        <input type="text" class="form-control" id='sisa' readonly>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="">Nominal Bayar</label>
                        <input type="text" class="form-control" name='nominal_bayar' min="1" max="{{$sisaDetail}}">
                    </div>
                    <div class="col-md-6 mb-3">
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
