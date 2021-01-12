@extends('layouts.application')
@section('module', 'Data Karyawan')

@section('content')

<div class="card">
    <div class="card-header h4 text-center">Form Posisi Karyawan</div>
    <form action="{{ route('employeePositionSave') }}" enctype="multipart/form-data" class="form-input" method="post" autocomplete="false">
        <div class="card-body">
            @csrf
            <input type="hidden" name="mode" value="{{$data['mode']}}">
            @if ($data['mode']=='edit')
                <input type="hidden" name="id" value="{{$data['data']->id}}"> 
            @endif
            <div class="row">
                <div class="col-xl-10 offset-xl-1">

                    <div class="form-group">
                        <label class="form-label">Nama Posisi*</label>
                        <input type="text" class="form-control {{ $errors->has('name')?' is-invalid':'' }}" placeholder="Nama" name="name" id="name" value="{{ old('name') ?? $data['data']->name ?? '' }}" required>
                        {!! $errors->first('name', '<small class="form-text text-danger">:message</small>') !!}
                        <small class="form-text text-muted">Masukan nama Posisi.</small>
                    </div>

                    @if ($data['mode']=='add')
                        <div class="form-group">
                            <label class="form-label">Level User*</label>
                            <select class="form-control select2 {{ $errors->has('level_id')?' is-invalid':'' }}" name="level_id">
                                @foreach ($data['level'] as $value)
                                    @if ($value->id > 30 && $value->id <=90)
                                        <option value="{{$value->id}}" {{$value->id==old('level_id')?'selected':''}}>{{$value->name}}</option>
                                    @endif
                                @endforeach
                            </select>
                            {!! $errors->first('level', '<small class="form-text text-danger">:message</small>') !!}
                            <small class="form-text text-muted">Pilih level user untuk Posisi.</small>
                        </div>
                    @else
                        <div class="form-group">
                            <label class="form-label">Level User*</label>
                            <input type="text" class="form-control" value="{{ $data['data']->level->name }}" disabled>
                        </div>
                        <input type="hidden" name="level_id" value="{{$data['data']->level_id}}">
                    @endif

                    <div class="form-group">
                        <label class="form-label">Deskripsi</label>
                        <textarea name="description" id="description" cols="30" rows="5" class="form-control {{ $errors->has('description')?' is-invalid':'' }}">{{ old('description') ?? $data['data']->description ?? '' }}</textarea>
                        {!! $errors->first('description', '<small class="form-text text-danger">:message</small>') !!}
                        <small class="form-text text-muted">Masukan deskripsi Posisi.</small>
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
