@extends('layouts.application')

@section('module', 'Laporan Simpanan')

@section('content')
<div class="ui-bordered px-3 pt-3 mb-3">
    <form class="form-row align-items-center" method="get" action="{{ url()->current() }}">
        
        <div class="col-md-3 mb-3">
            <label class="form-label">Jenis Simpanan</label>
            <select class="select2 form-control" name="type_id">
                <option value="all" {{ $data['type_id'] == 'all' ? 'selected' : '' }}>--Semua--</option>
                @foreach ($data['jenis'] as $value)
                    <option value="{{ $value->id }}" {{ $data['type_id'] == $value->id ? 'selected' : '' }}>{{ $value->name }}</option>
                @endforeach
            </select>
        </div>

        <div class="col-md-4 mb-3">
            <label class="form-label">Filter Tanggal</label>
            <div class="input-group">
                <input type="text" class="form-control datepicker" name="date" value="{{$data['date']}}">
                <div class="input-group-prepend"><span class="input-group-text"><i class="fa fa-calendar"></i></span></div>
                <span class="input-group-append">
                    <button class="btn btn-secondary" type="submit">Filter</button>
                </span>
            </div>
        </div>

        <div class="col-md-5 text-right">
            @if (Auth::user()->hasRule('rekapitulasiSimpananDownload'))
                <a href="{{ route('rekapitulasiSimpananDownload', ['date' => $data['date'], 'type_id' => $data['type_id']]) }}" class="btn my-1 btn-success" data-toggle="tooltip" data-state="dark" title="Download rekapitulasi simpanan">
                    <i class="fa fa-download"></i>
                    Download
                </a>
            @endif
            @if (Auth::user()->hasRule('rekapitulasiSimpananPrint'))
                <a href="{{ route('rekapitulasiSimpananPrint', ['date' => $data['date'], 'type_id' => $data['type_id']]) }}" class="btn my-1 btn-dark" data-toggle="tooltip" data-state="dark" title="Print rekapitulasi simpanan" target="_blank">
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
                <h4 class="mb-0">
                    @if ($data['type_id'] != 'all')
                        @foreach ($data['jenis'] as $item)
                            @if ($item->id == $data['type_id'])
                                Rekapitulasi {{ $item->name }}
                            @endif
                        @endforeach
                    @else
                        Rekapitulasi Simpanan
                    @endif
                </h4>
                <h5 class="mb-0">Per {{ date('d-m-Y', strtotime($data['date'])) }}</h5>
            </div>
            <div class="table-responsive">
                <table class="table card-table table-bordered">
                    <thead class="thead-light">
                        <tr class="text-center">
                            <th>#</th>
                            <th>Wilayah</th>
                            <th>Saldo s/d {{ date('Y-m-d', strtotime('-1 day', strtotime($data['start_date']))) }} (Rp)</th>
                            <th>Saldo Masuk (Rp)</th>
                            <th>Saldo Keluar (Rp)</th>
                            <th>Jasa (Rp)</th>
                            <th>Total Saldo (Rp)</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                            $i = $total_debit = $total_kredit = $total_saldo_awal = $total_jasa = 0;
                        @endphp
                        @foreach ($data['data'] as $value)
                            @php
                                $i++;
                                $saldo = $value['saldo_awal'] + $value['kredit'] - $value['debit'] + $value['jasa'];
                                $total_saldo_awal += $value['saldo_awal'];
                                $total_kredit += $value['kredit'];
                                $total_debit += $value['debit'];
                                $total_jasa += $value['jasa'];
                            @endphp
                            <tr>
                                <td class="text-center">{{ $i }}</td>
                                <td>{{ $value['nama'] }}</td>
                                <td class="text-right">{{ number_format($value['saldo_awal'], 2, ',', '.') }}</td>
                                <td class="text-right">{{ number_format($value['kredit'], 2, ',', '.') }}</td>
                                <td class="text-right">{{ number_format($value['debit'], 2, ',', '.') }}</td>
                                <td class="text-right">{{ number_format($value['jasa'], 2, ',', '.') }}</td>
                                <td class="text-right">{{ number_format($saldo, 2, ',', '.') }}</td>
                                <td class="text-center">
                                    @if (Auth::user()->hasRule('rekapitulasiSimpananDetail'))
                                        <a href="{{ route('rekapitulasiSimpananDetail', ['region_id' => $value['id'], 'date' => $data['date'], 'type_id' => $data['type_id']]) }}" class="btn icon-btn btn-success btn-sm" data-toggle="tooltip" data-placement="top" data-state="dark" title="Rekapitulasi simpanan anggota wilayah {{ $value['nama'] }}">
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
                            <th>{{ number_format($total_saldo_awal, 2, ',', '.') }}</th>
                            <th>{{ number_format($total_kredit, 2, ',', '.') }}</th>
                            <th>{{ number_format($total_debit, 2, ',', '.') }}</th>
                            <th>{{ number_format($total_jasa, 2, ',', '.') }}</th>
                            <th>{{ number_format($total_saldo_awal + $total_kredit - $total_debit + $total_jasa, 2, ',', '.') }}</th>
                            <th></th>
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