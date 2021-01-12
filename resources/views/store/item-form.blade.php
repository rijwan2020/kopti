@extends('layouts.application')

@section('module', 'Data Barang')

@section('content')
<div class="card">
    <div class="card-header h4 text-center">Form Data Barang</div>
    <form action="{{ route('itemSave') }}" enctype="multipart/form-data" class="form-input" method="post" autocomplete="false">
        <div class="card-body">
            @csrf
            <input type="hidden" name="mode" value="{{$data['mode']}}">
            @if ($data['mode']=='edit')
                <input type="hidden" name="id" value="{{$data['data']->id}}"> 
            @endif
            <div class="row">
                <div class="col-xl-10 offset-xl-1">

                    <div class="form-group">
                        <label class="form-label">Kode Barang *</label>
                        <input type="text" class="form-control {{ $errors->has('code')?' is-invalid':'' }}" placeholder="Kode Barang" name="code" id="code" value="{{ old('code') ?? $data['data']->code ?? '' }}" required>
                        {!! $errors->first('code', '<small class="form-text text-danger">:message</small>') !!}
                        <small class="form-text text-muted">Masukan kode Barang.</small>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Nama Barang *</label>
                        <input type="text" class="form-control {{ $errors->has('name')?' is-invalid':'' }}" placeholder="Nama Barang" name="name" id="name" value="{{ old('name') ?? $data['data']->name ?? '' }}" required>
                        {!! $errors->first('name', '<small class="form-text text-danger">:message</small>') !!}
                        <small class="form-text text-muted">Masukan nama Barang.</small>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Harga Jual *</label>
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text">Rp</span>
                            </div>
                            <input type="text" class="form-control {{ $errors->has('harga_jual')?' is-invalid':'' }} money-without-separator" placeholder="Harga Jual" name="harga_jual" id="harga_jual" value="{{ old('harga_jual') ?? number_format($data['data']->harga_jual ?? 0) }}" required>
                        </div>
                        {!! $errors->first('harga_jual', '<small class="form-text text-danger">:message</small>') !!}
                        <small class="form-text text-muted">Masukan Harga Jual.</small>
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