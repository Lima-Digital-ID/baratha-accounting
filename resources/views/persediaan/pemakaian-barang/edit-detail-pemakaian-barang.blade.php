<div class="row row-detail mb-3" data-no='{{$no}}'>
    <input type="hidden" name='id_detail[]' class='idDetail' value='{{$idDetail}}'>
    <div class="col-md-3 {{ isset($n)&&$errors->has('kode_barang.'.$n) ? ' is-invalid' : '' }}">
        <label for="" class="form-control-label">Barang</label>
        <select name="kode_barang[]" class="form-control select2 kode_barang" data-url="{{ url('persediaan/pemakaian-barang/getStock') }}">
            <option value=''>---Select---</option>
            @foreach($barang as $value)
                <option value="{{$value->kode_barang}}" {{ old($fields['kode_barang'], isset($edit) ?  $edit['kode_barang'] : '') == $value->kode_barang ? 'selected' : ''}}>{{$value->kode_barang.' ~ '.$value->nama}}</option>
            @endforeach
        </select>
        @if(isset($n)&&$errors->has('kode_barang.'.$n))
        <span class="invalid-feedback" role="alert">
            <strong>{{ $errors->first('kode_barang.'.$n) }}</strong>
        </span>
        @endif
    </div>

    <div class="col-md-1">
        <label for="" class="form-control-label">Stock</label>
        <input type="number" step=".01" name="stock[]" value="{{old($fields['stock'], isset($edit) ? $edit['stock'] : '')}}" class="form-control stock {{ isset($n)&&$errors->has('stock.'.$n) ? ' is-invalid' : '' }}" readonly>
        @if(isset($n)&&$errors->has('stock.'.$n))
        <span class="invalid-feedback" role="alert">
            <strong>{{ $errors->first('stock.'.$n) }}</strong>
        </span>
        @endif
    </div>
    
    <input type="hidden" step=".01" name="saldo[]" value="{{old($fields['saldo'], isset($edit) ? $edit['saldo'] : '')}}" class="form-control saldo {{ isset($n)&&$errors->has('saldo.'.$n) ? ' is-invalid' : '' }}" readonly>

    <div class="col-md-2">
        <label for="" class="form-control-label">Qty</label>
        <input type="number" step=".01" name="qty[]" value="{{old($fields['qty'], isset($edit) ? $edit['qty'] : '')}}" class="form-control getTotalQty {{ isset($n)&&$errors->has('qty.'.$n) ? ' is-invalid' : '' }}" id='qty'>
        @if(isset($n)&&$errors->has('qty.'.$n))
        <span class="invalid-feedback" role="alert">
            <strong>{{ $errors->first('qty.'.$n) }}</strong>
        </span>
        @endif
    </div>

    <div class="col-md-3 {{ isset($n)&&$errors->has('kode_biaya.'.$n) ? ' is-invalid' : '' }}">
        <label for="" class="form-control-label">Kode Biaya</label>
        <select name="kode_biaya[]" class="form-control select2" id="">
            <option value=''>---Select---</option>
            @foreach($kodeBiaya as $value)
                <option value="{{$value->kode_biaya}}" {{ old($fields['kode_biaya'], isset($edit) ?  $edit['kode_biaya'] : '') == $value->kode_biaya ? 'selected' : ''}}>{{$value->kode_biaya.' ~ '.$value->nama}}</option>
            @endforeach
        </select>
        @if(isset($n)&&$errors->has('kode_biaya.'.$n))
        <span class="invalid-feedback" role="alert">
            <strong>{{ $errors->first('kode_biaya.'.$n) }}</strong>
        </span>
        @endif
    </div>

    
    <div class="col-md-2">
        <label for="" class="form-control-label">Keterangan</label>
        <input type="text" name="keterangan[]" value="{{old($fields['keterangan'], isset($edit) ? $edit['keterangan'] : '')}}" class="form-control {{ isset($n)&&$errors->has('keterangan.'.$n) ? ' is-invalid' : '' }}">
        @if(isset($n)&&$errors->has('keterangan.'.$n))
        <span class="invalid-feedback" role="alert">
            <strong>{{ $errors->first('keterangan.'.$n) }}</strong>
        </span>
        @endif
    </div>

    <div class="col-md-1" style="margin-top: 35px">
        <a class="addDetail" data-no='{{$no}}' href=""><i class="fa fa-plus-square text-primary"></i></a>
        @if($hapus)
        <a class="deleteDetail addDeleteId" data-no='{{$no}}' href=""><i class="fa fa-minus-square text-danger"></i></a>
        @endif
    </div>
</div>
