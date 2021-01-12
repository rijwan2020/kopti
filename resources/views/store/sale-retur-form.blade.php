@extends('layouts.application')

@section('module', 'Retur Penjualan')

@section('content')
<div class="card">
    <div class="card-header h4 text-center">Form Data Penjualan Barang</div>
    @if ($data['mode'] == 'add')
        <form action="{{ route('saleReturConfirm') }}" enctype="multipart/form-data" class="form-input" method="post" autocomplete="false" id="form-penjualan">
            <div class="card-body">
                @csrf
                <input type="hidden" name="id" value="{{ $data['data']->id }}">
                <div class="row">
                    <div class="col-xl-10 offset-xl-1">
                        
                        <div class="form-group">
                            <label class="form-label">No Faktur Penjualan</label>
                            <input type="text" class="form-control" value="{{ $data['data']->sale->no_faktur }}" disabled>
                        </div>

                        <div class="form-group">
                            <label class="form-label">Barang</label>
                            <input type="text" class="form-control" value="[{{ $data['data']->item->code }}] - {{ $data['data']->item->name }}" disabled>
                        </div>

                        <div class="form-group">
                            <label class="form-label">Pembeli</label>
                            <input type="text" class="form-control" value="[{{ $data['data']->sale->member->code }}] - {{ $data['data']->sale->member->name }}" disabled>
                        </div>

                        <div class="form-group">
                            <label class="form-label">No Ref</label>
                            <input type="text" class="form-control {{ $errors->has('no_ref')?' is-invalid':'' }}" placeholder="No Ref" name="no_ref" id="no_ref" value="{{ old('no_ref') ?? 'TRXT-'.date('YmdHis') }}" required>
                            {!! $errors->first('no_ref', '<small class="form-text text-danger">:message</small>') !!}
                            <small class="form-text text-muted">Masukan No Ref.</small>
                        </div>

                        <div class="form-group">
                            <label class="form-label">Tanggal Retur *</label>
                            <input type="text" class="form-control {{ $errors->has('tanggal_transaksi')?' is-invalid':'' }} datepicker" placeholder="Tanggal Penjualan" name="tanggal_transaksi" id="tanggal_transaksi" value="{{ old('tanggal_transaksi') ?? date('Y-m-d') }}" required>
                            {!! $errors->first('tanggal_transaksi', '<small class="form-text text-danger">:message</small>') !!}
                            <small class="form-text text-muted">Masukan tanggal retur.</small>
                        </div>
                        
                        <div class="form-group">
                            <label class="form-label">Keterangan</label>
                            <input type="text" class="form-control {{ $errors->has('note')?' is-invalid':'' }}" placeholder="Keterangan" name="note" id="note" value="{{ old('note') ?? '' }}">
                            {!! $errors->first('note', '<small class="form-text text-danger">:message</small>') !!}
                            <small class="form-text text-muted">Masukan keterangan retur barang.</small>
                        </div>

                        <div class="form-group">
                            <label class="form-label">Qty</label>
                            <input type="text" class="form-control {{ $errors->has('qty')?' is-invalid':'' }}" name="qty" id="qty" value="{{ old('qty') ?? '1' }}">
                            {!! $errors->first('qty', '<small class="form-text text-danger">:message</small>') !!}
                            <small class="form-text text-muted">Masukan qty yg diretur. Maksimal <b>{{ $data['data']->qty - $data['data']->qty_retur }}</b></small>
                        </div>

                        <div class="form-group">
                            <label class="form-label">Pilih Akun *</label>
                            <select name="akun" id="akun" class="form-control select2" required>
                                <option value="">--Pilih--</option>
                                @foreach ($data['cash'] as $value)
                                    <option value="{{ $value->code }}">[{{ $value->code }}] - {{ $value->name }}</option>
                                @endforeach
                            </select>
                            {!! $errors->first('akun', '<small class="form-text text-danger">:message</small>') !!}
                            <small class="form-text text-muted">Pilih akun transaksi.</small>
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
    @else
        <form action="{{ route('saleReturSave') }}" enctype="multipart/form-data" class="form-input" method="post" autocomplete="false" id="form-penjualan">
            <div class="card-body">
                @csrf
                <input type="hidden" name="id" value="{{ $data['id'] }}">
                <input type="hidden" name="no_ref" value="{{ $data['no_ref'] }}">
                <input type="hidden" name="tanggal_transaksi" value="{{ $data['tanggal_transaksi'] }}">
                <input type="hidden" name="note" value="{{ $data['note'] }}">
                <input type="hidden" name="qty" value="{{ $data['qty'] }}">
                <input type="hidden" name="akun" value="{{ $data['akun']->code }}">
                <div class="row">
                    <div class="col-xl-10 offset-xl-1">
                        
                        <div class="form-group">
                            <label class="form-label">No Faktur Penjualan</label>
                            <input type="text" class="form-control" value="{{ $data['detail']->sale->no_faktur }}" disabled>
                        </div>

                        <div class="form-group">
                            <label class="form-label">Barang</label>
                            <input type="text" class="form-control" value="[{{ $data['detail']->item->code }}] - {{ $data['detail']->item->name }}" disabled>
                        </div>

                        <div class="form-group">
                            <label class="form-label">Pembeli</label>
                            <input type="text" class="form-control" value="[{{ $data['detail']->sale->member->code }}] - {{ $data['detail']->sale->member->name }}" disabled>
                        </div>

                        <div class="form-group">
                            <label class="form-label">No Ref</label>
                            <input type="text" class="form-control" value="{{ $data['no_ref'] }}" disabled>
                        </div>

                        <div class="form-group">
                            <label class="form-label">Tanggal Retur</label>
                            <input type="text" class="form-control" value="{{ $data['tanggal_transaksi'] }}" disabled>
                        </div>

                        <div class="form-group">
                            <label class="form-label">Keterangan</label>
                            <input type="text" class="form-control" value="{{ $data['note'] }}" disabled>
                        </div>

                        <div class="form-group">
                            <label class="form-label">Qty</label>
                            <input type="text" class="form-control" value="{{ $data['qty'] }}" disabled>
                        </div>

                        <div class="form-group">
                            <label class="form-label">Akun Pembukuan</label>
                            <input type="text" class="form-control" value="[{{ $data['akun']->code }}] - {{ $data['akun']->name }}" disabled>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card-footer text-center">
                <button type="submit" class="btn btn-dark" value="submit" data-toggle="tooltip" data-state="dark" title="Simpan">Save</button>
            </div>
        </form>
    @endif
</div>
@endsection