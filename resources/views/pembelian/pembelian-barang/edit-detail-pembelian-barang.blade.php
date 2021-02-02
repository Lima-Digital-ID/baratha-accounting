<div class="row row-detail mb-3" data-no='{{$no}}'>
    <input type="hidden" name='id_detail[]' class='idDetail' value='{{$idDetail}}'>
    <div class="col-md-3 {{ $errors->has($fields['kode_barang']) ? ' is-invalid' : '' }}">
        <label for="" class="form-control-label">Barang</label>
        <select name="kode_barang[]" class="form-control select2" id="">
            <option value=''>---Select---</option>
            @foreach($barang as $value)
                <option value="{{$value->kode_barang}}" {{ old($fields['kode_barang'], isset($edit) ?  $edit['kode_barang'] : '') == $value->kode_barang ? 'selected' : ''}}>{{$value->kode_barang.' ~ '.$value->nama}}</option>
            @endforeach
        </select>
        @if($errors->has($fields['kode_barang']))
        <span class="invalid-feedback" role="alert">
            <strong>{{ $errors->first($fields['kode_barang']) }}</strong>
        </span>
        @endif
    </div>
    <div class="col-md-3">
        <label for="" class="form-control-label">Harga (Satuan)</label>
        <input type="number" step=".01" name="harga_satuan[]" value="{{old($fields['harga_satuan'], isset($edit) ? $edit['harga_satuan'] : '')}}" class="form-control getSubtotal {{ $errors->has($fields['harga_satuan']) ? ' is-invalid' : '' }}" data-other='#qty' id='harga_satuan'>
        @if($errors->has($fields['harga_satuan']))
        <span class="invalid-feedback" role="alert">
            <strong>{{ $errors->first($fields['harga_satuan']) }}</strong>
        </span>
        @endif
    </div>
    <div class="col-md-2">
        <label for="" class="form-control-label">Qty</label>
        <input type="number" step=".01" name="qty[]" value="{{old($fields['qty'],isset($edit) ? $edit['qty'] : '')}}" class="form-control getSubtotal getTotalQty {{ $errors->has($fields['qty']) ? ' is-invalid' : '' }}" data-other='#harga_satuan' id='qty'>
        @if($errors->has($fields['qty']))
        <span class="invalid-feedback" role="alert">
            <strong>{{ $errors->first($fields['qty']) }}</strong>
        </span>
        @endif
    </div>
    
    <div class="col-md-3">
        <label for="" class="form-control-label">Subtotal</label>
        <input type="number" step=".01" name="subtotal[]" value="{{old($fields['subtotal'], isset($edit) ? $pembelian->status_ppn != 'Sudah' ? $edit['subtotal'] : $edit['subtotal'] + $edit['ppn'] : '')}}" class="form-control subtotal" readonly>
    </div>
    <div class="col-md-1" style="margin-top: 35px">
        <a class="addDetail" data-no='{{$no}}' href=""><i class="fa fa-plus-square text-primary"></i></a>
        @if($hapus)
        <a class="deleteDetail addDeleteId" data-no='{{$no}}' href=""><i class="fa fa-minus-square text-danger"></i></a>
        @endif
    </div>
    {{-- <div class="col-md-1">
        <div class="dropdown mt-4">
            <a class="btn btn-sm btn-icon-only text-light" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
            <i class="fas fa-ellipsis-v"></i>
            </a>
            <div class="dropdown-menu dropdown-menu-right dropdown-menu-arrow">
                <a class="dropdown-item addDetail" data-no='{{$no}}' href="">Tambah</a>
                @if($hapus)
                <a class="dropdown-item deleteDetail addDeleteId" data-no='{{$no}}' href="">Hapus</a>
                @endif
            </div>
        </div>
    </div> --}}
</div>
