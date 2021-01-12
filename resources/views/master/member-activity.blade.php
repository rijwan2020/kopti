@extends('layouts.application')

@section('module', 'Data Anggota')

@section('content')
<div class="ui-bordered px-3 pt-3 mb-3">
    <form class="form-row align-items-center" method="get" action="{{ url()->current() }}">
        <div class="col-md-4 mb-3">
            <label class="form-label">Filter Tanggal</label>
            <div class="input-group">
                    <div class="input-group-prepend"><span class="input-group-text"><i class="fa fa-calendar"></i></span></div>
                    <input type="text" class="form-control datepicker" name="date" value="{{$data['date']}}">
                <span class="input-group-append">
                    <button class="btn btn-secondary" type="submit">Filter</button>
                </span>
            </div>
        </div>

        <div class="col-md text-right">
            @if (Auth::user()->hasRule('memberActivityPrint'))
                <a href="{{ route('memberActivityPrint', ['id' => $data['data']->id,'date' => $data['date']]) }}" class="btn my-1 btn-dark" data-toggle="tooltip" data-state="dark" title="Print Catatan aktivitas anggota" target="_blank">
                    <i class="fa fa-print"></i>
                    Print
                </a>
            @endif
        </div>
    </form>
</div>
<div class="row">
    <div class="col-md-8 offset-md-2">
        <div class="card">
            <div class="card-header text-center">
                <h4 class="mb-1">Catatan Aktivitas Anggota</h4>
                <h6 class="mb-1">Sampai Tanggal : {{ date('d-m-Y', strtotime($data['date'])) }}</h6>
            </div>
            <div class="card-body">
                <table class="borderless mb-3" width="100%">
                    <tbody>
                        <tr>
                            <td width="30%">Kode Anggota</td>
                            <td>: {{ $data['data']->code }}</td>
                        </tr>
                        <tr>
                            <td>Nama Anggota</td>
                            <td>: {{ $data['data']->name }}</td>
                        </tr>
                        <tr>
                            <td>Wilayah</td>
                            <td>: {{ $data['data']->region->name }}</td>
                        </tr>
                        <tr>
                            <td>Jatah Per Bulan</td>
                            <td>: {{ $data['data']->soybean_ration }}</td>
                        </tr>
                    </tbody>
                </table>
                <h5>Jumlah Simpanan</h5>
                <div class="row">
                    <div class="col-md-8 offset-md-2">
                        <table class="table table-borderless">
                            @php
                                $total_saldo = 0
                            @endphp
                            @foreach ($data['data']->deposit as $value)
                                @php
                                    $saldo = $value->transaction->where('transaction_date', '<=', $data['date']." 23:59:59")->sum('kredit') - $value->transaction->where('transaction_date', '<=', $data['date']." 23:59:59")->sum('debit');
                                    $total_saldo += $saldo;
                                @endphp
                                <tr>
                                    <td class="py-0">- Jumlah {{ $value->type->name }}</td>
                                    <td class="text-right py-0">: Rp</td>
                                    <td class="text-right py-0">{{ number_format($saldo, 2, ',', '.') }}</td>
                                </tr>
                            @endforeach
                            <tfoot>
                                <tr>
                                    <th>Total Simpanan</th>
                                    <th class="text-right">: Rp</th>
                                    <th class="text-right">{{ number_format($total_saldo, 2, ',', '.') }}</th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
                <h5>Jumlah Kredit</h5>
                <div class="col-md-8 offset-md-2">
                    <table class="table table-borderless">
                        @php
                            $piutang = $data['data']->saleDebt->where('tanggal_transaksi', '<=', $data['date'].' 23:59:59')->sum('total') - $data['data']->saleDebt->where('tanggal_transaksi', '<=', $data['date'].' 23:59:59')->sum('pay');
                        @endphp
                        <tr>
                            <td class="py-0">- Piutang Kedelai</td>
                            <td class="text-right py-0">: Rp</td>
                            <td class="text-right py-0">{{ number_format($piutang, 2, ',', '.') }}</td>
                        </tr>
                        <tr>
                            <th width="60%">Jumlah Kredit</th>
                            <th class="text-right">: Rp</th>
                            <th class="text-right">{{ number_format($piutang, 2, ',', '.') }}</th>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection