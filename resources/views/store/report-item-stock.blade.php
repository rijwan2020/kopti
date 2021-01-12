@extends('layouts.application')

@section('module', 'Laporan Persediaan Barang')

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

        <div class="col-md-4 mb-3">
            <label class="form-label">Filter Tanggal</label>
            <div class="input-group">
                <div class="input-group-prepend"><span class="input-group-text">From</div>
                <input type="text" class="form-control datepicker" name="start_date" value="{{$data['start_date']}}">
                <div class="input-group-prepend"><span class="input-group-text">To</div>
                <input type="text" class="form-control datepicker" name="end_date" value="{{$data['end_date']}}">
                <div class="input-group-prepend"><span class="input-group-text"><i class="fa fa-calendar"></i></span></div>
            </div>
        </div>
        @if (!auth()->user()->isGudang())
            <div class="col-md-2 mb-3">
                <label class="form-label">Persediaan</label>
                <select class="select2 form-control" name="warehouse_id">
                    <option value="0" {{ $data['warehouse_id'] == '0'?'selected' : '' }}>Pusat</option>
                    @foreach ($data['warehouse'] as $value)
                        <option value="{{ $value->id }}" {{ $data['warehouse_id'] == $value->id ? 'selected' : '' }}>{{ $value->name }}</option>
                    @endforeach
                </select>
            </div>
        @endif
        
        <div class="col-md-3 mb-3">
            <label class="form-label">Pencarian</label>
            <div class="input-group">
                <input type="text" class="form-control" placeholder="Kata Kunci" name="q" value="{{$data['q']}}">
                <span class="input-group-append">
                    <button class="btn btn-secondary" type="submit">Cari</button>
                </span>
                @if (!empty($data['q']))
                    <span class="input-group-append">
                        <a class="btn btn-danger" href="{{ url()->current() }}"><i class="fa fa-times"></i></a>
                    </span>
                @endif
            </div>
        </div>

        <div class="col-md text-right">
            <a href="{{ route('storeReportItemStockPrint', [$data['param']]) }}" class="btn my-1 btn-dark" data-toggle="tooltip" data-state="dark" title="Print" target="_blank">
                <i class="fa fa-print"></i>
                Print
            </a>
            <a href="{{ route('storeReportItemStockDownload', [$data['param']]) }}" class="btn my-1 btn-success" data-toggle="tooltip" data-state="dark" title="Download">
                <i class="fa fa-download"></i>
                Download
            </a>
        </div>
    </form>
</div>
<div class="row">
    <div class="col-md-3 mb-3">
        <div class="card text-center">
            <div class="card-header h5">Stok s/d {{ date('d-m-Y', strtotime('-1 day', strtotime($data['start_date']))) }}</div>
            <div class="card-body h4">{{ $data['total_saldo_awal'] }} Kg</div>
        </div>
    </div>
    <div class="col-md-3 mb-3">
        <div class="card text-center">
            <div class="card-header h5">Total Stok Masuk</div>
            <div class="card-body h4">{{ number_format($data['total_masuk'], 2, ',', '.') }} Kg</div>
        </div>
    </div>
    <div class="col-md-3 mb-3">
        <div class="card text-center">
            <div class="card-header h5">Total Stok Keluar</div>
            <div class="card-body h4">{{ number_format($data['total_keluar'], 2, ',', '.') }} Kg</div>
        </div>
    </div>
    <div class="col-md-3 mb-3">
        <div class="card text-center">
            <div class="card-header h5">Total Stok Akhir</div>
            <div class="card-body h4">{{ number_format($data['total_persediaan'], 2, ',', '.') }} Kg</div>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header text-center">
                <h4 class="mb-0">Laporan Persediaan Barang {{ $data['gudang']->name ?? 'Pusat' }}</h4>
                <h5 class="mb-0">Periode {{ date('d-m-Y', strtotime($data['start_date'])) }} s/d {{ date('d-m-Y', strtotime($data['end_date'])) }}</h5>
            </div>
            <div class="table-responsive">
                <table class="table card-table table-bordered">
                    <thead class="thead-light">
                        <tr class="text-center">
                            <th>#</th>
                            <th>Kode Barang</th>
                            <th>Nama Barang</th>
                            <th>Stok s/d {{ date('d-m-Y', strtotime('-1 day', strtotime($data['start_date']))) }} (Kg)</th>
                            <th>Masuk (Kg)</th>
                            <th>Keluar (Kg)</th>
                            <th>Stok Akhir (Kg)</th>
                            @if (Auth::user()->hasRule('itemCard'))
                                <th>Aksi</th>
                            @endif
                        </tr>
                    </thead>
                    <tbody>
                        @php
                            $i = ($data['data']->currentPage() - 1) * $data['data']->perPage();
                        @endphp
                        @foreach ($data['data'] as $value)
                            @php
                                $i++;
                                $saldo = $value->saldo_awal + $value->masuk - $value->keluar;
                            @endphp
                            <tr>
                                <td>{{ $i }}</td>
                                <td>{{ $value->code }}</td>
                                <td>{{ $value->name }}</td>
                                <td class="text-right">{{ number_format($value->saldo_awal, 2, ',', '.')}}</td>
                                <td class="text-right">{{ number_format($value->masuk, 2, ',', '.')}}</td>
                                <td class="text-right">{{ number_format($value->keluar, 2, ',', '.')}}</td>
                                <td class="text-right">{{ number_format($saldo, 2, ',', '.')}}</td>
                                @if (Auth::user()->hasRule('itemCard'))
                                    <td class="text-center">
                                        <a href="{{ route('itemCard', ['id' => $value->id, 'start_date' => $data['start_date'], 'end_date' => $data['end_date'], 'warehouse_id' => $data['warehouse_id']]) }}" class="btn icon-btn btn-dark btn-sm" data-toggle="tooltip" data-placement="top" data-state="dark" title="Kartu Persediaan">
                                            <i class="fa fa-clipboard-list"></i>
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