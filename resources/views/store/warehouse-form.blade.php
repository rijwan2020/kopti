@extends('layouts.application')

@section('module', 'Data Gudang')

@section('content')
<div class="card">
    <div class="card-header h4 text-center">Form Data Gudang</div>
    <form action="{{ route('warehouseSave') }}" enctype="multipart/form-data" class="form-input" method="post" autocomplete="false">
        <div class="card-body">
            @csrf
            <input type="hidden" name="mode" value="{{$data['mode']}}">
            @if ($data['mode']=='edit')
                <input type="hidden" name="id" value="{{$data['data']->id}}"> 
            @endif
            <div class="row">
                <div class="col-xl-10 offset-xl-1">

                    <div class="form-group">
                        <label class="form-label">Kode Gudang *</label>
                        <input type="text" class="form-control {{ $errors->has('code')?' is-invalid':'' }}" placeholder="Kode Gudang" name="code" id="code" value="{{ old('code') ?? $data['data']->code ?? '' }}" required>
                        {!! $errors->first('code', '<small class="form-text text-danger">:message</small>') !!}
                        <small class="form-text text-muted">Masukan kode Gudang.</small>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Nama Gudang *</label>
                        <input type="text" class="form-control {{ $errors->has('name')?' is-invalid':'' }}" placeholder="Nama Gudang" name="name" id="name" value="{{ old('name') ?? $data['data']->name ?? '' }}" required>
                        {!! $errors->first('name', '<small class="form-text text-danger">:message</small>') !!}
                        <small class="form-text text-muted">Masukan nama Gudang.</small>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Contact Person</label>
                        <input type="text" class="form-control {{ $errors->has('cp')?' is-invalid':'' }}" placeholder="Contact Person" name="cp" id="cp" value="{{ old('cp') ?? $data['data']->cp ?? '' }}" >
                        {!! $errors->first('cp', '<small class="form-text text-danger">:message</small>') !!}
                        <small class="form-text text-muted">Masukan nama kontak person suplier.</small>
                    </div>

                    <div class="form-group">
                        <label class="form-label">No Telepon CP</label>
                        <input type="text" class="form-control {{ $errors->has('phone')?' is-invalid':'' }}" placeholder="No telepon cp" name="phone" id="phone" value="{{ old('phone') ?? $data['data']->phone ?? '' }}" >
                        {!! $errors->first('phone', '<small class="form-text text-danger">:message</small>') !!}
                        <small class="form-text text-muted">Masukan no telepon cp.</small>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Alamat</label>
                        <textarea name="address" id="address" cols="30" rows="5" class="form-control {{ $errors->has('address')?' is-invalid':'' }}">{{ old('address') ?? $data['data']->address ?? '' }}</textarea>
                        {!! $errors->first('address', '<small class="form-text text-danger">:message</small>') !!}
                        <small class="form-text text-muted">Masukan alamat Gudang.</small>
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
@section('scripts')
    
@endsection