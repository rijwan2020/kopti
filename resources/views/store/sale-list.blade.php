@extends('layouts.application')

@section('module', 'Data Penjualan')

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
        <div class="col-md-1 mb-3">
            <label class="form-label">Status</label>
            <select class="select2 form-control" name="status">
                <option value="all" {{ $data['status'] === 'all' ? 'selected' : '' }}>--Semua--</option>
                <option value="0" {{ $data['status'] === '0' ? 'selected' : '' }}>Hutang</option>
                <option value="1" {{ $data['status'] === '1' ? 'selected' : '' }}>Lunas</option>
            </select>
        </div>
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
            @if (Auth::user()->hasRule('saleAdd'))
                <a href="{{ route('saleAdd') }}" class="btn btn-info" data-toggle="tooltip" data-state="dark" title="Tambah data penjualan">
                    <i class="fa fa-plus"></i>
                    Tambah
                </a>
            @endif
            @if (Auth::user()->hasRule('saleDebtList'))
                <a href="{{ route('saleDebtList') }}" class="btn btn-success" data-toggle="tooltip" data-state="dark" title="Data Piutang Penjualan">
                    <i class="fa fa-bars"></i>
                    Data Piutang
                </a>
            @endif
            @if (Auth::user()->hasRule('saleReturList'))
                <a href="{{ route('saleReturList') }}" class="btn btn-success" data-toggle="tooltip" data-state="dark" title="Data Retur Penjualan">
                    <i class="fa fa-bars"></i>
                    Retur Penjualan
                </a>
            @endif
        </div>
    </form>
</div>
<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header h4 text-center">Data Penjualan Barang</div>
            <div class="table-responsive">
                <table class="table card-table">
                    <thead class="thead-light">
                        <tr>
                            <th>#</th>
                            <th>No Faktur</th>
                            <th>Pembeli</th>
                            <th>No Ref</th>
                            <th>Tanggal Transaksi</th>
                            <th>Jumlah (Rp)</th>
                            <th>Keterangan</th>
                            <th>Transaksi di</th>
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
                                <td>{{ $value->no_faktur }}</td>
                                <td>{{ $value->member->name }}</td>
                                <td>{{ $value->ref_number }}</td>
                                <td>{{ date('d M Y, H:i', strtotime($value->tanggal_jual)) }}</td>
                                <td class="text-right">
                                    {{ number_format($value->total_belanja, 2, ',', '.') }}
                                    <br>
                                    @if ($value->diskon > 0)
                                        <small>Diskon : {{ number_format($value->diskon, 2, ',', '.') }}</small>
                                    @endif
                                </td>
                                <td>{{ $value->note }}</td>
                                <td>{{ $value->warehouse_id == 0 ? 'Pusat' : $value->warehouse->name }}</td>
                                <td>{{ $value->status_pembayaran == 1 ? 'Lunas' : 'Belum Lunas' }}</td>
                                <td class="text-center">
                                    @if (Auth::user()->hasRule('saleDetail'))
                                        <a href="{{ route('saleDetail', ['id' => $value->id]) }}" class="btn icon-btn btn-success btn-sm" data-toggle="tooltip" data-placement="top" data-state="dark" title="Detail Penjualan {{ $value->no_faktur }}">
                                            <i class="fa fa-bars"></i>
                                        </a>
                                    @endif
                                    @if (Auth::user()->hasRule('salePrint'))
                                        <a href="{{ route('salePrint', ['id' => $value->id]) }}" class="btn icon-btn btn-dark btn-sm" data-toggle="tooltip" data-placement="top" data-state="dark" title="Print faktur Penjualan" target="_blank">
                                            <i class="fa fa-print"></i>
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