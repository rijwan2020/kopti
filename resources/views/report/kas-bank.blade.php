@extends('layouts.application')

@section('module', 'Laporan Harian')

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
                    <div class="input-group-prepend"><span class="input-group-text"><i class="fa fa-calendar"></i></span></div>
                    <input type="text" class="form-control datepicker" name="date" value="{{$data['date']}}">
                <span class="input-group-append">
                    <button class="btn btn-secondary" type="submit">Filter</button>
                </span>
            </div>
        </div>

        <div class="col-md text-right">
            @if (Auth::user()->hasRule('laporanKasBankPrint'))
                <a href="{{ route('laporanKasBankPrint', ['date' => $data['date']]) }}" class="btn my-1 btn-dark" data-toggle="tooltip" data-state="dark" title="Print Laporan Pemasukan/Pengeluaran Kas & Bank" target="_blank">
                    <i class="fa fa-print"></i>
                    Print
                </a>
            @endif
            @if (Auth::user()->hasRule('laporanKasBankDownload'))
                <a href="{{ route('laporanKasBankDownload', ['date' => $data['date']]) }}" class="btn my-1 btn-success" data-toggle="tooltip" data-state="dark" title="Download Laporan Pemasukan/Pengeluaran Kas & Bank">
                    <i class="fa fa-download"></i>
                    Download
                </a>
            @endif
        </div>
    </form>
</div>
<div class="card">
    <div class="card-header text-center">
        <h4 class="mb-1">Laporan Pemasukan/Pengeluaran Kas & Bank</h4>
        <h6 class="mb-1">Tanggal {{ date('d-m-Y', strtotime($data['date'])) }}</h6>
    </div>
    <table class="table card-table table-bordered">
        <thead>
            <tr class="text-center">
                <th class="text-center">#</th>
                <th>Tanggal Transaksi</th>
                <th>No Ref</th>
                <th>Kode Akun</th>
                <th>Nama Akun</th>
                <th>Keterangan</th>
                <th>Debit (Rp)</th>
                <th>Kredit (Rp)</th>
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
                    <td class="text-center">{{$i}}</td>
                    <td>{{ date('d M Y, H:i:s', strtotime($value->transaction_date)) }}</td>
                    <td>{{ $value->account->code }}</td>
                    <td>{{ $value->account->name }}</td>
                    <td>{{ $value['reference_number'] }}</td>
                    <td>{{ $value['name'] }}</td>
                    <td class="text-right">{{ number_format($value->debit, 2, ',', '.') }}</td>
                    <td class="text-right">{{ number_format($value->kredit, 2, ',', '.') }}</td>
                </tr>
            @endforeach
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

<div class="card mt-3">
    <div class="card-body">
        <div class="row text-right h5">
            <div class="col-md-8">Saldo s/d {{ date('d-m-Y', strtotime('-1 day', strtotime($data['date']))) }} (Rp):</div>
            <div class="col-md-4">{{ $data['saldo_awal'] >= 0 ? number_format($data['saldo_awal'], 2, ',', '.'):'('.number_format($data['saldo_awal']*-1, 2, ',', '.').')' }}</div>
        </div>
        <div class="row text-right h5">
            <div class="col-md-8">Total Mutasi Debit (Rp):</div>
            <div class="col-md-4">{{ number_format($data['total_debit'], 2, ',', '.') }}</div>
        </div>
        <div class="row text-right h5">
            <div class="col-md-8">Total Mutasi Kredit (Rp):</div>
            <div class="col-md-4">{{ number_format($data['total_kredit'], 2, ',', '.') }}</div>
        </div>
        @php
            $total_saldo = $data['saldo_awal'] + $data['total_debit'] - $data['total_kredit'];
        @endphp
        <div class="row text-right h5">
            <div class="col-md-8">Saldo s/d {{ date('d-m-Y', strtotime($data['date'])) }} (Rp):</div>
            <div class="col-md-4">{{ $total_saldo >= 0 ? number_format($total_saldo, 2, ',', '.'):'('.number_format($total_saldo*-1, 2, ',', '.').')' }}</div>
        </div>
    </div>
</div>
@endsection