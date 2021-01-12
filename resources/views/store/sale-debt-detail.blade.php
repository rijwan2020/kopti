@extends('layouts.application')

@section('module', 'Data Piutang Penjualan')

@section('content')
<div class="row mb-3">
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
                            <div>No Faktur</div>
                            <b> {{ $data['data']->sale->no_faktur }}</b>
                        </div>
                        <div class="d-flex justify-content-between mb-0">
                            <div>Tanggal Transaksi</div>
                            <b> {{ $data['data']->sale->tanggal_jual }}</b>
                        </div>
                        <div class="d-flex justify-content-between mb-0">
                            <div>Transaksi di</div>
                            <b> {{ $data['data']->warehouse->name ?? 'Pusat' }}</b>
                        </div>
                        <div class="d-flex justify-content-between mb-0">
                            <div>Status</div>
                            <b> {{ $data['data']->status == 0 ? 'Belum Lunas' : 'Lunas' }}</b>
                        </div>
                    </div>
                    <div class="col-md-8 text-right">
                        <h5 class="mb-1">
                            Total Piutang: Rp{{ number_format($data['data']->total, 2, ',', '.') }}
                        </h5>
                        <h5 class="mb-1">
                            Jumlah Bayar: Rp{{ number_format($data['data']->pay, 2, ',', '.') }}
                        </h5>
                        <h5 class="mb-1">
                            Sisa: Rp{{ number_format($data['data']->total - $data['data']->pay, 2, ',', '.') }}
                        </h5>
                        
                        <div>
                            @if (Auth::user()->hasRule('saleDebtPay') && $data['data']->status == 0)
                                <a href="{{ route('saleDebtPay', ['id' => $data['data']->id]) }}" class="btn my-1 btn-success" data-toggle="tooltip" data-state="dark" title="Print Transaksi">
                                    <i class="fa fa-dollar-sign"></i>
                                    Bayar
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
            <div class="card-header h4 text-center">Histori Pembayaran</div>
            <div class="table-responsive">
                <table class="table card-table table-bordered">
                    <thead class="thead-light">
                        <tr class="text-center">
                            <th>#</th>
                            <th>Tanggal Bayar</th>
                            <th>No Ref</th>
                            <th>Keterangan</th>
                            <th>Total (Rp)</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                            $i = 0;
                        @endphp
                        @foreach ($data['data']->histori as $value)
                            @if ($value->tipe == 1)
                                @php
                                    $i++;
                                @endphp
                                <tr>
                                    <td class="text-center">{{ $i }}</td>
                                    <td>{{ $value->trxdate }}</td>
                                    <td>{{ $value->no_ref }}</td>
                                    <td>{{ $value->note }}</td>
                                    <td class="text-right">{{ number_format($value->total,2, ',', '.') }}</td>
                                    <td class="text-center">
                                        @if (Auth::user()->hasRule('saleDebtDetailPrint'))
                                            <a href="{{ route('saleDebtDetailPrint', ['debt_id' => $data['data']->id,'id' => $value->id]) }}" class="btn icon-btn btn-dark btn-sm" data-toggle="tooltip" data-placement="top" data-state="dark" title="Print Bukti Pembayaran" target="_blank">
                                                <i class="fa fa-print"></i>
                                            </a>
                                        @endif
                                    </td>
                                </tr>
                            @endif
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
