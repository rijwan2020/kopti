@extends('layouts.application')

@section('module', 'Rekapitulasi Piutang')

@section('content')
<div class="card">
    <div class="card-header h4 text-center">Form Rekapitulasi Piutang</div>
    <form action="{{ route('storeReportPiutangSave') }}" enctype="multipart/form-data" class="form-input" method="post" autocomplete="false">
        <div class="card-body">
            @csrf
            <div class="row">
                <div class="col-xl-10 offset-xl-1">

                    <div class="form-group">
                        <label class="form-label">No Ref *</label>
                        <input type="text" class="form-control {{ $errors->has('no_ref')?' is-invalid':'' }}" name="no_ref" id="no_ref" value="{{ old('no_ref') ?? 'TRXT-'.date('YmdHis') }}" required>
                        {!! $errors->first('no_ref', '<small class="form-text text-danger">:message</small>') !!}
                        <small class="form-text text-muted">Masukan no ref/no bukti transaksi.</small>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Keterangan *</label>
                        <input type="text" class="form-control {{ $errors->has('note')?' is-invalid':'' }}" placeholder="Keterangan" name="note" id="note" value="{{ old('note') ?? '' }}" required>
                        {!! $errors->first('note', '<small class="form-text text-danger">:message</small>') !!}
                        <small class="form-text text-muted">Masukan keterangan transaksi.</small>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Tanggal Transaksi *</label>
                        <input type="text" class="form-control {{ $errors->has('trxdate')?' is-invalid':'' }} datepicker" placeholder="Tanggal Transaksi" name="trxdate" id="trxdate" value="{{ old('trxdate') ?? date('Y-m-d') }}" required>
                        {!! $errors->first('trxdate', '<small class="form-text text-danger">:message</small>') !!}
                        <small class="form-text text-muted">Masukan Tanggal Transaksi.</small>
                    </div>

                    <div class="form-group">
                        <label for="" class="form-label">Anggota</label>
                        <select name="member_id" id="" class="form-control select2" required>
                            @foreach ($data['member'] as $value)
                                <option value="{{ $value->id }}" {{ old('member_id') == $value->id ? 'selected' : '' }}>[{{$value->code}}] - {{$value->name}}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Tipe Transaksi</label>
                        <div>
                            <label class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="tipe" value="0" {{ old('tipe')==0 ? 'checked' : ''}}>
                                <span class="form-check-label">Penambahan</span>
                            </label>
                            <label class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="tipe" value="1" {{ old('tipe')==1 || !isset($data['tipe']) ? 'checked' : ''}}>
                                <span class="form-check-label">Pengurangan</span>
                            </label>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Jumlah *</label>
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text">Rp</span>
                            </div>
                            <input type="text" class="form-control {{ $errors->has('total')?' is-invalid':'' }} money-without-separator"  name="total" id="total" value="{{ old('total') ?? 0 }}" required>
                        </div>
                        {!! $errors->first('total', '<small class="form-text text-danger">:message</small>') !!}
                        <small class="form-text text-muted">Masukan jumlah transaksi.</small>
                    </div>

                    <div class="form-group">
                        <label class="form-label"><b>Catatan :</b> Field yang diberi tanda bintang (*) <b>harus diisi.</b></label>
                    </div>
                    

                </div>
            </div>
        </div>
        <div class="card-footer text-center">
            <button type="submit" class="btn btn-dark" value="submit" data-toggle="tooltip" data-state="dark" title="Simpan">Save</button>
            @if (Auth::user()->hasRule('storeReportPiutangUpload'))
                <a href="{{ route('storeReportPiutangUpload') }}" class="btn my-1 btn-primary" data-toggle="tooltip" data-state="dark" title="Upload data">
                    <i class="fa fa-upload"></i>
                    Upload
                </a>
            @endif
        </div>
    </form>
</div>
@endsection
@section('scripts')
    
@endsection