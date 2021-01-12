@extends('layouts.application')

@section('module', 'Laporan Rekapitulasi Piutang')

@section('content')
<div class="ui-bordered px-3 pt-3 mb-3">
    <form class="form-row align-items-center" method="get" action="{{ url()->current() }}">

        <div class="col-md-6 mb-3">
            <label class="form-label">Filter Tanggal</label>
            <div class="input-group">
                <div class="input-group-prepend"><span class="input-group-text">From</div>
                <input type="text" class="form-control datepicker" name="start_date" value="{{$data['start_date']}}">
                <div class="input-group-prepend"><span class="input-group-text">To</div>
                <input type="text" class="form-control datepicker" name="end_date" value="{{$data['end_date']}}">
                <div class="input-group-prepend"><span class="input-group-text"><i class="fa fa-calendar"></i></span></div>
                <span class="input-group-append">
                    <button class="btn btn-secondary" type="submit">Cari</button>
                </span>
            </div>
        </div>

        <div class="col-md-6 text-right">
            @if (Auth::user()->hasRule('storeReportPiutangAdd'))
                <a href="{{ route('storeReportPiutangAdd') }}" class="btn my-1 btn-info" data-toggle="tooltip" data-state="dark" title="Tambah">
                    <i class="fa fa-plus"></i>
                    Tambah
                </a>
            @endif
            @if (Auth::user()->hasRule('storeReportPiutangPrint'))
                <a href="{{ route('storeReportPiutangPrint', ['start_date' => $data['start_date'], 'end_date' => $data['end_date']]) }}" class="btn my-1 btn-dark" data-toggle="tooltip" data-state="dark" title="Print" target="_blank">
                    <i class="fa fa-print"></i>
                    Print
                </a>
            @endif
            @if (Auth::user()->hasRule('storeReportPiutangDownload'))
                <a href="{{ route('storeReportPiutangDownload', ['start_date' => $data['start_date'], 'end_date' => $data['end_date']]) }}" class="btn my-1 btn-success" data-toggle="tooltip" data-state="dark" title="Download">
                    <i class="fa fa-download"></i>
                    Download
                </a>
            @endif
        </div>
    </form>
</div>
<div class="row">
    <div class="col-md-3 mb-3">
        <div class="card text-center">
            <div class="card-header h5">Piutang s/d {{ date('d-m-Y', strtotime('-1 day', strtotime($data['start_date']))) }}</div>
            <div class="card-body h4">Rp{{ number_format($data['data']->sum('saldo_awal'), 2, ',', '.') }}</div>
        </div>
    </div>
    <div class="col-md-3 mb-3">
        <div class="card text-center">
            <div class="card-header h5">Total Penambahan</div>
            <div class="card-body h4">Rp{{ number_format($data['data']->sum('penambahan'), 2, ',', '.') }}</div>
        </div>
    </div>
    <div class="col-md-3 mb-3">
        <div class="card text-center">
            <div class="card-header h5">Total Pengurangan</div>
            <div class="card-body h4">Rp{{ number_format($data['data']->sum('pengurangan'), 2, ',', '.') }}</div>
        </div>
    </div>
    <div class="col-md-3 mb-3">
        <div class="card text-center">
            <div class="card-header h5">Total Piutang</div>
            <div class="card-body h4">Rp{{ number_format($data['data']->sum('saldo_akhir'), 2, ',', '.') }}</div>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header text-center">
                <h4 class="mb-0">Rekapitulasi Piutang</h4>
                <h5 class="mb-0">Periode {{ date('d-m-Y', strtotime($data['start_date'])) }} s/d {{ date('d-m-Y', strtotime($data['end_date'])) }}</h5>
            </div>
            <div class="table-responsive">
                <table class="table card-table table-bordered">
                    <thead class="thead-light">
                        <tr class="text-center">
                            <th>#</th>
                            <th>Kode Wilayah</th>
                            <th>Nama Wilayah</th>
                            <th>Piutang s/d {{ date('d-m-Y', strtotime('-1 day', strtotime($data['start_date']))) }} (Rp)</th>
                            <th>Penambahan (Rp)</th>
                            <th>Pengurangan (Rp)</th>
                            <th>Total Piutang (Rp)</th>
                            @if (Auth::user()->hasRule('itemCard'))
                                <th>Aksi</th>
                            @endif
                        </tr>
                    </thead>
                    <tbody>
                        @php
                            $i = 0;
                        @endphp
                        @foreach ($data['data'] as $value)
                            @php
                                $i++;
                            @endphp
                            <tr>
                                <td>{{ $i }}</td>
                                <td>{{ $value->code }}</td>
                                <td>{{ $value->name }}</td>
                                <td class="text-right">{{ number_format($value->saldo_awal, 2, ',', '.')}}</td>
                                <td class="text-right">{{ number_format($value->penambahan, 2, ',', '.')}}</td>
                                <td class="text-right">{{ number_format($value->pengurangan, 2, ',', '.')}}</td>
                                <td class="text-right">{{ number_format($value->saldo_akhir, 2, ',', '.')}}</td>
                                @if (Auth::user()->hasRule('storeReportPiutangDetail'))
                                    <td class="text-center">
                                        <a href="{{ route('storeReportPiutangDetail', ['region_id' => $value->id, 'start_date' => $data['start_date'], 'end_date' => $data['end_date'], 'suplier_id' => $value->id]) }}" class="btn icon-btn btn-dark btn-sm" data-toggle="tooltip" data-placement="top" data-state="dark" title="Detail">
                                            <i class="fa fa-bars"></i>
                                        </a>
                                    </td>
                                @endif	
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="card-footer">
                <div class="row">
                    <div class="col-md-3">
                        Total Record : <strong>{{ $data['data']->count() }}</strong>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection