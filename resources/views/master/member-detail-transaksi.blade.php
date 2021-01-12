@extends('layouts.application')

@section('module', 'Data Anggota')

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
            <div class="card-header h4 text-center">Data Transaksi {{ $data['member']->name }}</div>
            <div class="table-responsive">
                <table class="table card-table">
                    <thead class="thead-light">
                        <tr>
                            <th>#</th>
                            <th>Tanggal Transaksi</th>
                            <th>No Ref</th>
                            <th>Nama Transaksi</th>
                            <th>Jumlah (Rp)</th>
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
                                <td>{{ $value->transaction_date }}</td>
                                <td>{{ $value->reference_number }}</td>
                                <td>{{ $value->name }}</td>
                                <td class="text-right">{{ number_format($value->detail->sum('debit'), 2, ',', '.') }}</td>
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
