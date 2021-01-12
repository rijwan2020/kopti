@extends('layouts.application')

@section('module', 'Laporan Simpanan')

@section('content')
<div class="ui-bordered px-3 pt-3 mb-3">
    <form class="form-row align-items-center" method="get" action="{{ url()->current() }}">
        
        <div class="col-md-2 mb-3">
            <label class="form-label">Wilayah</label>
            <select class="select2 form-control" name="region_id">
                <option value="all" {{ $data['region_id'] == 'all' ? 'selected' : '' }}>--Semua--</option>
                @foreach ($data['region'] as $value)
                    <option value="{{ $value->id }}" {{ $data['region_id'] == $value->id ? 'selected' : '' }}>{{ $value->name }}</option>
                @endforeach
            </select>
        </div>

        <div class="col-md-2 mb-3">
            <label class="form-label">Jenis Simpanan</label>
            <select class="select2 form-control" name="type_id">
                <option value="all" {{ $data['type_id'] == 'all' ? 'selected' : '' }}>--Semua--</option>
                @foreach ($data['jenis'] as $value)
                    <option value="{{ $value->id }}" {{ $data['type_id'] == $value->id ? 'selected' : '' }}>{{ $value->name }}</option>
                @endforeach
            </select>
        </div>

        <div class="col-md-3 mb-3">
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
            @if (Auth::user()->hasRule('rekapitulasiSimpananDetailDownload'))
                <a href="{{ route('rekapitulasiSimpananDetailDownload', $data['param']) }}" class="btn my-1 btn-success" data-toggle="tooltip" data-state="dark" title="Download rekapitulasi simpanan anggota">
                    <i class="fa fa-download"></i>
                    Download
                </a>
            @endif
            @if (Auth::user()->hasRule('rekapitulasiSimpananDetailPrint'))
                <a href="{{ route('rekapitulasiSimpananDetailPrint', $data['param']) }}" class="btn my-1 btn-dark" data-toggle="tooltip" data-state="dark" title="Print rekapitulasi simpanan anggota" target="_blank">
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
                                Rekapitulasi {{ $item->name }} Anggota
                            @endif
                        @endforeach
                    @else
                        Rekapitulasi Simpanan Anggota
                    @endif
                </h4>
                <h5 class="mb-0">Per {{ date('d-m-Y', strtotime($data['date'])) }}</h5>
                @if ($data['region_id'] != 'all')
                    @foreach ($data['region'] as $item)
                        @if ($data['region_id'] == $item->id)
                            <h6 class="mb-0">Wilayah {{ $item->name }}</h6>
                        @endif
                    @endforeach
                @endif
            </div>
            <div class="table-responsive">
                <table class="table card-table">
                    <thead class="thead-light">
                        <tr>
                            <th>#</th>
                            <th>Kode Anggota</th>
                            <th>Nama Anggota</th>
                            <th class="text-center">Saldo s/d {{ date('Y-m-d', strtotime('-1 day', strtotime($data['start_date']))) }} (Rp)</th>
                            <th class="text-center">Saldo Masuk (Rp)</th>
                            <th class="text-center">Saldo Keluar (Rp)</th>
                            <th class="text-center">Jasa (Rp)</th>
                            <th class="text-center">Total Saldo (Rp)</th>
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
                                <td>{{ $i }}</td>
                                <td>{{ $value->code }}</td>
                                <td>{{ $value->name }}</td>
                                <td class="text-right">{{ number_format($value['saldo_awal'], 2, ',', '.') }}</td>
                                <td class="text-right">{{ number_format($value['kredit'], 2, ',', '.') }}</td>
                                <td class="text-right">{{ number_format($value['debit'], 2, ',', '.') }}</td>
                                <td class="text-right">{{ number_format($value['jasa'], 2, ',', '.') }}</td>
                                <td class="text-right">{{ number_format($saldo, 2, ',', '.') }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr class="text-right">
                            <th colspan="3">Jumlah :</th>
                            <th>{{ number_format($total_saldo_awal, 2, ',', '.') }}</th>
                            <th>{{ number_format($total_kredit, 2, ',', '.') }}</th>
                            <th>{{ number_format($total_debit, 2, ',', '.') }}</th>
                            <th>{{ number_format($total_jasa, 2, ',', '.') }}</th>
                            <th>{{ number_format($total_saldo_awal + $total_kredit - $total_debit + $total_jasa, 2, ',', '.') }}</th>
                        </tr>
                    </tfoot>
                </table>
            </div>
            <div class="card-footer">
                <div class="row">
                    <div class="col-md-12">
                        Total Record : <strong>{{$data['data']->count() }}</strong>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection