@extends('layouts.application')

@section('module', 'Simpanan')
@section('content')
<div class="card">
    <div class="card-header h4 text-center">Form Transaksi Simpanan</div>
    <form action="{{ route('depositDetailPreview', ['id' => $data['data']->id]) }}" enctype="multipart/form-data" class="form-input" method="post" autocomplete="false" id="form-transaksi-simpanan">
        <div class="card-body">
            @csrf
            <input type="hidden" name="deposit_id" value="{{ $data['data']->id }}">
            <div class="row">
                <div class="col-xl-10 offset-xl-1">

                    <div class="form-group">
                        <div class="row">
                            <div class="col-md-4">
                                <div class="d-flex justify-content-between">
                                    <div>Kode Anggota</div>
                                    <label class="form-label">{{ $data['data']->member->code }}</label>
                                </div>
                                <div class="d-flex justify-content-between">
                                    <div>Nama Anggota</div>
                                    <label class="form-label">{{ $data['data']->member->name }}</label>
                                </div>
                                <div class="d-flex justify-content-between">
                                    <div>No Rekening</div>
                                    <label class="form-label">{{ $data['data']->account_number }}</label>
                                </div>
                                <div class="d-flex justify-content-between">
                                    <div>Jenis Simpanan</div>
                                    <label class="form-label">{{ $data['data']->type->name }}</label>
                                </div>
                            </div>
                            <div class="col-md-8 text-right">
                                <h2 class="mb-1">Saldo: <strong>Rp{{ number_format($data['data']->balance, 2, ',', '.') }}</strong></h2>
                                @if ($data['data']->deposit_type_id == 1)
                                    <h4 class="mb-1">Besar SP Koperasi: <strong>Rp{{ number_format(config('config_apps.besar_sp'), 2, ',', '.') }}</strong></h4>
                                @endif
                                @if ($data['data']->deposit_type_id == 2)
                                    <h4 class="mb-1">Besar SW per Bulan: <strong>Rp{{ number_format(config('config_apps.besar_sw'), 2, ',', '.') }}</strong></h4>
                                @endif
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Tanggal Transaksi *</label>
                        <div class="input-group">
                            <div class="input-group-prepend"><span class="input-group-text"><i class="fa fa-calendar"></i></span></div>
                            <input type="text" class="form-control datepicker {{ $errors->has('transaction_date')?' is-invalid':'' }} " name="transaction_date" value="{{ old('transaction_date') ?? date('Y-m-d') }}">
                        </div>
                        {!! $errors->first('transaction_date', '<small class="form-text text-danger">:message</small>') !!}
                        <small class="form-text text-muted">Masukan tanggal transaksi.</small>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Jenis Transaksi *</label>
                        <select class="form-control select2 {{ $errors->has('type')?' is-invalid':'' }}" name="type" required>
                            @foreach ($data['type_transaction'] as $key => $value)
                                <option value="{{ $key }}" {{ $key == old('type') }}>[{{ str_pad($key, 2, 0, STR_PAD_LEFT) }}] - {{ $value }}</option>
                            @endforeach
                        </select>
                        {!! $errors->first('type', '<small class="form-text text-danger">:message</small>') !!}
                        <small class="form-text text-muted">Pilih jenis transaksi.</small>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Jumlah *</label>
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text">Rp</span>
                            </div>
                            <input type="text" class="form-control {{ $errors->has('balance')?' is-invalid':'' }} money-with-separator" placeholder="Saldo" name="balance" id="balance" value="{{ old('balance') ?? number_format($data['balance'], 2) ?? 0 }}" required>
                        </div>
                        {!! $errors->first('balance', '<small class="form-text text-danger">:message</small>') !!}
                        <small class="form-text text-muted">Masukan saldo yang disetor / ditarik.</small>
                    </div>

                    {{-- @if ($data['data']->deposit_type_id == 2)
                        <div class="form-group" id="bulan">
                            <label class="form-label">Jumlah Bulan *</label>
                            <input type="number" class="form-control {{ $errors->has('month')?' is-invalid':'' }}" placeholder="Jumlah Bulan" name="month" id="month" value="{{ old('month') ?? 1 }}" required>
                            {!! $errors->first('month', '<small class="form-text text-danger">:message</small>') !!}
                            <small class="form-text text-muted">Masukan jumlah bulan.</small>
                        </div>
                    @endif --}}

                    <div class="form-group">
                        <label class="form-label">No Referensi / No Bukti *</label>
                        <input type="text" class="form-control {{ $errors->has('reference_number')?' is-invalid':'' }}" placeholder="No Referensi / No Bukti" name="reference_number" id="reference_number" value="{{ old('reference_number') ?? 'TRXS-'.date('YmdHis') }}" required>
                        {!! $errors->first('reference_number', '<small class="form-text text-danger">:message</small>') !!}
                        <small class="form-text text-muted">Masukan no referensi / no bukti transaksi.</small>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Pilih Akun Penjurnalan *</label>
                        <select class="form-control select2 {{ $errors->has('account')?' is-invalid':'' }}" name="account" required>
                            @foreach ($data['cash'] as $key => $value)
                                <option value="{{ $value->code }}" {{ $value->code == old('account') }}>[{{ $value->code }}] - {{ $value->name }}</option>
                            @endforeach
                        </select>
                        {!! $errors->first('account', '<small class="form-text text-danger">:message</small>') !!}
                        <small class="form-text text-muted">Pilih akun untuk transaksi di jurnal.</small>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Keterangan transaksi </label>
                        <input type="text" class="form-control {{ $errors->has('name')?' is-invalid':'' }}" placeholder="Keterangan transaksi" name="note" id="note" value="{{ old('note') ?? '' }}" >
                        {!! $errors->first('note', '<small class="form-text text-danger">:message</small>') !!}
                        <small class="form-text text-muted">Masukan keterangan transaksi.</small>
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
        $(document).on('submit', '#form-transaksi-simpanan', function () {
            var input_is_ok = true;
            $('#balance').each(function() {
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
        @if($data['data']->deposit_type_id == 2)
            $('select[name=type]').change(function (e) {
                const id = $(this).val();
                if (id == 1) {
                    $('#bulan').css("display","block");
                    $('#balance').attr('readonly','');
                    $('#balance').attr('value',"{{number_format($data['balance'],2)}}");
                }else{
                    $('#bulan').css("display","none");
                    $('#balance').removeAttr('readonly','');
                    $('#balance').attr('value','0');
                }
            });
        @endif
    });
</script>
@endsection