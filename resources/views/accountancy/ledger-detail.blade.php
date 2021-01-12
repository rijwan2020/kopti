@extends('layouts.application')

@section('module', 'Buku Besar')

@section('content')
<div class="ui-bordered px-3 py-2 mb-3">
    <form class="form-row align-items-center" method="get" action="{{ url()->current() }}">
        @if (isset($data['tbb_id']))
            <input type="hidden" name="tbb_id" value="{{ $data['tbb_id'] }}">
        @endif
        @if (isset($data['tbt_id']))
            <input type="hidden" name="tbt_id" value="{{ $data['tbt_id'] }}">
        @endif
        <input type="hidden" name="type" value="{{$data['type']}}">
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
            <label class="form-label">Pilih Akun</label>
            <select class="select2 form-control" name="id">
                @foreach ($data['listakun'] as $value)
                    <option value="{{ $value->id }}" {{$data['id'] == $value->id?'selected':''}}>[{{ $value->code }}] - {{ $value->name }}</option>
                @endforeach
            </select>
        </div>

        <div class="col-md-2 mb-3">
            <label class="form-label">View Data</label>
            <select class="select2 form-control" name="type">
                <option value="1" {{$data['type'] == 1?'selected':''}}>Data Sudah Disesuaikan</option>
                <option value="0" {{$data['type'] == 0?'selected':''}}>Data Belum Disesuaikan</option>
            </select>
        </div>

        <div class="col-md-4 mb-3">
            <label class="form-label">Filter Tanggal</label>
            <div class="input-group">
                <div class="input-group-prepend"><span class="input-group-text">From</div>
                <input type="text" class="form-control {{ !isset($data['tbb_id']) && !isset($data['tbt_id']) ? 'datepicker' : '' }}" name="start_date" value="{{$data['start_date']}}" {{ !isset($data['tbb_id']) && !isset($data['tbt_id']) ? '' : 'readonly' }}>
                <div class="input-group-prepend"><span class="input-group-text">To</div>
                <input type="text" class="form-control {{ !isset($data['tbb_id']) && !isset($data['tbt_id']) ? 'datepicker' : '' }}" name="end_date" value="{{$data['end_date']}}" {{ !isset($data['tbb_id']) && !isset($data['tbt_id']) ? '' : 'readonly' }}>
                <span class="input-group-append">
                    <button class="btn btn-secondary" type="submit">Filter</button>
                </span>
            </div>
        </div>

        <div class="col-md-3 text-right">
            @if (Auth::user()->hasRule('ledgerDetailPrint'))
                <a href="{{ route('ledgerDetailPrint', $data['param']) }}" class="btn btn-dark mb-3" data-toggle="tooltip" data-state="dark" data-placement="bottom" data-original-title="Print Buku Besar {{$data['account']->name}}" target="_blank">
                    <i class="fa fa-print"></i>
                    Print
                </a>   
            @endif
            @if (Auth::user()->hasRule('ledgerDetailDownload'))
                <a href="{{ route('ledgerDetailDownload', $data['param']) }}" class="btn btn-success mb-3" data-toggle="tooltip" data-state="dark" data-placement="bottom" data-original-title="Download Buku Besar {{$data['account']->name}}">
                    <i class="fa fa-download"></i>
                    Download
                </a>   
            @endif
        </div>
    </form>
</div>
<div class="card">
    <div class="card-header text-center">
        <h4 class="mb-1">Buku Besar</h4>
        <h6 class="mb-1">{{date('d M Y', strtotime($data['start_date']))}} s/d {{date('d M Y', strtotime($data['end_date']))}}</h6>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-1"><b>Kode Akun</b></div>
            <div class="col-md-11">: <b>{{$data['account']->code}}</b></div>
            <div class="col-md-1"><b>Nama Akun</b></div>
            <div class="col-md-11">: <b>{{$data['account']->name}}</b></div>
            <div class="col-md-12 md-2">{!! $data['type']==0?'Data <b>BELUM</b> disesuaikan':'Data <b>SUDAH</b> disesuaikan' !!}</b></div>
        </div>
    </div>
    <table class="table card-table table-bordered">
        <thead class="thead-light">
            <tr class="text-center">
                <th>#</th>
                <th>Tangga Transaksi</th>
                <th>No Ref</th>
                <th>Keterangan</th>
                <th>Debit (Rp)</th>
                <th>Kredit (Rp)</th>
                <th>Saldo (Rp)</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td class="text-right" colspan="6"><b>Saldo Awal</b></td>
                <td class="text-right"><b>{{ $data['beginning_balance'] >= 0 ?number_format($data['beginning_balance'], 2, ',', '.'):'('.number_format($data['beginning_balance']*-1, 2, ',', '.').')' }}</b></td>
            </tr>
            @php
                $i = ($data['data']->currentPage() - 1) * $data['data']->perPage();
                $data['balance'] = $data['beginning_balance'];
            @endphp
            @foreach ($data['data'] as $value)
                @php
                    $i++;
                    if ($data['account']->type == 0) {
                        $data['balance'] += $value->debit;
                        $data['balance'] -= $value->kredit;
                    }else{
                        $data['balance'] -= $value->debit;
                        $data['balance'] += $value->kredit;
                    }
                @endphp
                <tr>
                    <td class="text-center">{{$i}}</td>
                    <td>{{ date('d M Y, H:i:s', strtotime($value->transaction_date)) }}</td>
                    <td>{{ $value['reference_number'] }}</td>
                    <td>{{ $value['name'] }}</td>
                    <td class="text-right">{{ number_format($value->debit, 2, ',', '.') }}</td>
                    <td class="text-right">{{ number_format($value->kredit, 2, ',', '.') }}</td>
                    <td class="text-right">{{ $data['balance'] >= 0 ?number_format($data['balance'], 2, ',', '.'):'('.number_format(($data['balance']*-1), 2, ',', '.').')' }}</td>
                </tr>
            @endforeach
            <tr>
                <td colspan="6" class="text-right"><b>Saldo Akhir</b></td>
                <td class="text-right"><b>{{ $data['balance'] >= 0 ?number_format($data['balance'], 2, ',', '.'):'('.number_format($data['balance']*-1, 2, ',', '.').')' }}</b></td>
            </tr>
        </tbody>
    </table>
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
@endsection