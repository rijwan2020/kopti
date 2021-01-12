@extends('layouts.application')

@section('module', 'Simpanan')
@section('content')
<div class="card">
    <div class="card-header h4 text-center">Form Transaksi Simpanan</div>
    <form action="{{ route('depositDetailSave', ['id' => $data['deposit']->id]) }}" enctype="multipart/form-data" class="form-input" method="post" autocomplete="false" id="form-transaksi-simpanan">
        <div class="card-body">
            @csrf
            <input type="hidden" name="deposit_id" value="{{ $data['data']['deposit_id'] }}">
            <input type="hidden" name="transaction_date" value="{{ $data['data']['transaction_date'].date(' H:i:s') }}">
            <input type="hidden" name="type" value="{{ $data['data']['type'] }}">
            <input type="hidden" name="debit" value="{{ $data['data']['debit'] }}">
            <input type="hidden" name="kredit" value="{{ $data['data']['kredit'] }}">
            <input type="hidden" name="reference_number" value="{{ $data['data']['reference_number'] }}">
            <input type="hidden" name="account" value="{{ $data['data']['account'] }}">
            <input type="hidden" name="note" value="{{ $data['data']['note'] }}">
            <div class="row">
                <div class="col-xl-10 offset-xl-1">

                    <div class="form-group">
                        <div class="row">
                            <div class="col-md-4">
                                <div class="d-flex justify-content-between">
                                    <div>Kode Anggota</div>
                                    <label class="form-label">{{ $data['deposit']->member->code }}</label>
                                </div>
                                <div class="d-flex justify-content-between">
                                    <div>Nama Anggota</div>
                                    <label class="form-label">{{ $data['deposit']->member->name }}</label>
                                </div>
                                <div class="d-flex justify-content-between">
                                    <div>No Rekening</div>
                                    <label class="form-label">{{ $data['deposit']->account_number }}</label>
                                </div>
                                <div class="d-flex justify-content-between">
                                    <div>Jenis Simpanan</div>
                                    <label class="form-label">{{ $data['deposit']->type->name }}</label>
                                </div>
                            </div>
                            <div class="col-md-8 text-right">
                                <h2 class="mb-1">Saldo: <strong>Rp{{ number_format($data['deposit']->balance, 2, ',', '.') }}</strong></h2>
                                @if ($data['deposit']->deposit_type_id == 1)
                                    <h4 class="mb-1">Besar SP Koperasi: <strong>Rp{{ number_format($data['deposit']->principal_balance, 2, ',', '.') }}</strong></h4>
                                @endif
                                @if ($data['deposit']->deposit_type_id == 2)
                                    <h4 class="mb-1">Besar SW per Pulan: <strong>Rp{{ number_format($data['deposit']->obligatory_balance, 2, ',', '.') }}</strong></h4>
                                @endif
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Tanggal Transaksi</label>
                        <input type="text" class="form-control" value="{{ $data['data']['transaction_date'] }}" disabled>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Jenis Transaksi</label>
                        <input type="text" class="form-control" value="[{{ str_pad($data['data']['type'], 2, 0, STR_PAD_LEFT) }}] - {{ $data['type_transaction'][$data['data']['type']] }}" disabled>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Jumlah</label>
                        <input type="text" class="form-control" value="Rp{{ number_format($data['data']['debit'] > 0 ? $data['data']['debit'] : $data['data']['kredit'], 2, ',', '.') }}" disabled>
                    </div>

                    {{-- @if ($data['deposit']->deposit_type_id == 2 && $data['data']['type'] == 1)
                        <div class="form-group">
                            <label class="form-label">Jumlah Bulan</label>
                            <input type="text" class="form-control" value="{{ $data['data']['month'] }} Bulan" disabled>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Total Bayar</label>
                            <input type="text" class="form-control" value="Rp{{ number_format($data['data']['kredit']*$data['data']['month'],2,',','.') }}" disabled>
                        </div>
                        <input type="hidden" name="month" value="{{ $data['data']['month'] }}">
                    @endif --}}

                    <div class="form-group">
                        <label class="form-label">No Referensi / No Bukti</label>
                        <input type="text" class="form-control" value="{{ $data['data']['reference_number'] }}" disabled>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Akun Jurnal</label>
                        <input type="text" class="form-control" value="[{{ $data['data']['account'] }}] - {{ $data['account']->name }}" disabled>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Keterangan</label>
                        <input type="text" class="form-control" value="{{ $data['data']['note'] }}" disabled>
                    </div>

                </div>
            </div>
        </div>
        <div class="card-footer text-center">
            <button type="submit" class="btn btn-dark" value="submit" data-toggle="tooltip" data-state="dark" title="Simpan">Konfirmasi</button>
            <a href="{{ route('depositDetailAdd', ['id'=>$data['data']['deposit_id']]) }}" class="btn btn-danger" title="Batalkan transaksi">
                Batalkan
            </a>
        </div>
    </form>
</div>
@endsection