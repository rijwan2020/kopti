@extends('layouts.application')

@section('module', 'Data Piutang')

@section('content')
<div class="alert alert-dark-primary alert-dismissible fade show">
    <strong>Info!</strong> 
    Pastikan data telah sesuai, lalu klik tombol konfirmasi.
</div>
<div class="card">
    <div class="card-header h4 text-center">Form Pembayaran Piutang Penjualan</div>
    <form action="{{ route('saleDebtSave') }}" enctype="multipart/form-data" class="form-input" method="post" autocomplete="false" id="form-bayar-utang">
        <div class="card-body">
            @csrf
            <input type="hidden" name="id" value="{{$data['id']}}">
            <input type="hidden" name="ref_number" value="{{$data['ref_number']}}">
            <input type="hidden" name="transaction_date" value="{{$data['transaction_date']}}">
            <input type="hidden" name="pay" value="{{$data['pay']}}">
            <input type="hidden" name="note" value="{{$data['note']}}">
            <input type="hidden" name="account" value="{{$data['account']}}">
            <div class="row">
                <div class="col-xl-10 offset-xl-1">

                    <div class="form-group">
                        <label class="form-label">No Faktur Penjualan</label>
                        <input type="text" class="form-control" value="{{ $data['debt']->sale->no_faktur }}" disabled>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Transaksi di</label>
                        <input type="text" class="form-control" value="{{ $data['debt']->warehouse->name ?? 'Pusat' }}" disabled>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Total Utang</label>
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text">Rp</span>
                            </div>
                            <input type="text" class="form-control" value="{{ number_format($data['debt']->total - $data['debt']->pay, 2, ',', '.') }}" disabled>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="form-label">No Ref / No Bukti</label>
                        <input type="text" class="form-control" value="{{ $data['ref_number'] }}" disabled>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Tanggal Transaksi</label>
                        <input type="text" class="form-control" value="{{ $data['transaction_date'] }}" disabled>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Total Bayar</label>
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text">Rp</span>
                            </div>
                            <input type="text" class="form-control" value="{{ $data['pay'] }}" disabled>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Keterangan</label>
                        <input type="text" class="form-control" value="{{ $data['note'] }}" disabled>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Keterangan</label>
                        <input type="text" class="form-control" value="{{ '['.$data['cash']->code.'] - '.$data['cash']->name }}" disabled>
                    </div>

                </div>
            </div>
        </div>
        <div class="card-footer text-center">
            <button type="submit" class="btn btn-dark" value="submit" data-toggle="tooltip" data-state="dark" title="Simpan">Konfirmasi</button>
            <a href="{{ route('saleDebtPay', ['id'=>$data['id']]) }}" class="btn btn-danger" title="Batalkan transaksi">Batal</a>
        </div>
    </form>
</div>
@endsection