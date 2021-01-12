@extends('layouts.application')
@section('module', 'Data Aset Barang')

@section('content')

<div class="card">
    <div class="card-header h4 text-center">Form Aset Barang</div>
    <form action="{{ route('assetSave') }}" enctype="multipart/form-data" class="form-input" method="post" autocomplete="false">
        <div class="card-body">
            @csrf
            <input type="hidden" name="mode" value="{{$data['mode']}}">
            @if ($data['mode']=='edit')
                <input type="hidden" name="id" value="{{$data['data']->id}}"> 
            @endif
            <div class="row">
                <div class="col-xl-10 offset-xl-1">

                    <div class="form-group">
                        <label class="form-label">Nama*</label>
                        <input type="text" class="form-control {{ $errors->has('name')?' is-invalid':'' }}" placeholder="Nama" name="name" id="name" value="{{ old('name') ?? $data['data']->name ?? '' }}" required>
                        {!! $errors->first('name', '<small class="form-text text-danger">:message</small>') !!}
                        <small class="form-text text-muted">Masukan nama aset.</small>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Pilih Kategori *</label>
                        <select class="form-control select2 {{ $errors->has('asset_category_id')?' is-invalid':'' }}" name="asset_category_id">
                            @foreach ($data['category'] as $value)
                                <option value="{{$value->id}}" {{$value->id==old('asset_category_id')?'selected':''}}>{{$value->name}}</option>
                            @endforeach
                        </select>
                        {!! $errors->first('asset_category_id', '<small class="form-text text-danger">:message</small>') !!}
                        <small class="form-text text-muted">Pilih kategori aset.</small>
                    </div>

                    <div class="form-group">
                        <label class="form-label">QTY*</label>
                        <input type="text" class="form-control {{ $errors->has('qty')?' is-invalid':'' }}" name="qty" id="qty" value="{{ old('qty') ?? $data['data']->qty ?? 1 }}" required>
                        {!! $errors->first('qty', '<small class="form-text text-danger">:message</small>') !!}
                        <small class="form-text text-muted">Masukan total unit aset, minimal 1.</small>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Harga Beli Total *</label>
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text">Rp</span>
                            </div>
                            <input type="text" class="form-control {{ $errors->has('price')?' is-invalid':'' }} money-without-separator" placeholder="Besar Simpanan Wajib" name="price" id="price" value="{{ old('besar_sp') ?? number_format($data['data']['price'] ?? 0) }}">
                        </div>
                        {!! $errors->first('price', '<small class="form-text text-danger">:message</small>') !!}
                        <small class="form-text text-muted">Masukan total harga beli.</small>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Tanggal Beli</label>
                        <div class="input-group" style="width: 40%">
                            <div class="input-group-prepend">
                                <span class="input-group-text"><i class="fa fa-calendar"></i></span>
                            </div>
                            <input type="text" class="form-control {{ $errors->has('purchase_date')?' is-invalid':'' }} datepicker" placeholder="Tanggal Beli" name="purchase_date" id="purchase_date" value="{{ old('purchase_date') ?? $data['data']['purchase_date'] ?? date('Y-m-d') }}">
                        </div>
                        {!! $errors->first('id', '<small class="form-text text-danger">:message</small>') !!}
                        <small class="form-text text-muted">Masukan Tanggal beli barang aset.</small>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Nilai Barang Tahun ini *</label>
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text">Rp</span>
                            </div>
                            <input type="text" class="form-control {{ $errors->has('item_value')?' is-invalid':'' }} money-without-separator" placeholder="Besar Simpanan Wajib" name="item_value" id="item_value" value="{{ old('besar_sp') ?? number_format($data['data']['item_value'] ?? 0) }}">
                        </div>
                        {!! $errors->first('item_value', '<small class="form-text text-danger">:message</small>') !!}
                        <small class="form-text text-muted">Masukan nilai barang tahun ini (setelah ada penyusutan).</small>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Keterangan</label>
                        <textarea name="note" id="note" cols="30" rows="5" class="form-control {{ $errors->has('note')?' is-invalid':'' }}">{{ old('note') ?? $data['data']->note ?? '' }}</textarea>
                        {!! $errors->first('note', '<small class="form-text text-danger">:message</small>') !!}
                        <small class="form-text text-muted">Masukan keterangan kategori aset.</small>
                    </div>

                    <div class="form-group">
                        <label class="form-label"><b>Catatan :</b> Field yang diberi tanda bintang (*) <b>harus diisi.</b></label>
                    </div>

                </div>
            </div>
        </div>
        <div class="card-footer text-center">
            <button type="submit" class="btn btn-dark" value="submit" data-toggle="tooltip" data-state="dark" title="Simpan">Save</button>
        </div>
    </form>
</div>
@endsection
