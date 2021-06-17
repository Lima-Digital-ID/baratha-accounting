<head>
    <title>INVOICE {{$report->kode_penjualan}}</title>
  </head>
  <center>
    <img src="{{ asset('img/invoice-header.jpg') }}" alt="">
    <table>
      <tr>
        <td colspan="3"><b>Bill To</b></td>
  
        <td rowspan="5" width="70px"></td>
  
        <td colspan="3"><div class="invoice">INVOICE</div></td>
      </tr>
  
      <tr>
        <td>Name</td>
        <td>:</td>
        <td>{{$report->nama}}</td>      
  
        <td>Number</td>
        <td>:</td>
        <td>{{$report->kode_penjualan}}</td>
      </tr>
  
      <tr>
        <td>Date</td>
        <td>:</td>
        <td>{{date('d-m-Y',strtotime($report->tanggal))}}</td>
      </tr>
      
    </table>
    <br>
    <table id="detail">
        <thead>
            <tr>
                <td>Tanggal</td>
                <td>Customer</td>
                <td>Kode Transaksi</td>
                <td>Keterangan</td>
                <td>Qty</td>
                <td>Subtotal</td>
            </tr>
        </thead>
        <tbody>
        @php
            $total_qty = $report->qty;
            $total_harga_satuan = $report->harga_satuan;
            $total_dpp = $report->total;
            $total_ppn = $report->total_ppn;
            $grandtotal = ($report->total + $report->total_ppn);
        @endphp
        <tr>
           <td>{{ $report->tanggal }}</td>
           <td>{{ $report->kode_customer }} - {{ $report->nama }}</td>
           <td>{{ $report->kode_penjualan }}</td>
           <td>{{ $report->keterangan }}</td>
           <td>{{ $report->qty }}</td>
           <td>Rp{{ number_format($report->total, 2, ',', '.') }}</td>
        </tr>
        </tbody>
    </table>
    <br>
    <br>
    <table style="float: right;" id="total">
      <tr>
        <td><b>PPN</b></td>
        <td>:</td>
        <td>Rp{{number_format($total_ppn, 2, ',', '.')}}</td>
      </tr>
      <tr>
        <td><b>Total</b></td>
        <td>:</td>
        <td>Rp{{number_format($grandtotal, 2, ',', '.')}}</td>
      </tr>
    </table>
    <br><br>
    <br><br>
    <img src="{{ asset('img/invoice-footer.jpg') }}" alt="">
  </center>
  <style>
    table{
      font-family: Arial, Helvetica, sans-serif;
    }
    table .invoice{
      font-family: Arial, Helvetica, sans-serif;
      font-size: 40px;
      font-weight: bold;
      color: #fe0000;
    }
    #detail {
      font-family: Arial, Helvetica, sans-serif;
      border-collapse: collapse;
      width: 100%;
    }
  
    #detail td, #detail th {
      border: 1px solid #ddd;
      padding: 8px;
    }
  
    #detail tr:nth-child(even){background-color: #f2f2f2;}
  
    #detail tr:hover {background-color: #ddd;}
  
    #detail th {
      padding-top: 12px;
      padding-bottom: 12px;
      text-align: center;
      background-color: #fe0000;
      color: white;
    }
  
    #total {
      margin-left: 450px;
    }
  </style>
  <script>
    window.print()
  </script>