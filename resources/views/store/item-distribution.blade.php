@extends('layouts.application')

@section('module', 'Data Barang')

@section('content')
@if ($data['data']->warehouse_id == 0)
    <div class="card">
        <div class="card-header h4 text-center">Form Distribusi Barang</div>
        <form action="{{ route('itemDistributionSave') }}" enctype="multipart/form-data" class="form-input" method="post" autocomplete="false" id="form-distribusi">
            <div class="card-body">
                @csrf
                <input type="hidden" name="id" value="{{ $data['data']->id }}">
                <input type="hidden" id="qty" value="{{ $data['data']->qty }}">
                <div class="row">
                    <div class="col-xl-10 offset-xl-1">

                        <div class="form-group">
                            <label for="" class="form-label">Barang</label>
                            <input type="text" value="{{ '['.$data['data']->item->code.'] - '.$data['data']->item->name }}" disabled class="form-control">
                        </div>

                        <div class="form-group">
                            <label for="" class="form-label">QTY Tersedia</label>
                            <input type="text" value="{{ fmod($data['data']->qty, 1) !== 0.00 ? number_format($data['data']->qty, 2, ',', '.') : number_format($data['data']->qty) }} Kg" disabled class="form-control">
                        </div>

                        <div class="form-group">
                            <label class="form-label">Tanggal Distribusi *</label>
                            <input type="text" class="form-control {{ $errors->has('tanggal_distribusi')?' is-invalid':'' }} datepicker" placeholder="Tanggal Distribusi" name="tanggal_distribusi" id="tanggal_distribusi" value="{{ old('tanggal_distribusi') ?? $data['data']->tanggal_masuk }}" required>
                            {!! $errors->first('tanggal_distribusi', '<small class="form-text text-danger">:message</small>') !!}
                            <small class="form-text text-muted">Masukan Tanggal Distribusi.</small>
                        </div>

                        <div class="form-group">
                            <div class="text-center"><strong>Distribusi Ke Gudang</strong></div>
                            <table class="table card-table">
                                <thead class="thead-light">
                                    <tr>
                                        <th class="text-center">#</th>
                                        <th>Kode Gudang</th>
                                        <th>Nama Gudang</th>
                                        <th>Jumlah Qty (Kg)</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php
                                        $i = 0;
                                    @endphp
                                    @foreach ($data['warehouse'] as $value)
                                        @php
                                            $i++;
                                        @endphp
                                        <input type="hidden" name="warehouse_id[{{ $value->id }}]" value="{{ $value->id }}">
                                        <tr>
                                            <td>{{ $i }}</td>
                                            <td>{{ $value->code }}</td>
                                            <td>{{ $value->name }}</td>
                                            <td>
                                                <div class="input-group">
                                                    <input type="text" class="form-control text-right input-qty" name="qty[{{ $value->id }}]" value="0">
                                                    <div class="input-group-prepend"><span class="input-group-text"> Kg</span><div>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card-footer text-center">
                <button type="submit" class="btn btn-dark" value="submit" data-toggle="tooltip" data-state="dark" title="Simpan">Save</button>
            </div>
        </form>
    </div>
@else
<div class="card">
    <div class="card-header h4 text-center">Form Pengembalian Distribusi Barang</div>
    <form action="{{ route('itemDistributionSave') }}" enctype="multipart/form-data" class="form-input" method="post" autocomplete="false" id="form-distribusi">
        <div class="card-body">
            @csrf
            <input type="hidden" name="id" value="{{ $data['data']->id }}">
            <input type="hidden" id="qty" value="{{ $data['data']->qty }}">
            <div class="row">
                <div class="col-xl-10 offset-xl-1">

                    <div class="form-group">
                        <label for="" class="form-label">Barang</label>
                        <input type="text" value="{{ '['.$data['data']->item->code.'] - '.$data['data']->item->name }}" disabled class="form-control">
                    </div>

                    <div class="form-group">
                        <label for="" class="form-label">QTY Tersedia</label>
                        <input type="text" value="{{ fmod($data['data']->qty, 1) !== 0.00 ? number_format($data['data']->qty, 2, ',', '.') : number_format($data['data']->qty) }} Kg" disabled class="form-control">
                    </div>

                    <div class="form-group">
                        <label class="form-label">Tanggal Pengembalian *</label>
                        <input type="text" class="form-control {{ $errors->has('tanggal_distribusi')?' is-invalid':'' }} datepicker" placeholder="Tanggal Distribusi" name="tanggal_distribusi" id="tanggal_distribusi" value="{{ old('tanggal_distribusi') ?? $data['data']->tanggal_masuk }}" required>
                        {!! $errors->first('tanggal_distribusi', '<small class="form-text text-danger">:message</small>') !!}
                        <small class="form-text text-muted">Masukan Tanggal Distribusi.</small>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Total Qty *</label>
                        <input type="text" class="form-control {{ $errors->has('qty')?' is-invalid':'' }}"  name="qty" id="total-qty" value="{{ old('qty') ?? 0 }}" required>
                        {!! $errors->first('qty', '<small class="form-text text-danger">:message</small>') !!}
                        <small class="form-text text-muted">Masukan total qty yang dikembalikan ke pusat.</small>
                    </div>

                </div>
            </div>
        </div>
        <div class="card-footer text-center">
            <button type="submit" class="btn btn-dark" value="submit" data-toggle="tooltip" data-state="dark" title="Simpan">Save</button>
        </div>
    </form>
</div>
@endif
@endsection
@section('scripts')
    @if ($data['data']->warehouse_id == 0)
        <script>
            $(document).ready(function(){
                $(document).on('submit', '#form-distribusi', function () {
                    var returnfalse = false;
                    var total = 0;
                    var qty = parseFloat($('#qty').val());

                    $('.input-qty').each(function (i) {
                        total += parseFloat($(this).val());
                    });
                    if (total == 0) {
                        alert('Jumlah Qty pada gudang harus diisi minimal satu gudang');
                        return false;
                    }
                    if (total > qty) {
                        alert('Jumlah qty melebihi persediaan pusat.');
                        return false;
                    }

                    if (returnfalse)
                        return false;
                });
            });
        </script>
    @else
        <script>
            $(document).ready(function(){
                $(document).on('submit', '#form-distribusi', function () {
                    var returnfalse = false;
                    var total = $('#total-qty').val();
                    var qty = parseFloat($('#qty').val());

                    if (total == 0) {
                        alert('Total Qty tidak boleh kosong');
                        return false;
                    }
                    if (total > qty) {
                        alert('Total qty melebihi persediaan gudang.');
                        $('#total-qty').val(qty);
                        return false;
                    }

                    if (returnfalse)
                        return false;
                });
            });
        </script>
    @endif
@endsection