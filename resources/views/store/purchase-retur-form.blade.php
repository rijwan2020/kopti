@extends('layouts.application')

@section('module', 'Retur Stok')

@section('content')
<div class="card">
    <div class="card-header h4 text-center">Form Retur Stok</div>
    @if ($data['mode'] == 'add')
        <form action="{{ route('purchaseReturConfirm') }}" enctype="multipart/form-data" class="form-input" method="post" autocomplete="false" id="form-penjualan">
            <div class="card-body">
                @csrf
                <input type="hidden" name="id" value="{{ $data['data']->id }}">
                <div class="row">
                    <div class="col-xl-10 offset-xl-1">
                        <div class="form-group">
                            <label class="form-label">Barang</label>
                            <input type="text" class="form-control" value="[{{ $data['data']->item->code }}] - {{ $data['data']->item->name }}" disabled>
                        </div>

                        <div class="form-group">
                            <label class="form-label">Harga Beli</label>
                            <input type="text" class="form-control" value="Rp{{ number_format($data['data']->harga_beli, 2, ',', '.') }}/Kg" disabled>
                        </div>

                        <div class="form-group">
                            <label class="form-label">No Ref</label>
                            <input type="text" class="form-control {{ $errors->has('no_retur')?' is-invalid':'' }}" placeholder="No Ref" name="no_retur" id="no_retur" value="{{ old('no_retur') ?? 'TRXT-'.date('YmdHis') }}" required>
                            {!! $errors->first('no_retur', '<small class="form-text text-danger">:message</small>') !!}
                            <small class="form-text text-muted">Masukan No Ref.</small>
                        </div>

                        <div class="form-group">
                            <label class="form-label">Tanggal Retur *</label>
                            <input type="text" class="form-control {{ $errors->has('tanggal_retur')?' is-invalid':'' }} datepicker" placeholder="Tanggal Penjualan" name="tanggal_retur" id="tanggal_retur" value="{{ old('tanggal_retur') ?? date('Y-m-d') }}" required>
                            {!! $errors->first('tanggal_retur', '<small class="form-text text-danger">:message</small>') !!}
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
        <form action="{{ route('purchaseReturSave') }}" enctype="multipart/form-data" class="form-input" method="post" autocomplete="false" id="form-penjualan">
            <div class="card-body">
                @csrf
                <input type="hidden" name="id" value="{{ $data['id'] }}">
                <input type="hidden" name="no_retur" value="{{ $data['no_retur'] }}">
                <input type="hidden" name="tanggal_retur" value="{{ $data['tanggal_retur'] }}">
                <input type="hidden" name="note" value="{{ $data['note'] }}">
                <input type="hidden" name="qty" value="{{ $data['qty'] }}">
                <input type="hidden" name="akun" value="{{ $data['akun']->code }}">
                <div class="row">
                    <div class="col-xl-10 offset-xl-1">

                        <div class="form-group">
                            <label class="form-label">Barang</label>
                            <input type="text" class="form-control" value="[{{ $data['data']->item->code }}] - {{ $data['data']->item->name }}" disabled>
                        </div>

                        <div class="form-group">
                            <label class="form-label">Harga Beli</label>
                            <input type="text" class="form-control" value="Rp{{ number_format($data['data']->harga_beli, 2, ',', '.') }}/Kg" disabled>
                        </div>

                        <div class="form-group">
                            <label class="form-label">No Ref</label>
                            <input type="text" class="form-control" value="{{ $data['no_retur'] }}" disabled>
                        </div>

                        <div class="form-group">
                            <label class="form-label">Tanggal Retur</label>
                            <input type="text" class="form-control" value="{{ $data['tanggal_retur'] }}" disabled>
                        </div>

                        <div class="form-group">
                            <label class="form-label">Keterangan</label>
                            <input type="text" class="form-control" value="{{ $data['note'] }}" disabled>
                        </div>

                        <div class="form-group">
                            <label class="form-label">Qty</label>
                            <input type="text" class="form-control" value="{{ $data['qty'] }} Kg" disabled>
                        </div>

                        <div class="form-group">
                            <label class="form-label">Total</label>
                            <input type="text" class="form-control" value="Rp{{ number_format($data['qty'] * $data['data']->harga_beli,2,',', '.') }}" disabled>
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