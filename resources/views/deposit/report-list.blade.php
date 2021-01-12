@extends('layouts.application')

@section('module', 'Laporan Simpanan')

@section('content')
<div class="ui-bordered px-3 pt-3 mb-3">
    <form class="form-row align-items-center" method="get" action="{{ url()->current() }}">
        
        <div class="col-md-3 mb-3">
            <label class="form-label">Pilih Tanggal</label>
            <div class="input-group">
                <input type="text" class="form-control datepicker" placeholder="Pilih Tanggal" name="end_date" value="{{$data['end_date']}}">
                <span class="input-group-append">
                    <button class="btn btn-secondary" type="submit">Filter</button>
                </span>
            </div>
        </div>

        <div class="col-md text-right">
            @if (Auth::user()->hasRule('depositReportDetail'))
                <a href="{{ route('depositReportDetail') }}" class="btn my-1 btn-primary" data-toggle="tooltip" data-state="dark" title="Rekapitulasi saldo masuk dan keluar simpanan">
                    <i class="fa fa-bars"></i>
                    Detail
                </a>
            @endif
            @if (Auth::user()->hasRule('depositReportDownload'))
                <a href="{{ route('depositReportDownload', ['end_date' => $data['end_date']]) }}" class="btn my-1 btn-success" data-toggle="tooltip" data-state="dark" title="Download rekapitulasi simpanan">
                    <i class="fa fa-download"></i>
                    Download
                </a>
            @endif
            @if (Auth::user()->hasRule('depositReportPrint'))
                <a href="{{ route('depositReportPrint', ['end_date' => $data['end_date']]) }}" class="btn my-1 btn-dark" data-toggle="tooltip" data-state="dark" title="Print rekapitulasi simpanan" target="_blank">
                    <i class="fa fa-print"></i>
                    Print
                </a>
            @endif
        </div>
    </form>
</div>
<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header text-center">
                <h4 class="mb-0">Rekapitulasi Simpanan</h4>
                <h5 class="mb-0">Per {{ date('d M Y', strtotime($data['end_date'])) }}</h5>
            </div>
            <div class="table-responsive">
                <table class="table card-table">
                    <thead class="thead-light">
                        <tr>
                            <th>#</th>
                            <th>Wilayah</th>
                            @foreach ($data['jenis'] as $item)
                                <th>{{ $item->name }}</th>
                            @endforeach
                            <th>Total</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                            $i = $jml_total = 0;
                            foreach ($data['jenis'] as $hasil) {
                                $jml[$hasil->id] = 0;
                            }
                        @endphp
                        @foreach ($data['data'] as $value)
                            @php
                                $i++;
                                $total = 0;
                            @endphp
                            <tr>
                                <td>{{ $i }}</td>
                                <td>{{ $value['nama'] }}</td>
                                @foreach ($data['jenis'] as $item)
                                    @php
                                        $total+= $value[$item->id];
                                        $jml_total += $value[$item->id];
                                        $jml[$item->id] += $value[$item->id];
                                    @endphp
                                    <td class="text-right">Rp{{ number_format($value[$item->id], 2, ',', '.') }}</td>
                                @endforeach
                                <th class="text-right">Rp{{ number_format($total, 2, ',', '.') }}</th>
                                <td class="text-center">
                                    @if (Auth::user()->hasRule('depositReportMember'))
                                        <a href="{{ route('depositReportMember', ['region_id' => $value['id'], 'end_date' => $data['end_date']]) }}" class="btn icon-btn btn-success btn-sm" data-toggle="tooltip" data-placement="top" data-state="dark" title="Rekapitulasi simpanan anggota wilayah {{ $value['nama'] }}">
                                            <i class="fa fa-bars"></i>
                                        </a>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr class="text-right">
                            <th colspan="2">Jumlah :</th>
                            @foreach ($data['jenis'] as $item)
                                <th class="text-right">Rp{{ number_format($jml[$item->id], 2, ',', '.') }}</th>
                            @endforeach
                            <th class="text-right">Rp{{ number_format($jml_total, 2, ',', '.') }}</th>
                            <th class="text-center">
                                @if (Auth::user()->hasRule('depositReportMember'))
                                    <a href="{{ route('depositReportMember', ['region_id' => 'all', 'end_date' => $data['end_date']]) }}" class="btn icon-btn btn-success btn-sm" data-toggle="tooltip" data-placement="top" data-state="dark" title="Rekapitulasi simpanan anggota">
                                        <i class="fa fa-bars"></i>
                                    </a>
                                @endif
                            </th>
                        </tr>
                    </tfoot>
                </table>
            </div>
            <div class="card-footer">
                <div class="row">
                    <div class="col-md-3">
                        Total Record : <strong>{{ count($data['data']) }}</strong>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
