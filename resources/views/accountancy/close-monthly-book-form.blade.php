@extends('layouts.application')

@section('module', 'Tutup Buku')
@section('content')

<div class="card">
    <div class="card-header h4 text-center">Form Tutup Buku Bulanan</div>
    <form action="{{ route('closeMonthlyBookPreview') }}" class="form-input" method="post" autocomplete="false">
        <div class="card-body">
            @csrf
            <div class="row">
                <div class="col-xl-10 offset-xl-1">
                    
                    <div class="form-group">
                        <label class="form-label">Tanggal Tutup Buku</label>
                        <div class="input-group">
                            <input type="text" class="form-control {{ $errors->has('closing_date')?' is-invalid':'' }} datepicker" name="closing_date" id="closing_date" value="{{ old('closing_date') ?? date('Y-m-d') }}" required>
                            <div class="input-group-prepend"><span class="input-group-text"><i class="fa fa-calendar"></i></span></div>
                        </div>
                        {!! $errors->first('closing_date', '<small class="form-text text-danger">:message</small>') !!}
                        <small class="form-text text-muted">Masukan tanggal tutup buku bulanan koperasi.</small>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Periode Pembukuan *</label>
                        <div class="input-group">
                            <div class="input-group-prepend"><span class="input-group-text">From</div>
                            <input type="text" class="form-control datepicker" name="start_periode" value="{{$data['start_periode']}}" required>
                            <div class="input-group-prepend"><span class="input-group-text">To</div>
                                <input type="text" class="form-control datepicker" name="end_periode" value="{{$data['end_periode']}}" required>
                            <div class="input-group-prepend"><span class="input-group-text"><i class="fa fa-calendar"></i></span></div>
                        </div>
                        <small class="form-text text-muted">Masukan periode pembukuan.</small>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Catatan </label>
                        <textarea name="description" id="description" cols="30" rows="5" class="form-control {{ $errors->has('description')?' is-invalid':'' }}">{{ old('description') ?? '' }}</textarea>
                        {!! $errors->first('description', '<small class="form-text text-danger">:message</small>') !!}
                        <small class="form-text text-muted">Masukan catatan untuk tutup buku.</small>
                    </div>

                    <div class="form-group">
                        <label class="form-label"><b>Catatan :</b> Field yang diberi tanda bintang (*) <b>harus diisi.</b></label>
                    </div>

                </div>
            </div>
        </div>
        <div class="card-footer text-center">
            <button type="submit" class="btn btn-dark" value="submit" data-toggle="tooltip" data-state="dark" title="Simpan">Preview</button>
        </div>
    </form>
</div>
@endsection