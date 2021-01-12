@extends('layouts.application')

@section('module', 'Data Penjualan')

@section('content')
<div class="card">
    <div class="card-header h4 text-center">Konfirmasi Penjualan baru</div>
    <form action="{{ route('saleSave') }}" enctype="multipart/form-data" class="form-input" method="post" autocomplete="false">
        <div class="card-body">
            @csrf
            <input type="hidden" name="no_faktur" value="{{ $data['data']['no_faktur'] }}">
            <input type="hidden" name="tanggal_jual" value="{{ $data['data']['tanggal_jual'] }}">
            <input type="hidden" name="member_id" value="{{ $data['data']['member_id'] }}">
            <input type="hidden" name="note" value="{{ $data['data']['note'] }}">
            <input type="hidden" name="ref_number" value="{{ $data['data']['ref_number'] }}">
            <input type="hidden" name="potongan_simpati1" value="{{ $data['data']['potongan_simpati1'] }}">
            <input type="hidden" name="potongan_simpati2" value="{{ $data['data']['potongan_simpati2'] }}">
            <input type="hidden" name="potongan_simpati3" value="{{ $data['data']['potongan_simpati3'] }}">
            <input type="hidden" name="total_belanja" value="{{ $data['data']['total_belanja'] }}">
            <input type="hidden" name="total_bayar" value="{{ $data['data']['total_bayar'] }}">
            <input type="hidden" name="account" value="{{ $data['data']['account'] }}">
            <input type="hidden" name="barang" value="{{ json_encode($data['data']['barang']) }}">
            <div class="row">
                <div class="col-xl-10 offset-xl-1">

                    <div class="form-group">
                        <label class="form-label">No Faktur</label>
                        <input type="text" class="form-control" value="{{ $data['data']['no_faktur'] }}" disabled>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Tanggal Penjualan</label>
                        <input type="text" class="form-control" value="{{ $data['data']['tanggal_jual'] }}" disabled>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Pembeli</label>
                        <input type="text" class="form-control" value="{{ '['.$data['member']->code.'] - '.$data['member']->name }}" disabled>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Keterangan</label>
                        <input type="text" class="form-control" value="{{ $data['data']['note'] }}" disabled>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Data Barang</label>
                        <table class="table table-bordered">
                            <thead class="thead-light">
                                <tr class="text-center">
                                    <th>#</th>
                                    <th>Kode Barang</th>
                                    <th>Nama Barang</th>
                                    <th>Qty (Kg)</th>
                                    <th>Harga (Rp/Kg)</th>
                                    <th>Harga Total (Rp)</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php
                                    $i = $total_penjualan = 0;
                                    $utang = $data['data']['total_belanja'] - $data['data']['total_bayar'];
                                @endphp
                                @foreach ($data['data']['barang'] as $value)
                                    @php
                                        $i++;
                                        $total_penjualan += $value['harga_total_satuan'];
                                        $harga_jual = str_replace(',', '', $value['harga_jual']);
                                    @endphp
                                    <tr>
                                        <td>{{ $i }}</td>
                                        <td>{{ $value['code'] }}</td>
                                        <td>{{ $value['name'] }}</td>
                                        <td>{{ $value['qty'] }}</td>
                                        <td class="text-right">{{ number_format($harga_jual, 2, ',', '.') }}</td>
                                        <td class="text-right">{{ number_format($value['harga_total_satuan'], 2, ',', '.') }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                            <tfoot>
                                <tr class="text-right">
                                    <th colspan="5">Total Penjualan</th>
                                    <th>{{ number_format($total_penjualan, 2, ',', '.') }}</th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Total Belanja</label>
                        <input type="text" class="form-control" value="Rp{{ number_format($total_penjualan, 2, ',', '.') }}" disabled>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Jumlah Bayar</label>
                        <input type="text" class="form-control" value="Rp{{ number_format($data['data']['total_bayar'], 2, ',', '.') }}" disabled>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Potongan ke Simpati Anggota 1</label>
                        <input type="text" class="form-control" value="Rp{{ number_format($data['data']['potongan_simpati1'], 2, ',', '.') }}" disabled>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Potongan ke Simpati Anggota 2</label>
                        <input type="text" class="form-control" value="Rp{{ number_format($data['data']['potongan_simpati2'], 2, ',', '.') }}" disabled>
                    </div>

                    <div class="form-group" style="display: none;">
                        <label class="form-label">Potongan ke Simpati Non Anggota</label>
                        <input type="text" class="form-control" value="Rp{{ number_format($data['data']['potongan_simpati3'], 2, ',', '.') }}" disabled>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Status</label>
                        <input type="text" class="form-control" value="{{ $utang > 0 ? 'Belum Lunas' : 'Lunas' }}" disabled>
                    </div>

                    @if ($utang > 0)
                        <div class="form-group">
                            <label class="form-label">Jumlah Piutang</label>
                            <input type="text" class="form-control" value="Rp{{ number_format($utang, 2, ',', '.') }}" disabled>
                        </div>
                    @else
                        <div class="form-group">
                            <label class="form-label">Kembalian</label>
                            <input type="text" class="form-control" value="Rp{{ number_format($utang*-1, 2, ',', '.') }}" disabled>
                        </div>
                    @endif

                    <div class="form-group">
                        <label class="form-label">Akun Transaksi</label>
                        <input type="text" class="form-control" value="{{ '['.$data['account']->code.'] - '.$data['account']->name }}" disabled>
                    </div>

                </div>
            </div>
        </div>
        <div class="card-footer text-center">
            <button type="submit" class="btn btn-dark" value="submit" data-toggle="tooltip" data-state="dark" title="Simpan">Save</button>
            <a href="{{ route('saleAdd') }}" class="btn btn-danger" title="Batalkan transaksi">Batal</a>
        </div>
    </form>
</div>
@endsection
@section('scripts')
    
@endsection