@extends('layouts.application')

@section('module', 'Sisa Hasil Usaha')

@section('content')
<div class="ui-bordered px-3 pt-3 mb-3">
    <form class="form-row align-items-center" method="get" action="{{ url()->current() }}">
        @if (isset($data['tbb_id']))
            <input type="hidden" name="tbb_id" value="{{ $data['tbb_id'] }}">
        @endif
        @if (isset($data['tbt_id']))
            <input type="hidden" name="tbt_id" value="{{ $data['tbt_id'] }}">
        @endif
        <div class="col-md-4 mb-3">
            @if (!isset($data['tbb_id']) && !isset($data['tbt_id']))
                <label class="form-label">Filter Tanggal</label>
                <div class="input-group">
                        <div class="input-group-prepend"><span class="input-group-text"><i class="fa fa-calendar"></i></span></div>
                    <input type="text" class="form-control datepicker" name="end_date" value="{{$data['end_date']}}">
                    <span class="input-group-append">
                        <button class="btn btn-secondary" type="submit">Filter</button>
                    </span>
                </div>
            @endif
        </div>

        <div class="col-md text-right">
            @if (Auth::user()->hasRule('shuConfig') && !isset($data['tbb_id']) && !isset($data['tbt_id']))
                <a href="{{ route('shuConfig') }}" class="btn my-1 btn-info" data-toggle="tooltip" data-state="dark" title="Set Alokasi SHU">
                    <i class="fa fa-cogs"></i>
                    Set Alokasi SHU
                </a>
            @endif
            @if (Auth::user()->hasRule('shuAnggota'))
                <a href="{{ route('shuAnggota', $data['param']) }}" class="btn my-1 btn-info" data-toggle="tooltip" data-state="dark" title="List SHU Anggota">
                    <i class="fa fa-bars"></i>
                    SHU Anggota
                </a>
            @endif
            @if (Auth::user()->hasRule('shuPrint'))
                <a href="{{ route('shuPrint', $data['param']) }}" class="btn my-1 btn-dark" data-toggle="tooltip" data-state="dark" title="Print SHU" target="_blank">
                    <i class="fa fa-print"></i>
                    Print
                </a>
            @endif
            @if (Auth::user()->hasRule('shuDownload'))
                <a href="{{ route('shuDownload', $data['param']) }}" class="btn my-1 btn-success" data-toggle="tooltip" data-state="dark" title="Download SHU">
                    <i class="fa fa-download"></i>
                    Download
                </a>
            @endif
        </div>
    </form>
</div>
@if ($data['percent'] != 100)
	<div class="alert alert-dark-warning alert-dismissible fade show">
	    <i class="fa fa-exclamation-triangle"></i>
	    <strong>Warning!</strong> 
	    Persentase SHU harus berjumlah <b>100%</b>
	</div>
@endif
<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header text-center">
                <h3 class="mb-1">Rencana Pembagian Sisa Hasil Usaha</h3>
                <h5 class="mb-0">Periode {{ date('d-m-Y', strtotime($data['end_date'])) }}</h5>
            </div>
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div class="h5">Perhitungan Hasil Usaha</div>
                    <div class="h5">{{ $data['shu'] >=0 ? 'Rp'.number_format($data['shu'] + $data['zakat'], 2, ',', '.') : '(Rp'.number_format(($data['shu'] + $data['zakat'])*-1, 2, ',', '.').')' }}</div>
                </div>
                <div class="d-flex justify-content-between">
                    <div class="h5">Pengeluaran Zakat (2.5%)</div>
                    <div class="h5">{{ $data['shu'] >=0 ? 'Rp'.number_format($data['zakat'], 2, ',', '.') : 'Rp0' }}</div>
                </div>
                <div class="d-flex justify-content-between">
                    <div class="h5">Dana yang dibagikan (PHU - Zakat)</div>
                    <div class="h5">{{ $data['shu'] >=0 ? 'Rp'.number_format($data['shu'], 2, ',', '.') : 'Rp0' }}</div>
                </div>
            </div>
            <div class="table-responsive">
                <table class="table card-table">
                    <thead class="thead-light">
                        <tr>
                            <th>#</th>
                            <th>Alokasi</th>
                            <th>Persentase</th>
                            <th>Jumlah (Rp)</th>
                        </tr>
                    </thead>
                    @php
                        $i = $jumlah = 0;
                    @endphp
                    @foreach ($data['data'] as $item)
                        @php
                            $i++;
                            $total = $data['shu'] * $item->percent / 100;
                            $jumlah += $total;
                        @endphp
                        <tr>
                            <td>{{ $i }}</td>
                            <td>{{ $item->allocation }}</td>
                            <td>{{ number_format($item->percent, 2, ',', '.') }}%</td>
                            <td class="text-right">{{ $data['shu'] >= 0 ? number_format($total, 2, ',', '.') : '0' }}</td>
                        </tr>
                    @endforeach
                    <tfoot>
                        <tr class="text-right">
                            <th colspan="3">Jumlah :</th>
                            <th>{{ $data['shu'] >= 0 ? number_format($jumlah, 2, ',', '.') : '0' }}</th>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection