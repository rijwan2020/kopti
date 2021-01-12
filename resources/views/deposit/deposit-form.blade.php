@extends('layouts.application')

@section('module', 'Simpanan')

@section('content')
<div class="card">
    <div class="card-header h4 text-center">Form Data Simpanan</div>
    <form action="{{ route('depositSave') }}" enctype="multipart/form-data" class="form-input" method="post" autocomplete="false" id="form-tambah-simpanan">
        <div class="card-body">
            @csrf
            <input type="hidden" name="mode" value="{{$data['mode']}}">
            @if ($data['mode']=='edit')
                <input type="hidden" name="id" value="{{$data['data']->id}}"> 
            @endif
            <div class="row">
                <div class="col-xl-10 offset-xl-1">

                    <div class="form-group">
                        <label class="form-label">Nama Penyimpan *</label>
                        <select name="member_id" class="form-control member {{ $errors->has('member_id')?' has-danger':'' }}" required>
                        </select>
                        <small class="form-text text-muted">Ketikan kode atau nama anggota.</small>
                        {!! $errors->first('member_id', '<small class="text-danger">:message</small>') !!}
                    </div>

                    <div class="form-group">
                        <label class="form-label">Jenis Simpanan *</label>
                        <select class="form-control select2 {{ $errors->has('deposit_type_id')?' is-invalid':'' }}" name="deposit_type_id" required>
                            @foreach ($data['type'] as $value)
                                <option value="{{ $value->id }}" {{ $value->id == old('deposit_type_id') || $value->id == $data['data']['deposit_type_id'] ? 'selected' : '' }}>{{ $value->name }}</option>
                            @endforeach
                        </select>
                        {!! $errors->first('deposit_type_id', '<small class="form-text text-danger">:message</small>') !!}
                        <small class="form-text text-muted">Pilih jenis simpanan.</small>
                    </div>

                    <div class="form-group">
                        <label class="form-label">No Rekening *</label>
                        <input type="text" class="form-control {{ $errors->has('account_number')?' is-invalid':'' }}" placeholder="No Rekening" name="account_number" id="account_number" value="{{ old('account_number') ?? $data['data']['account_number'] ?? '' }}" required>
                        {!! $errors->first('account_number', '<small class="form-text text-danger">:message</small>') !!}
                        <small class="form-text text-muted">Masukan no rekening simpanan.</small>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Saldo *</label>
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text">Rp</span>
                            </div>
                            <input type="text" class="form-control {{ $errors->has('beginning_balance')?' is-invalid':'' }} money-without-separator" placeholder="Saldo" name="beginning_balance" id="beginning_balance" value="{{ old('beginning_balance') ??  0 }}" required>
                        </div>
                        {!! $errors->first('beginning_balance', '<small class="form-text text-danger">:message</small>') !!}
                        <small class="form-text text-muted">Masukan saldo awal.</small>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Tanggal Registrasi *</label>
                        <div class="input-group">
                            <div class="input-group-prepend"><span class="input-group-text"><i class="fa fa-calendar"></i></span></div>
                            <input type="text" class="form-control datepicker {{ $errors->has('registration_date')?' is-invalid':'' }} " name="registration_date" value="{{ old('registration_date') ?? date('Y-m-d') }}">
                        </div>
                        {!! $errors->first('registration_date', '<small class="form-text text-danger">:message</small>') !!}
                        <small class="form-text text-muted">Masukan tanggal registrasi pembuatan simpanan.</small>
                    </div>

                    <div class="form-group" id="principal_balance" style="display: none">
                        <label class="form-label">Besar Simpanan Pokok*</label>
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text">Rp</span>
                            </div>
                            <input type="text" class="form-control {{ $errors->has('principal_balance')?' is-invalid':'' }} money-without-separator" placeholder="Saldo" name="principal_balance" value="{{ old('principal_balance') ?? number_format(config('config_apps.besar_sp')) ?? 0 }}">
                        </div>
                        {!! $errors->first('principal_balance', '<small class="form-text text-danger">:message</small>') !!}
                        <small class="form-text text-muted">Masukan Besar Simpanan Pokok koperasi.</small>
                    </div>

                    {{-- <div class="form-group" id="obligatory_balance" style="display: none">
                        <label class="form-label">Besar Simpanan Wajib per bulan*</label>
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text">Rp</span>
                            </div>
                            <input type="text" class="form-control {{ $errors->has('obligatory_balance')?' is-invalid':'' }} money-without-separator" placeholder="Saldo" name="obligatory_balance" value="{{ old('obligatory_balance') ?? number_format(config('config_apps.besar_sw')) ?? 0 }}">
                        </div>
                        {!! $errors->first('obligatory_balance', '<small class="form-text text-danger">:message</small>') !!}
                        <small class="form-text text-muted">Masukan Besar Simpanan Wajib per bulan untuk anggota.</small>
                    </div> --}}

                    <div class="form-group">
                        <label class="form-label">Transaksi Ke *</label>
                        <select class="form-control select2 {{ $errors->has('account')?' is-invalid':'' }}" name="account" required>
                            @foreach ($data['account'] as $value)
                                <option value="{{ $value->code }}" {{ $value->code == old('account') ? 'selected' : '' }}>[{{ $value->code }}] - {{ $value->name }}</option>
                            @endforeach
                        </select>
                        {!! $errors->first('account', '<small class="form-text text-danger">:message</small>') !!}
                        <small class="form-text text-muted">Pilih akun untuk penjurnalan.</small>
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
<script>
    $(document).ready(function() {
        $('.member').select2({
            placeholder: 'Pilih Anggota',
            ajax: {
                url: '/getMember',
                dataType: 'json',
                delay: 250,
                processResults: function (data) {
                    return {
                        results:  $.map(data, function (item) {
                            return {
                                text: '[' + item.code + '] - ' + item.name,
                                id: item.id
                            }
                        })
                    };
                },
                cache: true
            }
        });
        $('select[name=deposit_type_id]').change(function (e) {
            const id = $(this).val();
            if (id == 1) {
                $('#principal_balance').css("display","block");
				$('#obligatory_balance').css("display","none");
            }else if (id == 2) {
                $('#principal_balance').css("display","none");
				$('#obligatory_balance').css("display","block");
            }else{
                $('#principal_balance').css("display","none");
				$('#obligatory_balance').css("display","none");
            }
            $.ajax({
                url: '/getNoRek/' + id,
                method: 'GET',
                success: function (data) {
                    $('#account_number').val(data);
                }
            });
        });
        $(document).on('submit', '#form-tambah-simpanan', function () {
            var input_is_ok = true;
            $('#beginning_balance').each(function() {
                saldo_awal = $(this).val();
                if (saldo_awal == 0) {
                    input_is_ok = false;
                    alert('Saldo tidak boleh 0!');
                    return false;
                }
            });
            if (!input_is_ok)
                return false;
                
            return true;
        });
    });
</script>
@endsection