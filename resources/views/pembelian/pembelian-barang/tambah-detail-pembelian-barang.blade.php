<div class="row row-detail mb-3" data-no='{{$no}}'>
    <div class="col-md-3 {{ isset($n)&&$errors->has('kode_barang.'.$n) ? ' is-invalid' : '' }}">
        <label for="" class="form-control-label">Barang</label>
        <select name="kode_barang[]" class="form-control select2" id="">
            <option value=''>---Select---</option>
            @foreach($barang as $value)
                <option value="{{$value->kode_barang}}" {{ isset($n)&&old('kode_barang.'.$n) == $value->kode_barang ? 'selected' : ''}}>{{$value->kode_barang.' ~ '.$value->nama}}</option>
            @endforeach
        </select>
        @if(isset($n)&&$errors->has('kode_barang.'.$n))
        <span class="invalid-feedback" role="alert">
            <strong>{{ $errors->first('kode_barang.'.$n) }}</strong>
        </span>
        @endif
    </div>
  
    <div class="col-md-3">
      <label for="" class="form-control-label">Subtotal</label>
      <input type="number" step=".01" name="subtotal[]" value="{{isset($n) ? old('subtotal.'.$n) : ''}}" class="form-control subtotal getSubtotal" data-other='#qty' id='subtotal'>
    </div>
  
    <div class="col-md-2">
        <label for="" class="form-control-label">Qty</label>
        <input type="number" step=".01" name="qty[]" value="{{isset($n) ? old('qty.'.$n) : ''}}" class="form-control getSubtotal getTotalQty {{ isset($n)&&$errors->has('qty.'.$n) ? ' is-invalid' : '' }}" data-other='#subtotal' id='qty'>
        @if(isset($n)&&$errors->has('qty.'.$n))
        <span class="invalid-feedback" role="alert">
            <strong>{{ $errors->first('qty.'.$n) }}</strong>
        </span>
        @endif
    </div>
  
    <div class="col-md-3">
      <label for="" class="form-control-label">Harga (Satuan)</label>
      <input type="number" step=".01" name="harga_satuan[]" value="{{isset($n) ? old('harga_satuan.'.$n) : ''}}" class="form-control harga_satuan {{ isset($n)&&$errors->has('harga_satuan.'.$n) ? ' is-invalid' : '' }}" id='harga_satuan' readonly>
      @if(isset($n)&&$errors->has('harga_satuan.'.$n))
      <span class="invalid-feedback" role="alert">
          <strong>{{ $errors->first('harga_satuan.'.$n) }}</strong>
      </span>
      @endif
    </div>
    
    <div class="col-md-1" style="margin-top: 35px">
          <a class="addDetail" data-no='{{$no}}' href=""><i class="fa fa-plus-square text-primary"></i></a>
          @if($hapus)
          <a class="deleteDetail" data-no='{{$no}}' href=""><i class="fa fa-minus-square text-danger"></i></a>
          @endif
    </div>
  </div>
  