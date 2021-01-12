@extends('layouts.application')

@section('module', 'Laporan Simpanan')

@section('content')
<div class="ui-bordered px-3 pt-3 mb-3">
    <form class="form-row align-items-center" method="get" action="{{ url()->current() }}">

        <div class="col-md-1 mb-3">
            <label class="form-label">Limit</label>
            <select class="select2 form-control" name="limit">
                <option value="25" {{ $data['limit'] == 25 ?' selected' : '' }}>25</option>
                <option value="50" {{ $data['limit'] == 50 ?' selected' : '' }}>50</option>
                <option value="100" {{ $data['limit'] == 100 ?' selected' : '' }}>100</option>
                <option value="150" {{ $data['limit'] == 150 ?' selected' : '' }}>150</option>
                <option value="200" {{ $data['limit'] == 200 ?' selected' : '' }}>200</option>
            </select>
        </div>

        <div class="col-md-2 mb-3">
            <label class="form-label">Wilayah</label>
            <select class="select2 form-control" name="region_id">
                <option value="all" {{ $data['region_id'] == 'all' ? 'selected' : '' }}>--Semua--</option>
                @foreach ($data['region'] as $value)
                    <option value="{{ $value->id }}" {{ $data['region_id'] == $value->id ? 'selected' : '' }}>{{ $value->name }}</option>
                @endforeach
            </select>
        </div>

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
            @if (Auth::user()->hasRule('depositReportMemberDetail'))
                <a href="{{ route('depositReportMemberDetail') }}" class="btn my-1 btn-primary" data-toggle="tooltip" data-state="dark" title="Rekapitulasi saldo masuk dan keluar simpanan anggota">
                    <i class="fa fa-bars"></i>
                    Detail
                </a>
            @endif
            @if (Auth::user()->hasRule('depositReportMemberDownload'))
                <a href="{{ route('depositReportMemberDownload', ['end_date' => $data['end_date'], 'region_id' => $data['region_id']]) }}" class="btn my-1 btn-success" data-toggle="tooltip" data-state="dark" title="Download rekapitulasi simpanan anggota">
                    <i class="fa fa-download"></i>
                    Download
                </a>
            @endif
            @if (Auth::user()->hasRule('depositReportMemberPrint'))
                <a href="{{ route('depositReportMemberPrint', ['end_date' => $data['end_date'], 'region_id' => $data['region_id']]) }}" class="btn my-1 btn-dark" data-toggle="tooltip" data-state="dark" title="Print rekapitulasi simpanan anggota" target="_blank">
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
                <h4 class="mb-0">Rekapitulasi Simpanan Anggota</h4>
                <h5 class="mb-0">
                    Per {{ date('d M Y', strtotime($data['end_date'])) }}
                    @if ($data['region_id']!='a')
                        @foreach ($data['region'] as $item)
                            @if ($item->id == $data['region_id'])
                                <br> Wilayah: {{ $item->name }}
                            @endif
                        @endforeach
                    @endif  
                </h5>
            </div>
            <div class="table-responsive">
                <table class="table card-table">
                    <thead class="thead-light">
                        <tr>
                            <th>#</th>
                            <th>Kode</th>
                            <th>Nama</th>
                            @foreach ($data['jenis'] as $item)
                                <th>{{ $item->name }}</th>
                            @endforeach
                            <th>Total</th>
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
                                <td>{{ $value['code'] }}</td>
                                <td>{{ $value['name'] }}</td>
                                @foreach ($data['jenis'] as $item)
                                    @php
                                        $total+= $value[$item->id];
                                        $jml_total += $value[$item->id];
                                        $jml[$item->id] += $value[$item->id];
                                    @endphp
                                    <td class="text-right">Rp{{ number_format($value[$item->id], 2, ',', '.') }}</td>
                                @endforeach
                                <th class="text-right">Rp{{ number_format($total, 2, ',', '.') }}</th>
                            </tr>
                        @endforeach
                    </tbody>
                    {{-- <tfoot>
                        <tr class="text-right">
                            <th colspan="2">Jumlah :</th>
                            @foreach ($data['jenis'] as $item)
                                <th class="text-right">Rp{{ number_format($jml[$item->id], 2, ',', '.') }}</th>
                            @endforeach
                            <th class="text-right">Rp{{ number_format($jml_total, 2, ',', '.') }}</th>
                            <th class="text-center">
                                @if (Auth::user()->hasRule('depositReportMember'))
                                    <a href="{{ route('depositReportMember', ['region_id' => 'all', 'end_date' => $data['end_date']]) }}" class="btn icon-btn btn-success btn-sm" data-toggle="tooltip" data-placement="top" data-state="dark" title="Lhat rekap anggota">
                                        <i class="fa fa-bars"></i>
                                    </a>
                                @endif
                            </th>
                        </tr>
                    </tfoot> --}}
                </table>
            </div>
            <div class="card-footer">
                <div class="row">
                    <div class="col-md-3">
                        Total Record : <strong>{{$data['data']->count() + ($data['limit']*($data['data']->currentPage() - 1))}}</strong> of <strong>{{$data['data']->total()}}</strong>
                    </div>
                    <div class="col-md-9">
                        {{ $data['data']->appends(request()->input())->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
