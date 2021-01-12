@extends('layouts.application')

@section('module', 'Retur Penjualan')

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
        @if (!auth()->user()->isGudang())
            <div class="col-md-2 mb-3">
                <label class="form-label">Gudang</label>
                <select class="select2 form-control" name="warehouse_id">
                    <option value="all" {{ $data['warehouse_id'] == 'all'?'selected' : '' }}>Semua</option>
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
    </form>
</div>
<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header h4 text-center">Data Retur Penjualan Barang</div>
            <div class="table-responsive">
                <table class="table card-table">
                    <thead class="thead-light">
                        <tr>
                            <th>#</th>
                            <th>No Faktur</th>
                            <th>Barang</th>
                            <th>Pembeli</th>
                            <th>No Ref</th>
                            <th>Tanggal Transaksi</th>
                            <th>Qty (Kg)</th>
                            <th>Harga Jual (Rp)</th>
                            <th>Total (Rp)</th>
                            <th>Keterangan</th>
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
                                <td>{{ '[' . $value->item->code . '] - '. $value->item->name }}</td>
                                <td>{{ '[' . $value->member->code . '] - '. $value->member->name }}</td>
                                <td>{{ $value->no_ref }}</td>
                                <td>{{ $value->tanggal_transaksi }}</td>
                                <td>{{ fmod($value->qty, 1) !== 0.00 ? number_format($value->qty, 2, ',', '.') : number_format($value->qty) }}</td>
                                <td class="text-right">{{ number_format($value->harga, 2, ',', '.') }}</td>
                                <td class="text-right">{{ number_format($value->jumlah, 2, ',', '.') }}</td>
                                <td>{{ $value->note }}</td>
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