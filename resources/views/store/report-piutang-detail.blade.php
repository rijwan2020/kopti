@extends('layouts.application')

@section('module', 'Laporan Rekapitulasi Piutang')

@section('content')
<div class="ui-bordered px-3 pt-3 mb-3">
    <form class="form-row align-items-center" method="get" action="{{ url()->current() }}">
        <div class="col-md-1 mb-3">
            <label class="form-label">Limit</label>
            <select class="select2 form-control" name="limit">
                <option value="25" {{$data['limit'] == 25?'selected':''}}>25</option>
                <option value="50" {{$data['limit'] == 50?'selected':''}}>50</option>
                <option value="100" {{$data['limit'] == 100?'selected':''}}>100</option>
                <option value="150" {{$data['limit'] == 150?'selected':''}}>150</option>
                <option value="200" {{$data['limit'] == 200?'selected':''}}>200</option>
            </select>
        </div>
        <div class="col-md-2 mb-3">
            <label class="form-label">Keanggotaan</label>
            <select class="select2 form-control" name="status">
                <option value="1" {{$data['status'] === '1'?'selected':''}}>Anggota</option>
                <option value="0" {{$data['status'] === '0'?'selected':''}}>Non Anggota</option>
            </select>
        </div>
        <div class="col-md-3 mb-3">
            <label class="form-label">Wilayah</label>
            <select class="select2 form-control" name="region_id">
                @foreach ($data['regionlist'] as $value)
                <option value="{{ $value->id }}" {{$data['region_id'] == $value->id?'selected':''}}>{{ $value->name }}</option>
                @endforeach
            </select>
        </div>
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

        <div class="col-md-2 text-right">
            @if (Auth::user()->hasRule('storeReportPiutangAdd'))
                <a href="{{ route('storeReportPiutangAdd') }}" class="btn my-1 btn-info" data-toggle="tooltip" data-state="dark" title="Tambah">
                    <i class="fa fa-plus"></i>
                    Tambah
                </a>
            @endif
            @if (Auth::user()->hasRule('storeReportPiutangDetailPrint'))
                <a href="{{ route('storeReportPiutangDetailPrint', ['region_id' => $data['region_id'],'start_date' => $data['start_date'], 'end_date' => $data['end_date'], 'status' => $data['status']]) }}" class="btn my-1 btn-dark" data-toggle="tooltip" data-state="dark" title="Print" target="_blank">
                    <i class="fa fa-print"></i>
                    Print
                </a>
            @endif
            @if (Auth::user()->hasRule('storeReportPiutangDetailDownload'))
                <a href="{{ route('storeReportPiutangDetailDownload', ['region_id' => $data['region_id'],'start_date' => $data['start_date'], 'end_date' => $data['end_date'], 'status' => $data['status']]) }}" class="btn my-1 btn-success" data-toggle="tooltip" data-state="dark" title="Download">
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
            <div class="card-body h4">Rp{{ number_format($data['saldo_awal'], 2, ',', '.') }}</div>
        </div>
    </div>
    <div class="col-md-3 mb-3">
        <div class="card text-center">
            <div class="card-header h5">Total Penambahan</div>
            <div class="card-body h4">Rp{{ number_format($data['penambahan'], 2, ',', '.') }}</div>
        </div>
    </div>
    <div class="col-md-3 mb-3">
        <div class="card text-center">
            <div class="card-header h5">Total Pengurangan</div>
            <div class="card-body h4">Rp{{ number_format($data['pengurangan'], 2, ',', '.') }}</div>
        </div>
    </div>
    <div class="col-md-3 mb-3">
        <div class="card text-center">
            <div class="card-header h5">Total Piutang</div>
            <div class="card-body h4">Rp{{ number_format($data['saldo_akhir'], 2, ',', '.') }}</div>
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
            <div class="card-body">
                Keanggotaan : {{ $data['status'] ? 'Anggota' : 'Non Anggota' }} <br>
                Wilayah : {{ $data['region']->name }}
            </div>
            <div class="table-responsive">
                <table class="table card-table table-bordered">
                    <thead class="thead-light">
                        <tr class="text-center">
                            <th>#</th>
                            <th>Kode Anggota</th>
                            <th>Nama Anggota</th>
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
                                @if (Auth::user()->hasRule('storeReportPiutangDetailAnggota'))
                                    <td class="text-center">
                                        <a href="{{ route('storeReportPiutangDetailAnggota', ['id' => $value->id, 'start_date' => $data['start_date'], 'end_date' => $data['end_date']]) }}" class="btn icon-btn btn-dark btn-sm" data-toggle="tooltip" data-placement="top" data-state="dark" title="Detail">
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