@extends('layouts.application')
@section('module', 'Konfigurasi Aplikasi')

@section('content')
<div class="card">
    <div class="card-header h4 text-center">Konfigurasi Aplikasi</div>
    <form action="{{ route('configAppsUpdate') }}" enctype="multipart/form-data" class="form-input" method="post" autocomplete="false">
        <div class="card-body">
            @csrf
            <div class="row">
                <div class="col-xl-10 offset-xl-1">

                    <div class="form-group">
                        <label class="form-label">Kode Anggota Selanjutnya *</label>
                        <input type="text" class="form-control {{ $errors->has('next_code_anggota')?' is-invalid':'' }} money-without-separator" placeholder="Besar Simpanan Pokok" name="next_code_anggota" id="next_code_anggota" value="{{ old('next_code_anggota') ?? $data['data']['next_code_anggota'] }}">
                        {!! $errors->first('next_code_anggota', '<small class="form-text text-danger">:message</small>') !!}
                        <small class="form-text text-muted">Atur kode otomatis selanjutnya anggota.</small>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Kode Non Anggota Selanjutnya *</label>
                        <input type="text" class="form-control {{ $errors->has('next_code_non_anggota')?' is-invalid':'' }} money-without-separator" placeholder="Besar Simpanan Pokok" name="next_code_non_anggota" id="next_code_non_anggota" value="{{ old('next_code_non_anggota') ?? $data['data']['next_code_non_anggota'] }}">
                        {!! $errors->first('next_code_non_anggota', '<small class="form-text text-danger">:message</small>') !!}
                        <small class="form-text text-muted">Atur kode otomatis selanjutnya non anggota.</small>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Besar Simpanan Pokok *</label>
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text">Rp</span>
                            </div>
                            <input type="text" class="form-control {{ $errors->has('besar_sp')?' is-invalid':'' }} money-without-separator" placeholder="Besar Simpanan Pokok" name="besar_sp" id="besar_sp" value="{{ old('besar_sp') ?? number_format($data['data']['besar_sp'] ?? 0) }}">
                        </div>
                        {!! $errors->first('besar_sp', '<small class="form-text text-danger">:message</small>') !!}
                        <small class="form-text text-muted">Atur besar simpanan pokok anggota.</small>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Besar Simpanan Wajib *</label>
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text">Rp</span>
                            </div>
                            <input type="text" class="form-control {{ $errors->has('besar_sw')?' is-invalid':'' }} money-without-separator" placeholder="Besar Simpanan Wajib" name="besar_sw" id="besar_sw" value="{{ old('besar_sw') ?? number_format($data['data']['besar_sw'] ?? 0) }}">
                        </div>
                        {!! $errors->first('besar_sw', '<small class="form-text text-danger">:message</small>') !!}
                        <small class="form-text text-muted">Atur besar simpanan wajib anggota.</small>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Periode Pembukuan *</label>
                        <div class="input-group">
                            <div class="input-group-prepend"><span class="input-group-text">From</div>
                            <input type="text" class="form-control datepicker" name="journal_periode_start" value="{{$data['data']['journal_periode_start']}}">
                            <div class="input-group-prepend"><span class="input-group-text">To</div>
                                <input type="text" class="form-control datepicker" name="journal_periode_end" value="{{$data['data']['journal_periode_end']}}">
                            <div class="input-group-prepend"><span class="input-group-text"><i class="fa fa-calendar"></i></span></div>
                        </div>
                        <small class="form-text text-muted">Masukan periode aktif pembukuan.</small>
                    </div>

                    <div class="form-group">
                        <label for="" class="form-label">Akun Pembelian Barang Toko</label>
                        <select name="akun_pembelian" id="" class="form-control select2">
                            @foreach ($data['account'] as $value)
                                <option value="{{ $value->code }}" {{ $data['data']['akun_pembelian'] == $value->code ? 'selected' : '' }}>[{{ $value->code }}] - {{ $value->name }}</option>
                            @endforeach
                        </select>
                        {!! $errors->first('akun_pembelian', '<small class="form-text text-danger">:message</small>') !!}
                        <small class="form-text text-muted">Pilih akun untuk pembelian persediaan barang.</small>
                    </div>

                    <div class="form-group">
                        <label for="" class="form-label">Akun Diskon Pembelian Barang Toko</label>
                        <select name="akun_diskon_pembelian" id="" class="form-control select2">
                            @foreach ($data['account'] as $value)
                                <option value="{{ $value->code }}" {{ $data['data']['akun_diskon_pembelian'] == $value->code ? 'selected' : '' }}>[{{ $value->code }}] - {{ $value->name }}</option>
                            @endforeach
                        </select>
                        {!! $errors->first('akun_diskon_pembelian', '<small class="form-text text-danger">:message</small>') !!}
                        <small class="form-text text-muted">Pilih akun untuk diskon pembelian persediaan barang.</small>
                    </div>

                    <div class="form-group">
                        <label for="" class="form-label">Akun Penjualan Barang ke Anggota</label>
                        <select name="akun_penjualan_anggota" id="" class="form-control select2">
                            @foreach ($data['account'] as $value)
                                <option value="{{ $value->code }}" {{ $data['data']['akun_penjualan_anggota'] == $value->code ? 'selected' : '' }}>[{{ $value->code }}] - {{ $value->name }}</option>
                            @endforeach
                        </select>
                        {!! $errors->first('akun_penjualan_anggota', '<small class="form-text text-danger">:message</small>') !!}
                        <small class="form-text text-muted">Pilih akun untuk penjualan barang ke anggota.</small>
                    </div>

                    <div class="form-group">
                        <label for="" class="form-label">Akun Penjualan Barang ke Non Anggota</label>
                        <select name="akun_penjualan_non_anggota" id="" class="form-control select2">
                            @foreach ($data['account'] as $value)
                                <option value="{{ $value->code }}" {{ $data['data']['akun_penjualan_non_anggota'] == $value->code ? 'selected' : '' }}>[{{ $value->code }}] - {{ $value->name }}</option>
                            @endforeach
                        </select>
                        {!! $errors->first('akun_penjualan_non_anggota', '<small class="form-text text-danger">:message</small>') !!}
                        <small class="form-text text-muted">Pilih akun untuk penjualan barang ke non anggota.</small>
                    </div>

                    <div class="form-group">
                        <label for="" class="form-label">Akun Piutang Penjualan Barang ke Anggota</label>
                        <select name="piutang_penjualan_anggota" id="" class="form-control select2">
                            @foreach ($data['account'] as $value)
                                <option value="{{ $value->code }}" {{ $data['data']['piutang_penjualan_anggota'] == $value->code ? 'selected' : '' }}>[{{ $value->code }}] - {{ $value->name }}</option>
                            @endforeach
                        </select>
                        {!! $errors->first('piutang_penjualan_anggota', '<small class="form-text text-danger">:message</small>') !!}
                        <small class="form-text text-muted">Pilih akun untuk piutang penjualan barang ke anggota.</small>
                    </div>

                    <div class="form-group">
                        <label for="" class="form-label">Akun Piutang Penjualan Barang ke Non Anggota</label>
                        <select name="piutang_penjualan_non_anggota" id="" class="form-control select2">
                            @foreach ($data['account'] as $value)
                                <option value="{{ $value->code }}" {{ $data['data']['piutang_penjualan_non_anggota'] == $value->code ? 'selected' : '' }}>[{{ $value->code }}] - {{ $value->name }}</option>
                            @endforeach
                        </select>
                        {!! $errors->first('piutang_penjualan_non_anggota', '<small class="form-text text-danger">:message</small>') !!}
                        <small class="form-text text-muted">Pilih akun untuk piutang penjualan barang ke non anggota.</small>
                    </div>

                    <div class="form-group">
                        <label for="" class="form-label">Rekening Simpati Kopti Anggota 1</label>
                        <select name="rek_simpati_kopti1" id="" class="form-control select2">
                            @foreach ($data['deposit'] as $value)
                                <option value="{{ $value->id }}" {{ $data['data']['rek_simpati_kopti1'] == $value->id ? 'selected' : '' }}>{{ $value->account_number }}</option>
                            @endforeach
                        </select>
                        {!! $errors->first('rek_simpati_kopti1', '<small class="form-text text-danger">:message</small>') !!}
                        <small class="form-text text-muted">Pilih rekening simpanan kopti untuk keperluan pemotongan penjualan anggota 1.</small>
                    </div>

                    <div class="form-group">
                        <label for="" class="form-label">Rekening Simpati Kopti Anggota 2</label>
                        <select name="rek_simpati_kopti2" id="" class="form-control select2">
                            @foreach ($data['deposit'] as $value)
                                <option value="{{ $value->id }}" {{ $data['data']['rek_simpati_kopti2'] == $value->id ? 'selected' : '' }}>{{ $value->account_number }}</option>
                            @endforeach
                        </select>
                        {!! $errors->first('rek_simpati_kopti2', '<small class="form-text text-danger">:message</small>') !!}
                        <small class="form-text text-muted">Pilih rekening simpanan kopti untuk keperluan pemotongan penjualan anggota 2.</small>
                    </div>

                    <div class="form-group" style="display: none;">
                        <label for="" class="form-label">Rekening Simpati Kopti Non Anggota</label>
                        <select name="rek_simpati_kopti3" id="" class="form-control select2">
                            @foreach ($data['deposit'] as $value)
                                <option value="{{ $value->id }}" {{ $data['data']['rek_simpati_kopti3'] == $value->id ? 'selected' : '' }}>{{ $value->account_number }}</option>
                            @endforeach
                        </select>
                        {!! $errors->first('rek_simpati_kopti3', '<small class="form-text text-danger">:message</small>') !!}
                        <small class="form-text text-muted">Pilih rekening simpanan kopti untuk keperluan pemotongan penjualan non anggota.</small>
                    </div>

                    <div class="form-group">
                        <label for="" class="form-label">Akun Retur Penjualan Anggota</label>
                        <select name="akun_retur_penjualan_anggota" id="" class="form-control select2">
                            @foreach ($data['account'] as $value)
                                <option value="{{ $value->code }}" {{ $data['data']['akun_retur_penjualan_anggota'] == $value->code ? 'selected' : '' }}>[{{ $value->code }}] - {{ $value->name }}</option>
                            @endforeach
                        </select>
                        {!! $errors->first('akun_retur_penjualan_anggota', '<small class="form-text text-danger">:message</small>') !!}
                        <small class="form-text text-muted">Pilih akun untuk retur penjualan barang dari anggota.</small>
                    </div>

                    <div class="form-group">
                        <label for="" class="form-label">Akun Retur Penjualan Non Anggota</label>
                        <select name="akun_retur_penjualan_non_anggota" id="" class="form-control select2">
                            @foreach ($data['account'] as $value)
                                <option value="{{ $value->code }}" {{ $data['data']['akun_retur_penjualan_non_anggota'] == $value->code ? 'selected' : '' }}>[{{ $value->code }}] - {{ $value->name }}</option>
                            @endforeach
                        </select>
                        {!! $errors->first('akun_retur_penjualan_non_anggota', '<small class="form-text text-danger">:message</small>') !!}
                        <small class="form-text text-muted">Pilih akun untuk retur penjualan barang dari non anggota.</small>
                    </div>

                    <div class="form-group">
                        <label for="" class="form-label">Akun Biaya Stock Opname Pusat</label>
                        <select name="akun_so_pusat" id="" class="form-control select2">
                            @foreach ($data['account'] as $value)
                                <option value="{{ $value->code }}" {{ $data['data']['akun_so_pusat'] == $value->code ? 'selected' : '' }}>[{{ $value->code }}] - {{ $value->name }}</option>
                            @endforeach
                        </select>
                        {!! $errors->first('akun_so_pusat', '<small class="form-text text-danger">:message</small>') !!}
                        <small class="form-text text-muted">Pilih akun biaya untuk stock opname penyusutan pusat.</small>
                    </div>

                    <div class="form-group">
                        <label for="" class="form-label">Akun Biaya Stock Opname Gudang</label>
                        <select name="akun_so_gudang" id="" class="form-control select2">
                            @foreach ($data['account'] as $value)
                                <option value="{{ $value->code }}" {{ $data['data']['akun_so_gudang'] == $value->code ? 'selected' : '' }}>[{{ $value->code }}] - {{ $value->name }}</option>
                            @endforeach
                        </select>
                        {!! $errors->first('akun_so_gudang', '<small class="form-text text-danger">:message</small>') !!}
                        <small class="form-text text-muted">Pilih akun biaya untuk stock opname penyusutan gudang.</small>
                    </div>

                    <div class="form-group">
                        <label for="" class="form-label">Akun Retur Pembelian</label>
                        <select name="akun_retur_pembelian" id="" class="form-control select2">
                            @foreach ($data['account'] as $value)
                                <option value="{{ $value->code }}" {{ $data['data']['akun_retur_pembelian'] == $value->code ? 'selected' : '' }}>[{{ $value->code }}] - {{ $value->name }}</option>
                            @endforeach
                        </select>
                        {!! $errors->first('akun_retur_pembelian', '<small class="form-text text-danger">:message</small>') !!}
                        <small class="form-text text-muted">Pilih akun untuk retur pembelian.</small>
                    </div>

                    <div class="form-group">
                        <label for="" class="form-label">Akun Susut Pembelian</label>
                        <select name="akun_susut_pembelian" id="" class="form-control select2">
                            @foreach ($data['account'] as $value)
                                <option value="{{ $value->code }}" {{ $data['data']['akun_susut_pembelian'] == $value->code ? 'selected' : '' }}>[{{ $value->code }}] - {{ $value->name }}</option>
                            @endforeach
                        </select>
                        {!! $errors->first('akun_susut_pembelian', '<small class="form-text text-danger">:message</small>') !!}
                        <small class="form-text text-muted">Pilih akun untuk susut pembelian.</small>
                    </div>

                    <input type="hidden" name="akun_persediaan" value="{{ $data['data']['akun_persediaan'] }}">

                    <div class="form-group">
                        <label class="form-label"><b>Catatan :</b> Field yang diberi tanda bintang (*) <b>harus diisi.</b></label>
                    </div>

                </div>
            </div>
        </div>
        <div class="card-footer text-center">
            <button type="submit" class="btn btn-dark" value="submit" data-toggle="tooltip" data-state="dark" title="Update">Update</button>
        </div>
    </form>
</div>
@endsection

@section('scripts')
@endsection