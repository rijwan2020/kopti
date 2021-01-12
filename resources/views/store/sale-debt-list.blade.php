@extends('layouts.application')

@section('module', 'Data Piutang Penjualan')

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
            <label class="form-label">Status</label>
            <select class="select2 form-control" name="status">
                <option value="all" {{ $data['status'] === 'all' ? 'selected' : '' }}>--Semua--</option>
                <option value="1" {{ $data['status'] === '1' ? 'selected' : '' }}>Lunas</option>
                <option value="0" {{ $data['status'] === '0' ? 'selected' : '' }}>Belum Lunas</option>
            </select>
        </div>
        <div class="col-md-3 mb-3">
            <label class="form-label">Pencarian</label>
            <div class="input-group">
                <input type="text" class="form-control" placeholder="Kata Kunci" name="q" value="{{ $data['q'] }}">
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
    </form>
</div>
<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header h4 text-center">Data Piutang Penjualan</div>
            <div class="table-responsive">
                <table class="table card-table">
                    <thead class="thead-light">
                        <tr>
                            <th>#</th>
                            <th>No Faktur</th>
                            <th>Pembeli</th>
                            <th>Tanggal Transaksi</th>
                            <th>Total Utang (Rp)</th>
                            <th>Total Bayar (Rp)</th>
                            <th>Sisa Utang (Rp)</th>
                            <th>Jatuh Tempo</th>
                            <th>Status</th>
                            <th class="text-center">Action</th>
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
                                <td>{{ $value->sale->no_faktur }}</td>
                                <td>{{ $value->member->name }}</td>
                                <td>{{ $value->tanggal_transaksi }}</td>
                                <td class="text-right">{{ number_format($value->total, 2, ',', '.') }}</td>
                                <td class="text-right">{{ number_format($value->pay, 2, ',', '.') }}</td>
                                <td class="text-right">{{ number_format($value->total - $value->pay, 2, ',', '.') }}</td>
                                <td>{{ $value->due_date }}</td>
                                <td>{{ $value->status == 0? 'Belum Lunas' : 'Lunas' }}</td>
                                <td class="text-center">
                                    @if (Auth::user()->hasRule('saleDebtDetail'))
                                        <a href="{{ route('saleDebtDetail', ['id' => $value->id]) }}" class="btn icon-btn btn-info btn-sm" data-toggle="tooltip" data-placement="top" data-state="dark" title="Detail">
                                            <i class="fa fa-bars"></i>
                                        </a>
                                    @endif
                                    @if ($value->status == 0 && Auth::user()->hasRule('saleDebtPay'))
                                        <a href="{{ route('saleDebtPay', ['id' => $value->id]) }}" class="btn icon-btn btn-success btn-sm" data-toggle="tooltip" data-placement="top" data-state="dark" title="Bayar">
                                            <i class="fa fa-dollar-sign"></i>
                                        </a>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="card-footer">
                <div class="row">
                    <div class="col-md-3">
                        Total Record : <strong>{{ $data['data']->count() + ($data['limit']*($data['data']->currentPage() - 1)) }}</strong> of <strong>{{$data['data']->total()}}</strong>
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