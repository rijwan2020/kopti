@extends('layouts.application')

@section('module', 'Data Pembelian Barang')

@section('content')
<div class="card">
    <div class="card-header h4 text-center">Form Data Pembelian Barang</div>
    <form action="{{ route('purchaseSave') }}" enctype="multipart/form-data" class="form-input" method="post" autocomplete="false">
        <div class="card-body">
            @csrf
            <input type="hidden" name="no_faktur" value="{{ $data['data']['no_faktur'] }}">
            <input type="hidden" name="tanggal_beli" value="{{ $data['data']['tanggal_beli'] }}">
            <input type="hidden" name="suplier_id" value="{{ $data['data']['suplier_id'] }}">
            <input type="hidden" name="note" value="{{ $data['data']['note'] }}">
            <input type="hidden" name="ref_number" value="{{ $data['data']['ref_number'] }}">
            <input type="hidden" name="diskon" value="{{ $data['data']['diskon'] }}">
            <input type="hidden" name="total" value="{{ $data['data']['total'] }}">
            <input type="hidden" name="total_bayar" value="{{ $data['data']['total_bayar'] }}">
            <input type="hidden" name="account" value="{{ $data['data']['account'] }}">
            <input type="hidden" name="warehouse_id" value="{{ $data['data']['warehouse_id'] }}">
            <input type="hidden" name="barang" value="{{ json_encode($data['data']['barang']) }}">
            <div class="row">
                <div class="col-xl-10 offset-xl-1">

                    <div class="form-group">
                        <label class="form-label">No Faktur</label>
                        <input type="text" class="form-control" value="{{ $data['data']['no_faktur'] }}" disabled>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Tanggal Beli</label>
                        <input type="text" class="form-control" value="{{ $data['data']['tanggal_beli'] }}" disabled>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Suplier</label>
                        <input type="text" class="form-control" value="{{ '['.$data['suplier']->code.'] - '.$data['suplier']->name }}" disabled>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Keterangan</label>
                        <input type="text" class="form-control" value="{{ $data['data']['note'] }}" disabled>
                    </div>

                    <div class="form-group">
                        <label class="form-label">No Ref</label>
                        <input type="text" class="form-control" value="{{ $data['data']['ref_number'] }}" disabled>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Alokasi Barang ke</label>
                        <input type="text" class="form-control" value="{{ $data['warehouse'] }}" disabled>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Data Barang</label>
                        <table class="table table-bordered">
                            <thead class="thead-light">
                                <tr>
                                    <th>#</th>
                                    <th>Kode Barang</th>
                                    <th>Nama Barang</th>
                                    <th>Qty Bersih (Kg)</th>
                                    <th>Qty Susut (Kg)</th>
                                    <th>Harga Beli/Kg (Rp)</th>
                                    <th>Harga Total (Rp)</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php
                                    $i = $total_beli = 0;
                                    $utang = $data['data']['total'] - $data['data']['diskon'] - $data['data']['total_bayar'];
                                @endphp
                                @foreach ($data['data']['barang'] as $value)
                                    @php
                                        $qty = str_replace(',','',$value['qty']);
                                        $susut = str_replace(',','',$value['susut']);
                                        $harga_satuan = str_replace(',','',$value['harga_beli_satuan']);
                                        $harga_total_satuan = str_replace(',','',$value['harga_total_satuan']);
                                        $i++;
                                        $total_beli += $harga_total_satuan;
                                    @endphp
                                    <tr>
                                        <td>{{ $i }}</td>
                                        <td>{{ $value['code'] }}</td>
                                        <td>{{ $value['name'] }}</td>
                                        <td class="text-right">{{ number_format($qty, 2, ',', '.') }}</td>
                                        <td class="text-right">{{ number_format($susut, 2, ',', '.') }}</td>
                                        <td class="text-right">{{ number_format($harga_satuan, 2, ',', '.') }}</td>
                                        <td class="text-right">{{ number_format($harga_total_satuan, 2, ',', '.') }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                            <tfoot>
                                <tr class="text-right">
                                    <th colspan="6">Total Pembelian</th>
                                    <th>{{ number_format($total_beli, 2, ',', '.') }}</th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Total Pembelian</label>
                        <input type="text" class="form-control" value="Rp{{ number_format($total_beli, 2, ',', '.') }}" disabled>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Total Diskon</label>
                        <input type="text" class="form-control" value="Rp{{ number_format($data['data']['diskon'], 2, ',', '.') }}" disabled>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Total yang Harus Dibayar</label>
                        <input type="text" class="form-control" value="Rp{{ number_format($data['data']['total'] - $data['data']['diskon'], 2, ',', '.') }}" disabled>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Jumlah Bayar</label>
                        <input type="text" class="form-control" value="Rp{{ number_format($data['data']['total_bayar'], 2, ',', '.') }}" disabled>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Status</label>
                        <input type="text" class="form-control" value="{{ $utang > 0 ? 'Belum Lunas' : 'Lunas' }}" disabled>
                    </div>

                    @if ($utang > 0)
                        <div class="form-group">
                            <label class="form-label">Jumlah Utang</label>
                            <input type="text" class="form-control" value="Rp{{ number_format($utang, 2, ',', '.') }}" disabled>
                        </div>
                    @endif

                    <div class="form-group">
                        <label class="form-label">Akun Transaksi</label>
                        <input type="text" class="form-control" value="{{ '['.$data['account']->code.'] - '.$data['account']->name }}" disabled>
                    </div>

                    <div class="form-group">
                        <label class="form-label"><b>Catatan :</b> Field yang diberi tanda bintang (*) <b>harus diisi.</b></label>
                    </div>
                    

                </div>
            </div>
        </div>
        <div class="card-footer text-center">
            <button type="submit" class="btn btn-dark" value="submit" data-toggle="tooltip" data-state="dark" title="Simpan">Save</button>
            <a href="{{ route('purchaseAdd') }}" class="btn btn-danger" title="Batalkan transaksi">Batal</a>
        </div>
    </form>
</div>
@endsection
@section('scripts')
    
@endsection