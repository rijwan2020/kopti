@extends('layouts.application')

@section('module', 'Simpanan')

@section('content')
<div class="row mb-3">
    <div class="col-md-12">
        <div class="card">
            <div class="card-body">
                <div class="row">
                    <div class="col-md-4">
                        <div class="d-flex justify-content-between mb-0">
                            <div>No Rekening</div>
                            <b> {{ $data['deposit']->account_number }}</b>
                        </div>
                        <div class="d-flex justify-content-between mb-0">
                            <div>Kode Anggota</div>
                            <b> {{ $data['deposit']->member->code }}</b>
                        </div>
                        <div class="d-flex justify-content-between mb-0">
                            <div>Nama Anggota</div>
                            <b> {{ $data['deposit']->member->name }}</b>
                        </div>
                        <div class="d-flex justify-content-between mb-0">
                            <div>Jenis Simpanan</div>
                            <b> {{ $data['deposit']->type->name }}</b>
                        </div>
                        <div class="d-flex justify-content-between mb-0">
                            <div>Wilayah</div>
                            <b> {{ $data['deposit']->region->name }}</b>
                        </div>
                    </div>
                    <div class="col-md-8 text-right">
                        <h2 class="mb-1">Saldo: Rp{{ number_format($data['deposit']->balance, 2, ',', '.') }}</h2>
                        <h5 class="mb-1">Total Kredit: Rp{{ number_format($data['total_kredit'], 2, ',', '.') }} | Total Debit: Rp{{ number_format($data['total_debit'], 2, ',', '.') }}</h5>
                        <div>
                            @if (Auth::user()->hasRule('depositDetailAdd'))
                                <a href="{{ route('depositDetailAdd', ['id' => $data['deposit']->id]) }}" class="btn my-1 btn-primary" data-toggle="tooltip" data-state="dark" title="Tambah Transaksi">
                                    <i class="fa fa-plus"></i>
                                    Transaksi
                                </a>
                            @endif
                            @if (Auth::user()->hasRule('depositDetailPrint'))
                                <a href="{{ route('depositDetailPrint', ['id' => $data['deposit']->id, 'q' => $data['q'], 'start_date' => $data['start_date'], 'end_date' => $data['end_date']]) }}" class="btn my-1 btn-dark" data-toggle="tooltip" data-state="dark" title="Print Semua Transaksi" target="_blank">
                                    <i class="fa fa-print"></i>
                                    Print
                                </a>
                            @endif
                            @if (Auth::user()->hasRule('depositDetailDownload'))
                                <a href="{{ route('depositDetailDownload', ['id' => $data['deposit']->id, 'q' => $data['q'], 'start_date' => $data['start_date'], 'end_date' => $data['end_date']]) }}" class="btn my-1 btn-success" data-toggle="tooltip" data-state="dark" title="Download Transaksi">
                                    <i class="fa fa-download"></i>
                                    Download
                                </a>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header h4 text-center">Histori Transaksi</div>
            <div class="card-body">
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
                    <div class="col-md-5 mb-3">
                        <label class="form-label">Filter Tanggal</label>
                        <div class="input-group">
                            <div class="input-group-prepend"><span class="input-group-text">From</div>
                            <input type="text" class="form-control datepicker" name="start_date" value="{{$data['start_date']}}">
                            <div class="input-group-prepend"><span class="input-group-text">To</div>
                            <input type="text" class="form-control datepicker" name="end_date" value="{{$data['end_date']}}">
                            <div class="input-group-prepend"><span class="input-group-text"><i class="fa fa-calendar"></i></span></div>
                        </div>
                    </div>
                    <div class="col-md-4 mb-3">
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
            <div class="table-responsive">
                <table class="table card-table">
                    <thead class="thead-light">
                        <tr>
                            <th>#</th>
                            <th>No Ref</th>
                            <th>Keterangan</th>
                            <th>Tanggal Transaksi</th>
                            <th>Jenis Transaksi</th>
                            <th>Kredit (Rp)</th>
                            <th>Debit (Rp)</th>
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
                                <td>{{ $value->reference_number }}</td>
                                <td>{{ $value->note }}</td>
                                <td>{{ $value->transaction_date }}</td>
                                <td>[{{ str_pad($value->type, 2, 0, STR_PAD_LEFT) }}] - {{ $data['type_transaction'][$value->type] }}</td>
                                <td class="text-right">{{ number_format($value->kredit, 2, ',', '.') }}</td>
                                <td class="text-right">{{ number_format($value->debit, 2, ',', '.') }}</td>
                                <td class="text-center">
                                    @if (Auth::user()->hasRule('depositTransactionPrint'))
                                        <a href="{{ route('depositTransactionPrint', ['id' => $value->id]) }}" class="btn btn-sm icon-btn btn-dark" data-toggle="tooltip" data-state="dark" title="Print transaksi" target="_blank">
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