@extends('layouts.application')

@section('module', 'Simpanan')

@section('content')
<div class="card">
    <div class="card-header h4 text-center">Form Jenis Simpanan</div>
    <form action="{{ route('depositTypeSave') }}" enctype="multipart/form-data" class="form-input" method="post" autocomplete="false">
        <div class="card-body">
            @csrf
            <input type="hidden" name="mode" value="{{$data['mode']}}">
            @if ($data['mode']=='edit')
                <input type="hidden" name="id" value="{{$data['data']->id}}"> 
            @endif
            <div class="row">
                <div class="col-xl-10 offset-xl-1">

                    <div class="form-group">
                        <label class="form-label">Kode Jenis*</label>
                        <input type="text" class="form-control {{ $errors->has('code')?' is-invalid':'' }}" placeholder="Kode Jenis" name="code" id="code" value="{{ old('code') ?? $data['data']->code ?? '' }}" required>
                        {!! $errors->first('code', '<small class="form-text text-danger">:message</small>') !!}
                        <small class="form-text text-muted">Masukan kode jenis simpanan.</small>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Nama Jenis*</label>
                        <input type="text" class="form-control {{ $errors->has('name')?' is-invalid':'' }}" placeholder="Nama Jenis" name="name" id="name" value="{{ old('name') ?? $data['data']->name ?? '' }}" required>
                        {!! $errors->first('name', '<small class="form-text text-danger">:message</small>') !!}
                        <small class="form-text text-muted">Masukan nama jenis simpanan.</small>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Keterangan</label>
                        <textarea name="description" id="description" cols="30" rows="5" class="form-control {{ $errors->has('description')?' is-invalid':'' }}">{{ old('description') ?? $data['data']->description ?? '' }}</textarea>
                        {!! $errors->first('description', '<small class="form-text text-danger">:message</small>') !!}
                        <small class="form-text text-muted">Masukan Keterangan jenis simpanan.</small>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Tipe</label>
                        <div>
                            <label class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="type" value="3" {{ old('type')==3 || (isset($data['data']->type) AND $data['data']->type==3) ? 'checked' : ''}}>
                                <span class="form-check-label">Reguler</span>
                            </label>
                            <label class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="type" value="4" {{ old('type')==4 || (isset($data['data']->type) AND $data['data']->type==4) ? 'checked' : ''}}>
                                <span class="form-check-label">Berjangka</span>
                            </label>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Jenis Jangka</label>
                        <div>
                            <label class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="term_type" value="0" {{ old('term_type')==0 || (isset($data['data']->term_type) AND $data['data']->term_type==0) ? 'checked' : ''}}>
                                <span class="form-check-label">Bulan</span>
                            </label>
                            <label class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="term_type" value="1" {{ old('term_type')==1 || (isset($data['data']->term_type) AND $data['data']->term_type==1) ? 'checked' : ''}}>
                                <span class="form-check-label">Nominal</span>
                            </label>
                        </div>
                        <small class="form-text text-muted">Abaikan jika jenis simpanan bukan simpanan berjangka.</small>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Max Jangka*</label>
                        <input type="text" class="form-control money-without-separator {{ $errors->has('term')?' is-invalid':'' }}" placeholder="Max jangka" name="term" id="term" value="{{ old('term') ?? $data['data']->term ?? '' }}">
                        {!! $errors->first('term', '<small class="form-text text-danger">:message</small>') !!}
                        <small class="form-text text-muted">Masukan max jangka sesuai dengan jenis jangka yang dipilih (bulan/nominal).</small>
                    </div>

                    @if ($data['mode'] == 'add')
                        <div class="form-group">
                            <label class="form-label">Induk Kode Akun *</label>
                            <select name="account" class="form-control select2 {{ $errors->has('account')?' has-danger':'' }}" required>
                                @foreach ($data['account'] as $value)
                                    <option value="{{ $value->code }}">[{{ $value->code }}] - {{ $value->name }}</option>
                                @endforeach
                            </select>
                            <small class="form-text text-muted">Pilih induk kode akun.</small>
                            {!! $errors->first('account', '<small class="text-danger">:message</small>') !!}
                        </div>
                        <div class="form-group">
                            <label class="form-label">Kelompok Akun *</label>
                            <select name="group_id" class="form-control select2 {{ $errors->has('group_id')?' has-danger':'' }}" required>
                                @foreach ($data['group'] as $value)
                                    <option value="{{ $value->id }}">{{ $value->name }}</option>
                                @endforeach
                            </select>
                            <small class="form-text text-muted">Pilih kelompok akun.</small>
                            {!! $errors->first('group_id', '<small class="text-danger">:message</small>') !!}
                        </div>
                    @endif
                    
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