@extends('layouts.application')

@section('module', 'Kelompok Akun')

@section('content')

<div class="card">
    <div class="card-header h4 text-center">Form Kelompok Akun</div>
    <form action="{{ route('accountGroupSave') }}" class="form-input" method="post" autocomplete="false">
        <div class="card-body">
            @csrf
            <input type="hidden" name="mode" value="{{$data['mode']}}">
            @if ($data['mode']=='edit')
                <input type="hidden" name="id" value="{{$data['data']->id}}"> 
                <input type="hidden" name="type" value="{{$data['data']->type}}"> 
                <input type="hidden" name="account_id" value="{{$data['data']->account_id}}"> 
            @endif
            <div class="row">
                <div class="col-xl-10 offset-xl-1">
                   
                    
                    <div class="form-group">
                        <label class="form-label">Nama Kelompok Akun*</label>
                        <input type="text" class="form-control {{ $errors->has('name')?' is-invalid':'' }}" placeholder="Nama Kelompok Akun" name="name" id="name" value="{{ old('name') ?? $data['data']->name ?? '' }}" required>
                        {!! $errors->first('name', '<small class="form-text text-danger">:message</small>') !!}
                        <small class="form-text text-muted">Masukan nama kelompok akun.</small>
                    </div>  

                    @if ($data['mode'] == 'add')
                        <div class="form-group">
                            <label class="form-label">Golongan *</label>
                            <select name="account_id" id="" class="form-control select2 {{ $errors->has('account_id')?' is-invalid':'' }}" required>
                                <option value="">--Pilih--</option>
                                @foreach ($data['golongan'] as $value)
                                    <option value="{{ $value->id }}">{{ $value->name }}</option>
                                @endforeach
                            </select>
                            {!! $errors->first('account_id', '<small class="form-text text-danger">:message</small>') !!}
                            <small class="form-text text-muted">Masukan nama kelompok akun.</small>
                        </div>
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
                            <small class="form-text text-muted">Pilih saldo normal kelompok akun.</small>
                        </div>
                    @endif

                    <div class="form-group">
                        <label class="form-label">Deskripsi di Laporan</label>
                        <textarea name="description" id="" cols="30" rows="5" class="form-control {{ $errors->has('name')?' is-invalid':'' }}">{{ old('description') ?? $data['data']->description ?? '' }}</textarea>
                        {!! $errors->first('description', '<small class="form-text text-danger">:message</small>') !!}
                        <small class="form-text text-muted">Masukan deskripsi untuk penjelasan di laporan. Gunakan <b>this.date</b> untuk tanggal otomatis</small>
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