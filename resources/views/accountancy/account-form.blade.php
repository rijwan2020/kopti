@extends('layouts.application')

@section('module', 'Data Akun')

@section('content')

<div class="card">
    <div class="card-header h4 text-center">Form Data Akun</div>
    <form action="{{ route('accountSave') }}" class="form-input" method="post" autocomplete="false">
        <div class="card-body">
            @csrf
            <input type="hidden" name="mode" value="{{$data['mode']}}">
            @if ($data['mode']=='edit')
                <input type="hidden" name="id" value="{{$data['data']->id}}"> 
            @endif
            <div class="row">
                <div class="col-xl-10 offset-xl-1">
                    @if ($data['mode'] == 'add')
                        <div class="form-group">
                            <label class="form-label">Induk Akun*</label>
                            <select class="form-control select2 {{ $errors->has('parent_id')?' is-invalid':'' }}" name="parent_id" required>
                                <option value=""></option>
                                @foreach ($data['akun'] as $value)
                                    @if ($value->level != 3)
                                        <option value="{{$value->id}}" {{$value->id==old('parent_id')?'selected':''}}>[{{$value->code}}] - {{$value->name}}</option>
                                    @endif
                                @endforeach
                            </select>
                            {!! $errors->first('level', '<small class="form-text text-danger">:message</small>') !!}
                            <small class="form-text text-muted">Pilih induk akun.</small>
                        </div>
                    @else
                        <div class="form-group">
                            <label class="form-label">Kode Akun*</label>
                            <input type="text" class="form-control" value="{{ $data['data']->code ?? '' }}" disabled>
                        </div>
                    @endif
                    
                    <div class="form-group">
                        <label class="form-label">Nama Akun*</label>
                        <input type="text" class="form-control {{ $errors->has('name')?' is-invalid':'' }}" placeholder="Nama Akun" name="name" id="name" value="{{ old('name') ?? $data['data']->name ?? '' }}" required>
                        {!! $errors->first('name', '<small class="form-text text-danger">:message</small>') !!}
                        <small class="form-text text-muted">Masukan nama akun.</small>
                    </div>

                    @if ($data['mode'] == 'add')
                        <div class="form-group">
                            <label class="form-label">Saldo Normal</label>
                            <div>
                                <label class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="type" value="0" {{ old('type')==0 || (isset($data['data']->type) AND $data['data']->type==0) ? 'checked' : ''}}>
                                    <span class="form-check-label">Debit</span>
                                </label>
                                <label class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="type" value="1" {{ old('type')==1 || (isset($data['data']->type) AND $data['data']->type==1) ? 'checked' : ''}}>
                                    <span class="form-check-label">Kredit</span>
                                </label>
                            </div>
                            <small class="form-text text-muted">Pilih saldo normal akun.</small>
                        </div>
                    @endif

                    <div class="form-group">
                        <label class="form-label">Kelompok Akun*</label>
                        <select class="form-control select2 {{ $errors->has('group_id')?' is-invalid':'' }}" name="group_id" required>
                            @foreach ($data['group'] as $value)
                                <option value="{{ $value->id }}" {{ $value->id == old('group_id') || (isset($data['data']->group_id) AND $data['data']->group_id == $value->id) ? 'selected' : '' }}>{{ $value->name }}</option>
                            @endforeach
                        </select>
                        {!! $errors->first('level', '<small class="form-text text-danger">:message</small>') !!}
                        <small class="form-text text-muted">Pilih kelompok akun.</small>
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