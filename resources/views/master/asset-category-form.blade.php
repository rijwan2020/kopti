@extends('layouts.application')
@section('module', 'Data Aset Barang')

@section('content')

<div class="card">
    <div class="card-header h4 text-center">Form Kategori Aset Barang</div>
    <form action="{{ route('assetCategorySave') }}" enctype="multipart/form-data" class="form-input" method="post" autocomplete="false">
        <div class="card-body">
            @csrf
            <input type="hidden" name="mode" value="{{$data['mode']}}">
            @if ($data['mode']=='edit')
                <input type="hidden" name="id" value="{{$data['data']->id}}"> 
            @endif
            <div class="row">
                <div class="col-xl-10 offset-xl-1">

                    <div class="form-group">
                        <label class="form-label">Nama Kategori*</label>
                        <input type="text" class="form-control {{ $errors->has('name')?' is-invalid':'' }}" placeholder="Nama Kategori" name="name" id="name" value="{{ old('name') ?? $data['data']->name ?? '' }}" required>
                        {!! $errors->first('name', '<small class="form-text text-danger">:message</small>') !!}
                        <small class="form-text text-muted">Masukan nama kategori aset.</small>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Deskripsi</label>
                        <textarea name="description" id="description" cols="30" rows="5" class="form-control {{ $errors->has('description')?' is-invalid':'' }}">{{ old('description') ?? $data['data']->description ?? '' }}</textarea>
                        {!! $errors->first('description', '<small class="form-text text-danger">:message</small>') !!}
                        <small class="form-text text-muted">Masukan deskripsi kategori aset.</small>
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
