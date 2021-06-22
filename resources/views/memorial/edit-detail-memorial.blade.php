<div class="row row-detail mb-3" data-no='{{$no}}'>
  <input type="hidden" name='id_detail[]' class='idDetail' value='{{$idDetail}}'>
  <div class="col-md-3 {{ isset($n)&&$errors->has('kode.'.$n) ? ' is-invalid' : '' }}">
      <label for="" class="form-control-label">Kode</label>
      <select name="kode[]" class="form-control select2">
          <option value=''>---Select---</option>
          @foreach($kodeRekening as $value)
              <option value="{{$value->kode_rekening}}"{{ old($fields['kode'], isset($edit) ?  $edit['kode'] : '') == $value->kode_rekening ? 'selected' : ''}}>{{$value->kode_rekening.' ~ '.$value->nama}}</option>
          @endforeach
      </select>
      @if(isset($n)&&$errors->has('kode.'.$n))
      <span class="invalid-feedback" role="alert">
          <strong>{{ $errors->first('kode.'.$n) }}</strong>
      </span>
      @endif
  </div>

  <div class="col-md-3 {{ isset($n)&&$errors->has('lawan.'.$n) ? ' is-invalid' : '' }}">
      <label for="" class="form-control-label">Lawan</label>
      <select name="lawan[]" class="form-control select2">
          <option value=''>---Select---</option>
          @foreach($kodeRekening as $value)
              <option value="{{$value->kode_rekening}}" {{ old($fields['lawan'], isset($edit) ?  $edit['lawan'] : '') == $value->kode_rekening ? 'selected' : ''}}>{{$value->kode_rekening.' ~ '.$value->nama}}</option>
          @endforeach
      </select>
      @if(isset($n)&&$errors->has('lawan.'.$n))
      <span class="invalid-feedback" role="alert">
          <strong>{{ $errors->first('lawan.'.$n) }}</strong>
      </span>
      @endif
  </div>

  <div class="col-md-2">
      <label for="" class="form-control-label">Nominal</label>
      <input type="number" step=".01" name="subtotal[]" value="{{old($fields['subtotal'], isset($edit) ? $edit['subtotal'] : '')}}" class="form-control getTotalKas {{ isset($n)&&$errors->has('subtotal.'.$n) ? ' is-invalid' : '' }}">
      @if(isset($n)&&$errors->has('subtotal.'.$n))
      <span class="invalid-feedback" role="alert">
          <strong>{{ $errors->first('subtotal.'.$n) }}</strong>
      </span>
      @endif
  </div>

  
  <div class="col-md-3">
      <label for="" class="form-control-label">Keterangan</label>
      <input type="text" name="keterangan[]" value="{{old($fields['keterangan'], isset($edit) ? str_replace('-', ' ', $edit['keterangan']) : '')}}" class="form-control {{ isset($n)&&$errors->has('keterangan.'.$n) ? ' is-invalid' : '' }}">
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
