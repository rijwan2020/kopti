@extends('layouts.application')

@section('module', 'Laporan Penjualan Wilayah')

@section('content')
<div class="ui-bordered px-3 pt-3 mb-3">
    <form class="form-row align-items-center" method="get" action="{{ url()->current() }}">
        <div class="col-md-4 mb-3">
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

        <div class="col-md text-right">
            <a href="{{ route('storeReportRegionPrint', [$data['param']]) }}" class="btn my-1 btn-dark" data-toggle="tooltip" data-state="dark" title="Print" target="_blank">
                <i class="fa fa-print"></i>
                Print
            </a>
            <a href="{{ route('storeReportRegionDownload', [$data['param']]) }}" class="btn my-1 btn-success" data-toggle="tooltip" data-state="dark" title="Download">
                <i class="fa fa-download"></i>
                Download
            </a>
        </div>
    </form>
</div>
<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header text-center">
                <h4 class="mb-0">Rekapitulasi Penjualan Wilayah</h4>
                <h5 class="mb-0">Periode {{ date('d-m-Y', strtotime($data['start_date'])) }} s/d {{ date('d-m-Y', strtotime($data['end_date'])) }}</h5>
            </div>
            <div class="table-responsive">
                <table class="table card-table table-bordered">
                    <thead class="thead-light">
                        <tr class="text-center">
                            <th>#</th>
                            <th>Wilayah</th>
                            <th>Kebutuhan/Bulan (Kg)</th>
                            <th>Total Penjualan (Rp)</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                            $i = $total_kebutuhan = $total_penjualan = 0;
                        @endphp
                        @foreach ($data['data'] as $value)
                            @php
                                $i++;
                                $kebutuhan = $value->member->sum('soybean_ration');
                                $total_kebutuhan += $kebutuhan;
                                $penjualan = $value->penjualan->where('tanggal_jual', '>=', $data['start_date'].' 00:00:00')->where('tanggal_jual', '<=', $data['end_date'].' 23:59:59')->sum('total_belanja');
                                $total_penjualan += $penjualan;
                            @endphp
                            <tr>
                                <td class="text-center">{{ $i }}</td>
                                <td>{{ $value->name }}</td>
                                <td class="text-right">{{ number_format($kebutuhan, 2, ',','.') }}</td>
                                <td class="text-right">{{ number_format($penjualan, 2, ',','.') }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr class="text-right">
                            <th colspan="2">Jumlah</th>
                            <th>{{ number_format($total_kebutuhan, 2, ',','.') }}</th>
                            <th>{{ number_format($total_penjualan, 2, ',','.') }}</th>
                        </tr>
                    </tfoot>
                </table>
            </div>
            <div class="card-footer">
                <div class="row">
                    <div class="col-md-3">
                        Total Record : <strong>{{$data['data']->count()}}</strong>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection