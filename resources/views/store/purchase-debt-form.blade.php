@extends('layouts.application')

@section('module', 'Data Utang Pembelian')

@section('content')
<div class="card">
    <div class="card-header h4 text-center">Form Pembayaran Utang Pembelian</div>
    <form action="{{ route('purchaseDebtConfirm') }}" enctype="multipart/form-data" class="form-input" method="post" autocomplete="false" id="form-bayar-utang">
        <div class="card-body">
            @csrf
            <input type="hidden" name="id" value="{{$data['data']->id}}">
            <input type="hidden" id="total_utang" value="{{ $data['data']->total - $data['data']->pay }}">
            <div class="row">
                <div class="col-xl-10 offset-xl-1">

                    <div class="form-group">
                        <label class="form-label">No Faktur Pembelian</label>
                        <input type="text" class="form-control" value="{{ $data['data']->purchase->no_faktur }}" disabled>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Suplier</label>
                        <input type="text" class="form-control" value="{{ $data['data']->suplier->name }}" disabled>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Total Utang</label>
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text">Rp</span>
                            </div>
                            <input type="text" class="form-control" value="{{ number_format($data['data']->total - $data['data']->pay, 2, ',', '.') }}" disabled>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="form-label">No Ref / No Bukti *</label>
                        <input type="text" class="form-control {{ $errors->has('ref_number')?' is-invalid':'' }}" name="ref_number" id="ref_number" value="{{ old('ref_number') ?? 'TRXT-'.date('YmdHis') }}" required>
                        {!! $errors->first('ref_number', '<small class="form-text text-danger">:message</small>') !!}
                        <small class="form-text text-muted">Masukan no referensi / no bukti.</small>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Tanggal Transaksi *</label>
                        <input type="text" class="form-control {{ $errors->has('transaction_date')?' is-invalid':'' }} datepicker" name="transaction_date" id="transaction_date" value="{{ old('transaction_date') ?? date('Y-m-d') }}" required>
                        {!! $errors->first('transaction_date', '<small class="form-text text-danger">:message</small>') !!}
                        <small class="form-text text-muted">Masukan no referensi / no bukti.</small>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Total Bayar *</label>
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text">Rp</span>
                            </div>
                            <input type="text" class="form-control {{ $errors->has('pay')?' is-invalid':'' }} money-with-separator" placeholder="Harga Jual" name="pay" id="pay" value="{{ old('pay') ?? number_format($data['data']->total - $data['data']->pay, 2) }}" required>
                        </div>
                        {!! $errors->first('pay', '<small class="form-text text-danger">:message</small>') !!}
                        <small class="form-text text-muted">Masukan besar pembayaran.</small>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Keterangan</label>
                        <input type="text" class="form-control {{ $errors->has('note')?' is-invalid':'' }}" name="note" id="note" value="{{ old('note') ?? '' }}">
                        {!! $errors->first('note', '<small class="form-text text-danger">:message</small>') !!}
                        <small class="form-text text-muted">Masukan keterangan.</small>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Pilih Akun *</label>
                        <select name="account" id="account" class="form-control select2 {{ $errors->has('account')?' is-invalid':'' }}">
                            @foreach ($data['cash'] as $value)
                                <option value="{{ $value->code }}" {{ old('account') == $value->code ? 'selected' : '' }}>[{{ $value->code }}] - {{ $value->name }}</option>
                            @endforeach
                        </select>
                        {!! $errors->first('account', '<small class="form-text text-danger">:message</small>') !!}
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
</div>
@endsection
@section('scripts')
    <script>
        $(document).ready(function(){
            $(document).on('submit', '#form-bayar-utang', function () {
                var returnfalse = false;
                var total_utang = $('#total_utang').val();
                var bayar = deleteSeparator($('#pay').val());

                console.log(total_utang);
                console.log(bayar);
                

                //jika belum menambahkan barang maka tolak
                if (bayar <= 0) {
                    alert('Total bayar tidak boleh kosong !');
                    return false;
                }

                if(bayar > total_utang){
                    alert('Total bayar tidak boleh melebihi total utang !');
                    return false;
                }

                if (returnfalse)
                    return false;
            });
        });
        function deleteSeparator(x){
            return parseFloat(x.replace(/,/g,''));
        }
    </script>
@endsection