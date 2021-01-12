@extends('layouts.application')

@section('module', 'Data Penjualan Barang')

@section('content')
<div class="row mb-3">
    <div class="col-md-1"></div>
    <div class="col-md-10 text-center">
        <h3>BUKTI PENJUALAN BARANG</h3>
    </div>
    <div class="col-md-1 text-right">
        @if (Auth::user()->hasRule('salePrint'))
            <a href="{{ route('salePrint', ['id' => $data['data']->id]) }}" class="btn my-1 btn-dark" data-toggle="tooltip" data-state="dark" title="Print Transaksi" target="_blank">
                <i class="fa fa-print"></i>
                Print
            </a>
        @endif
    </div>
</div>

<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-body">
                <div class="row">
                    <div class="col-md-4">
                        <div class="d-flex justify-content-between mb-0">
                            <div>Pembeli</div>
                            <b>[{{ $data['data']->member->code }}] - {{ $data['data']->member->name }}</b>
                        </div>
                        <div class="d-flex justify-content-between mb-0">
                            <div>Alamat</div>
                            <b> {{ $data['data']->member->region->name }}</b>
                        </div>
                        <div class="d-flex justify-content-between mb-0">
                            <div>Keterangan</div>
                            <b> {{ $data['data']->note }}</b>
                        </div>
                    </div>
                    <div class="col-md-4 offset-md-4 text-right">
                        <div class="d-flex justify-content-between mb-0">
                            <div>No Faktur</div>
                            <b> {{ $data['data']->no_faktur }}</b>
                        </div>
                        <div class="d-flex justify-content-between mb-0">
                            <div>Tanggal Transaksi</div>
                            <b> {{ $data['data']->tanggal_jual }}</b>
                        </div>
                        <div class="d-flex justify-content-between mb-0">
                            <div>Transaksi di</div>
                            <b> {{ $data['data']->warehouse->name ?? 'Pusat' }}</b>
                        </div>
                    </div>
                </div>
            </div>
            <div class="table-responsive">
                <table class="table card-table table-bordered">
                    <thead class="thead-light">
                        <tr class="text-center">
                            <th>#</th>
                            <th>Kode Barang</th>
                            <th>Nama Barang</th>
                            <th>Qty (Kg)</th>
                            <th>Harga Satuan (Rp)</th>
                            <th>Total (Rp)</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                            $i = 0;
                        @endphp
                        @foreach ($data['data']->detail as $value)
                            @php
                                $i++;
                            @endphp
                            <tr>
                                <td class="text-center">{{ $i }}</td>
                                <td>{{ $value->item->code }}</td>
                                <td>{{ $value->item->name }}</td>
                                <td class="text-right">
                                    @php
                                    if (fmod($value->qty, 1) !== 0.00) {
                                        echo number_format($value->qty, 2, ',', '.');
                                    }else{
                                        echo number_format($value->qty);
                                    }
                                    if ($value->qty_retur > 0) {
                                        echo "<br><small>(Retur : ".(fmod($value->qty_retur, 1) !== 0.00 ? number_format($value->qty_retur, 2, ',', '.') : number_format($value->qty_retur)).")</small>";
                                    }
                                    @endphp
                                </td>
                                <td class="text-right">{{ number_format($value->harga_jual,2, ',', '.') }}</td>
                                <td class="text-right">
                                    {{ number_format($value->harga_total_satuan,2, ',', '.') }}
                                    @if ($value->qty_retur > 0)
                                        <br><small>(Retur : {{ number_format($value->harga_jual * $value->qty_retur,2, ',', '.') }})</small>
                                    @endif
                                </td>
                                <td class="text-center">
                                    @if (Auth::user()->hasRule('saleReturAdd'))
                                        <a href="{{ route('saleReturAdd', ['id' => $value->id]) }}" class="btn icon-btn btn-warning btn-sm" data-toggle="tooltip" data-placement="top" data-state="dark" title="Retur barang">
                                            <i class="fa fa-retweet"></i>
                                        </a>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr class="text-right">
                            <th colspan="5">Jumlah : </th>
                            <th>{{ number_format($data['data']->total_belanja, 2, ',', '.') }}</th>
                            <th></th>
                        </tr>
                    </tfoot>
                </table>
            </div>
            <div class="card-footer">
                <div class="row">
                    <div class="col-md-3">
                        Total Item : <strong>{{ $i }}</strong>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
