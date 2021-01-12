@extends('layouts.application')

@section('module', 'Laporan Penjualan Anggota dan Non Anggota')

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
            <label class="form-label">Wilayah</label>
            <select class="select2 form-control" name="region_id">
                <option value="all" {{$data['region_id'] == 'all' ? 'selected' : ''}}>--Semua--</option>
                @foreach ($data['region'] as $item)
                    <option value="{{$item->id}}" {{$data['region_id'] == $item->id ? 'selected' : ''}}>{{$item->name}}</option>  
                @endforeach
            </select>
        </div>
        <div class="col-md-2 mb-3">
            <label class="form-label">Status</label>
            <select class="select2 form-control" name="status">
                <option value="1" {{$data['status'] === '1' ? 'selected' : ''}}>Anggota</option>
                <option value="0" {{$data['status'] === '0' ? 'selected' : ''}}>Non Anggota</option>
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
                    <button class="btn btn-secondary" type="submit">Filter</button>
                </span>
            </div>
        </div>

        <div class="col-md text-right">
            <a href="{{ route('storeReportMemberPrint', [$data['param']]) }}" class="btn my-1 btn-dark" data-toggle="tooltip" data-state="dark" title="Print" target="_blank">
                <i class="fa fa-print"></i>
                Print
            </a>
            <a href="{{ route('storeReportMemberDownload', [$data['param']]) }}" class="btn my-1 btn-success" data-toggle="tooltip" data-state="dark" title="Download">
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
                <h4 class="mb-0">{{ $data['title'] }}</h4>
                <h5 class="mb-0">Periode {{ date('d-m-Y', strtotime($data['start_date'])) }} s/d {{ date('d-m-Y', strtotime($data['end_date'])) }}</h5>
            </div>
            <div class="table-responsive">
                <table class="table card-table table-bordered">
                    <thead class="thead-light">
                        <tr class="text-center">
                            <th>#</th>
                            <th>Kode</th>
                            <th>Nama</th>
                            <th>Wilayah</th>
                            <th>Jatah/Bulan (Kg)</th>
                            <th>Total Penjualan (Kg)</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                            $i = ($data['data']->currentPage() - 1) * $data['data']->perPage();
                        @endphp
                        @foreach ($data['data'] as $value)
                            @php
                                $i++;
                            @endphp
                            <tr>
                                <td>{{ $i }}</td>
                                <td>{{ $value->code }}</td>
                                <td>{{ $value->name }}</td>
                                <td>{{ $value->region->name ?? '' }}</td>
                                <td class="text-right">{{ number_format($value->soybean_ration, 2, ',','.') }}</td>
                                <td class="text-right">{{ number_format($value->total_qty, 2, ',','.') }}</td>
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

@section('scripts')
    <script src="{{ asset('js/delete-data.js') }}"></script>
@endsection