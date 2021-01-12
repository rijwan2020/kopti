@extends('layouts.application')

@section('module', 'Simpanan')

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
            <label class="form-label">Jenis Simpanan</label>
            <select class="select2 form-control" name="type_id">
                <option value="all" {{ $data['type_id'] == 'all' ? 'selected' : '' }}>--Semua--</option>
                @foreach ($data['type'] as $value)
                    <option value="{{ $value->id }}" {{ $data['type_id'] == $value->id ? 'selected' : '' }}>{{ $value->name }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-2 mb-3">
            <label class="form-label">Wilayah</label>
            <select class="select2 form-control" name="region_id">
                <option value="all" {{ $data['type_id'] == 'all' ? 'selected' : '' }}>--Semua--</option>
                @foreach ($data['region'] as $value)
                    <option value="{{ $value->id }}" {{ $data['region_id'] == $value->id ? 'selected' : '' }}>{{ $value->name }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-3 mb-3">
            <label class="form-label">Filter Tanggal</label>
            <div class="input-group">
                <div class="input-group-prepend"><span class="input-group-text">From</div>
                <input type="text" class="form-control datepicker" name="start_date" value="{{$data['start_date']}}">
                <div class="input-group-prepend"><span class="input-group-text">To</div>
                <input type="text" class="form-control datepicker" name="end_date" value="{{$data['end_date']}}">
                <div class="input-group-prepend"><span class="input-group-text"><i class="fa fa-calendar"></i></span></div>
            </div>
        </div>
        <div class="col-md-2 mb-3">
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

        <div class="col-md-2 text-right">
            @if (Auth::user()->hasRule('depositTransactionDownload'))
                <a href="{{ route('depositTransactionDownload', $data['param']) }}" class="btn my-1 btn-sm btn-success" data-toggle="tooltip" data-state="dark" title="Download transaksi simpanan">
                    <i class="fa fa-download"></i>
                    Download
                </a>
            @endif
            @if (Auth::user()->hasRule('depositTransactionPrintAll'))
                <a href="{{ route('depositTransactionPrintAll', $data['param']) }}" class="btn my-1 btn-sm btn-dark" data-toggle="tooltip" data-state="dark" title="Print transaksi simpanan" target="_blank">
                    <i class="fa fa-print"></i>
                    Print
                </a>
            @endif
            @if (Auth::user()->hasRule('depositTransactionUpload'))
                <a href="{{ route('depositTransactionUpload') }}" class="btn my-1 btn-sm btn-warning" data-toggle="tooltip" data-state="dark" title="Upload transaksi simpanan">
                    <i class="fa fa-upload"></i>
                    Upload
                </a>
            @endif
        </div>
    </form>
</div>
<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header h4 text-center">Data Transaksi Simpanan</div>
            <div class="table-responsive">
                <table class="table card-table table-bordered">
                    <thead class="thead-light">
                        <tr class="text-center">
                            <th class="align-middle">#</th>
                            <th class="align-middle">No Rekening</th>
                            <th class="align-middle">Kode Anggota</th>
                            <th class="align-middle">Nama Anggota</th>
                            <th class="align-middle">Wilayah</th>
                            <th class="align-middle">Jenis Simpanan</th>
                            <th class="align-middle">No Ref</th>
                            <th class="align-middle">Keterangan</th>
                            <th class="align-middle">Tanggal Transaksi</th>
                            <th class="align-middle">Jenis Transaksi</th>
                            <th class="align-middle">Debit (Rp)</th>
                            <th class="align-middle">Kredit (Rp)</th>
                            <th class="align-middle">Action</th>
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
                                <td>{{ $value->deposit->account_number }}</td>
                                <td>{{ $value->member->name }}</td>
                                <td>{{ $value->member->code }}</td>
                                <td>{{ $value->region->name }}</td>
                                <td>{{ $value->depositType->name }}</td>
                                <td>{{ $value->reference_number }}</td>
                                <td>{{ $value->note }}</td>
                                <td>{{ $value->transaction_date }}</td>
                                <td>[{{ str_pad($value->type, 2, 0, STR_PAD_LEFT) }}] - {{ $data['type_transaction'][$value->type] }}</td>
                                <td class="text-right">{{ number_format($value->debit, 2, ',', '.') }}</td>
                                <td class="text-right">{{ number_format($value->kredit, 2, ',', '.') }}</td>
                                <td class="text-center">
                                    @if (Auth::user()->hasRule('depositTransactionPrint'))
                                        <a href="{{ route('depositTransactionPrint', ['id' => $value->id]) }}" class="btn btn-sm icon-btn btn-dark" data-toggle="tooltip" data-state="dark" title="Print transaksi {{ $value->reference_number }}" target="_blank">
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