@extends('layouts.application')

@section('module', 'Alokasi SHU')

@section('content')
<div class="card">
    <div class="card-header h4 text-center">Form Alokasi SHU</div>
    <form action="{{ route('shuConfigSave') }}" enctype="multipart/form-data" class="form-input" method="post" autocomplete="false">
        <div class="card-body">
            @csrf
            <input type="hidden" name="mode" value="{{$data['mode']}}">
            @if ($data['mode']=='edit')
                <input type="hidden" name="id" value="{{$data['data']->id}}"> 
            @endif
            <div class="row">
                <div class="col-xl-10 offset-xl-1">

                    <div class="form-group">
                        <label class="form-label">Alokasi *</label>
                        <input type="text" class="form-control {{ $errors->has('allocation')?' is-invalid':'' }}" placeholder="Alokasi" name="allocation" id="allocation" value="{{ old('allocation') ?? $data['data']->allocation ?? '' }}" required>
                        {!! $errors->first('allocation', '<small class="form-text text-danger">:message</small>') !!}
                        <small class="form-text text-muted">Masukan Alokasi.</small>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Pilih Akun *</label>
                        <select name="account" id="account" required class="form-control select2">
                            @foreach ($data['account'] as $item)
                                <option value="{{ $item->code }}" {{ old('account') == $item->code || (isset($data['data']->account) AND $data['data']->account == $item->code) ? 'selected' : '' }}>[{{ $item->code }}] - {{ $item->name }}</option>
                            @endforeach
                        </select>
                        {!! $errors->first('account', '<small class="form-text text-danger">:message</small>') !!}
                        <small class="form-text text-muted">Pilih akun untuk alokasi.</small>
                    </div>
                    

                    <div class="form-group">
                        <label class="form-label">Persentase *</label>
                        <div class="input-group">
                            <input type="text" class="form-control {{ $errors->has('percent')?' is-invalid':'' }}" placeholder="Persentase" name="percent" id="percent" value="{{ old('percent') ?? $data['data']->percent ?? 0 }}" required>
                            <div class="input-group-prepend">
                                <span class="input-group-text">%</span>
                            </div>
                        </div>
                        {!! $errors->first('percent', '<small class="form-text text-danger">:message</small>') !!}
                        <small class="form-text text-muted">Masukan Persentase. Gunakan titik (.) untuk pecahan desimal.</small>
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