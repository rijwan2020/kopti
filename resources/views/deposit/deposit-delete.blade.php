@extends('layouts.application')

@section('module', 'Simpanan')
@section('content')
<div class="card">
    <div class="card-header h4 text-center">Form Delete Simpanan</div>
    <form action="{{ route('depositDeleteConfirm', ['id' => $data['data']->id]) }}" enctype="multipart/form-data" class="form-input" method="post" autocomplete="false" id="form-transaksi-simpanan">
        <div class="card-body">
            @csrf
            <input type="hidden" name="deposit_id" value="{{ $data['data']->id }}">
            <div class="row">
                <div class="col-xl-10 offset-xl-1">

                    <div class="form-group">
                        <label class="form-label">Tanggal Penghapusan / Penutupan *</label>
                        <div class="input-group">
                            <div class="input-group-prepend"><span class="input-group-text"><i class="fa fa-calendar"></i></span></div>
                            <input type="text" class="form-control datepicker {{ $errors->has('transaction_date')?' is-invalid':'' }} " name="transaction_date" value="{{ old('transaction_date') ?? date('Y-m-d') }}">
                        </div>
                        {!! $errors->first('transaction_date', '<small class="form-text text-danger">:message</small>') !!}
                        <small class="form-text text-muted">Masukan tanggal penghapusan / penutupan rekening.</small>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Saldo Simpanan</label>
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text">Rp</span>
                            </div>
                            <input type="text" class="form-control" value="{{ number_format($data['data']->balance, 2, ',', '.') }}" disabled>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Jenis *</label>
                        <div>
                            <label class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="type" value="0" {{ old('type') == 0 ? 'checked' : ''}}>
                                <span class="form-check-label">Penghapusan</span>
                            </label>
                            <label class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="type" value="1" {{ old('type') == 1 ? 'checked' : ''}}>
                                <span class="form-check-label">Penutupan</span>
                            </label>
                        </div>
                        <small class="form-text text-muted">Pilih <b>Penutupan</b> agar saldo ditransaksikan ke jurnal.</small>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Pilih Akun Penjurnalan *</label>
                        <select class="form-control select2 {{ $errors->has('account')?' is-invalid':'' }}" name="account" required>
                            @foreach ($data['cash'] as $key => $value)
                                <option value="{{ $value->code }}" {{ $value->code == old('account') }}>[{{ $value->code }}] - {{ $value->name }}</option>
                            @endforeach
                        </select>
                        {!! $errors->first('account', '<small class="form-text text-danger">:message</small>') !!}
                        <small class="form-text text-muted">Pilih akun untuk transaksi di jurnal jika tabungan ditutup.</small>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Keterangan </label>
                        <input type="text" class="form-control {{ $errors->has('name')?' is-invalid':'' }}" placeholder="Keterangan" name="note" id="note" value="{{ old('note') ?? '' }}" >
                        {!! $errors->first('note', '<small class="form-text text-danger">:message</small>') !!}
                        <small class="form-text text-muted">Masukan keterangan.</small>
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
    });
</script>
@endsection