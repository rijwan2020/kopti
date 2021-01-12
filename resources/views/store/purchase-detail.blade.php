@extends('layouts.application')

@section('module', 'Data Pembelian Barang')

@section('content')
<div class="row mb-3">
    <div class="col-md-12">
        <div class="card">
            <div class="card-body">
                <div class="row">
                    <div class="col-md-4">
                        <div class="d-flex justify-content-between mb-0">
                            <div>No Faktur</div>
                            <b> {{ $data['data']->no_faktur }}</b>
                        </div>
                        <div class="d-flex justify-content-between mb-0">
                            <div>Tanggal Transaksi</div>
                            <b> {{ $data['data']->tanggal_beli }}</b>
                        </div>
                        <div class="d-flex justify-content-between mb-0">
                            <div>Suplier</div>
                            <b> {{ $data['data']->suplier->name }}</b>
                        </div>
                        <div class="d-flex justify-content-between mb-0">
                            <div>Keterangan</div>
                            <b> {{ $data['data']->note }}</b>
                        </div>
                        <div class="d-flex justify-content-between mb-0">
                            <div>Status</div>
                            <b> {{ $data['data']->status == 0 ? 'Belum Lunas' : 'Lunas' }}</b>
                        </div>
                    </div>
                    <div class="col-md-8 text-right">
                        <h5 class="mb-1">
                            Total Pembelian: Rp{{ number_format($data['data']->total, 2, ',', '.') }}
                        </h5>
                        <h6 class="mb-1">
                            Diskon: Rp{{ number_format($data['data']->diskon, 2, ',', '.') }}
                        </h6>
                        <h5 class="mb-1">
                            Total Bayar: Rp{{ number_format($data['data']->total_bayar, 2, ',', '.') }}
                        </h5>
                        @if ($data['data']->status == 0)
                            <h5 class="mb-1">
                                Sisa Utang: <strong>Rp{{ number_format($data['data']->total - $data['data']['diskon'] - $data['data']->total_bayar, 2, ',', '.') }}</strong>
                            </h5>
                        @endif
                        
                        <div>
                            @if ($data['data']->status == 0 && Auth::user()->hasRule('purchaseDebtPay'))
                                <a href="{{ route('purchaseDebtPay', ['id' => $data['data']->debt->id]) }}" class="btn my-1 btn-primary" data-toggle="tooltip" data-state="dark" title="Bayar Utang">
                                    <i class="fa fa-dollar-sign"></i>
                                    Bayar Utang
                                </a>
                            @endif
                            @if (Auth::user()->hasRule('purchasePrint'))
                                <a href="{{ route('purchasePrint', ['id' => $data['data']->id]) }}" class="btn my-1 btn-dark" data-toggle="tooltip" data-state="dark" title="Print Transaksi" target="_blank">
                                    <i class="fa fa-print"></i>
                                    Print
                                </a>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header h4 text-center">Detail Pembelian {{ $data['data']->no_faktur }}</div>
            <div class="table-responsive">
                <table class="table card-table table-bordered">
                    <thead class="thead-light">
                        <tr>
                            <th>#</th>
                            <th>Kode Barang</th>
                            <th>Nama Barang</th>
                            <th>Harga Beli (Rp/Kg)</th>
                            <th>Qty (Kg)</th>
                            <th>Total (Rp)</th>
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
                                <td>{{ $i }}</td>
                                <td>{{ $value->item->code }}</td>
                                <td>{{ $value->item->name }}</td>
                                <td class="text-right">{{ number_format($value->harga_beli,2, ',', '.') }}</td>
                                <td class="text-right">
                                    @php
                                    if (fmod($value->qty, 1) !== 0.00) {
                                        echo number_format($value->qty, 2, ',', '.');
                                    }else{
                                        echo number_format($value->qty);
                                    }
                                    @endphp
                                </td>
                                <td class="text-right">{{ number_format($value->total,2, ',', '.') }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="card-footer">
                <div class="row">
                    <div class="col-md-3">
                        Total Record : <strong>{{ $i }}</strong>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
